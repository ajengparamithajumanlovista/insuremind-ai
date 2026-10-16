<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserProfile;
use App\Support\Money;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Asisten chat InsureMind.
 *
 * Primarily uses OpenAI when an API key is available; otherwise (or if a call
 * fails), it automatically falls back to a rule-based answer engine that
 * builds responses from profile data and application calculations. This keeps
 * chatbot responses relevant and auditable.
 */
class ChatAssistantService
{
    public function __construct(
        private readonly RiskAssessmentService $risk,
    ) {}

    /**
     * Compose a reply to the user's message.
     *
     * @param  array<int, array{role: string, content: string}>  $history
     * @return array{body_id: string, body_en: string, meta: array<string, mixed>}
     */
    public function reply(User $user, string $message, array $history = []): array
    {
        $profile = $user->profile;
        $context = $this->context($user, $profile);

        if ($this->hasApiKey()) {
            try {
                return $this->askOpenAi($message, $history, $context);
            } catch (Throwable $e) {
                Log::warning('ChatAssistantService: OpenAI gagal, memakai rule-based.', [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $this->ruleBased($message, $context);
    }

    /**
     * Whether the OpenAI integration is available.
     */
    public function hasApiKey(): bool
    {
        return filled(config('services.openai.key'));
    }

    /**
     * User context summary for the prompt and rule-based answers.
     *
     * @return array<string, mixed>
     */
    private function context(User $user, ?UserProfile $profile): array
    {
        $data = [
            'name' => $user->name,
            'income' => 0.0,
            'expense' => 0.0,
            'debt' => 0.0,
            'savings' => 0.0,
            'dependents' => 0,
            'surplus' => 0.0,
            'emergency_months' => 0.0,
            'has_health' => false,
            'has_life' => false,
            'has_work' => false,
            'ready' => false,
            'risk' => null,
            'policies_due' => [],
        ];

        if (! $profile) {
            return $data;
        }

        $data['income'] = (float) $profile->monthly_income;
        $data['expense'] = (float) $profile->monthly_expense;
        $data['debt'] = (float) $profile->monthly_debt;
        $data['savings'] = (float) $profile->savings;
        $data['dependents'] = (int) $profile->dependents;
        $data['surplus'] = $profile->monthlySurplus();
        $data['emergency_months'] = $profile->emergencyFundMonths();
        $data['has_health'] = (bool) $profile->has_health_insurance;
        $data['has_life'] = (bool) $profile->has_life_insurance;
        $data['has_work'] = (bool) $profile->has_work_insurance;
        $data['ready'] = $profile->isReady();

        if ($data['ready']) {
            $data['risk'] = $this->risk->assess($profile);
        }

        $data['policies_due'] = $user->policies()
            ->where('is_active', true)
            ->whereNotNull('next_due_date')
            ->orderBy('next_due_date')
            ->take(3)
            ->get()
            ->map(fn ($policy) => [
                'name' => $policy->policy_name ?: $policy->provider_name,
                'due' => optional($policy->next_due_date)->format('d M Y'),
                'status' => $policy->reminderStatus(),
                'days' => $policy->daysUntilDue(),
                'premium' => Money::idr($policy->premium_amount),
            ])
            ->all();

        return $data;
    }

    /**
     * Call OpenAI Chat Completions with the user context summary.
     *
     * @param  array<int, array{role: string, content: string}>  $history
     * @param  array<string, mixed>  $context
     * @return array{body_id: string, body_en: string, meta: array<string, mixed>}
     */
    private function askOpenAi(string $message, array $history, array $context): array
    {
        $messages = array_merge(
            [['role' => 'system', 'content' => $this->systemPrompt($context)]],
            array_slice($history, -10),
            [['role' => 'user', 'content' => $message]],
        );

        $response = Http::withToken((string) config('services.openai.key'))
            ->timeout((int) config('services.openai.timeout'))
            ->acceptJson()
            ->post(rtrim((string) config('services.openai.base_url'), '/').'/chat/completions', [
                'model' => (string) config('services.openai.model'),
                'messages' => $messages,
                'temperature' => 0.4,
                'max_tokens' => 600,
            ]);

        if ($response->failed()) {
            throw new \RuntimeException('OpenAI HTTP '.$response->status());
        }

        $answer = trim((string) data_get($response->json(), 'choices.0.message.content', ''));

        if ($answer === '') {
            throw new \RuntimeException('OpenAI mengembalikan balasan kosong.');
        }

        return [
            'body_id' => $answer,
            'body_en' => $answer,
            'meta' => ['engine' => 'openai', 'model' => config('services.openai.model')],
        ];
    }

    /**
     * System prompt: limit the topic and include the latest user data.
     *
     * @param  array<string, mixed>  $context
     */
    private function systemPrompt(array $context): string
    {
        $risk = $context['risk'];
        $summary = implode("\n", array_filter([
            'Nama: '.$context['name'],
            'Penghasilan bulanan: '.Money::idr($context['income']),
            'Pengeluaran bulanan: '.Money::idr($context['expense']),
            'Cicilan bulanan: '.Money::idr($context['debt']),
            'Tabungan: '.Money::idr($context['savings']),
            'Surplus bulanan: '.Money::idr($context['surplus']),
            'Tanggungan: '.$context['dependents'],
            'Dana darurat: '.$context['emergency_months'].' bulan',
            'Asuransi kesehatan: '.($context['has_health'] ? 'ada' : 'belum ada'),
            'Asuransi jiwa: '.($context['has_life'] ? 'ada' : 'belum ada'),
            'Asuransi kerja: '.($context['has_work'] ? 'ada' : 'belum ada'),
            $risk ? 'Skor risiko: '.$risk['score'].'/100 ('.$risk['level'].')' : null,
        ]));

        return <<<PROMPT
Kamu adalah InsureMind AI, asisten perencana asuransi untuk pengguna Indonesia.
Jawablah dengan ringkas (maksimal 160 kata) dan dalam bahasa yang sama dengan pertanyaan pengguna
(jika pengguna menulis dalam bahasa Indonesia, jawab dalam bahasa Indonesia; jika bahasa Inggris, jawab dalam bahasa Inggris).
Gunakan data pengguna berikut bila relevan:

{$summary}

Aturan:
- Jangan mengarang angka. Bila data belum ada, katakan pengguna perlu melengkapi profil.
- Bedakan dengan jelas antara fakta, estimasi, dan saran.
- Ingatkan bahwa ini bukan nasihat keuangan resmi dan keputusan akhir ada di tangan pengguna.
- Fokus pada topik asuransi, proteksi keluarga, dana darurat, dan perencanaan premi.
PROMPT;
    }

    /**
     * Rule-based answer engine (fallback when OpenAI is unavailable).
     *
     * @param  array<string, mixed>  $context
     * @return array{body_id: string, body_en: string, meta: array<string, mixed>}
     */
    private function ruleBased(string $message, array $context): array
    {
        $intent = $this->detectIntent($message);
        $pair = match ($intent) {
            'profile' => $this->answerProfile($context),
            'risk' => $this->answerRisk($context),
            'emergency' => $this->answerEmergency($context),
            'premium' => $this->answerPremium($context),
            'life' => $this->answerLife($context),
            'health' => $this->answerHealth($context),
            'reminder' => $this->answerReminder($context),
            'compare' => $this->answerCompare($context),
            'greeting' => $this->answerGreeting($context),
            default => $this->answerDefault($message, $context),
        };

        return [
            'body_id' => $pair[0],
            'body_en' => $pair[1],
            'meta' => ['engine' => 'rule-based', 'intent' => $intent],
        ];
    }

    /**
     * Determine the user's intent from message keywords.
     */
    private function detectIntent(string $message): string
    {
        $text = mb_strtolower($message);

        $keywords = [
            'greeting' => ['halo', 'hai', 'hello', 'hi ', 'pagi', 'siang', 'sore', 'malam'],
            'profile' => ['profil', 'profile', 'data saya', 'my data', 'penghasilan', 'income', 'surplus'],
            'risk' => ['risiko', 'risk', 'skor', 'score', 'analisis', 'assessment'],
            'emergency' => ['dana darurat', 'emergency', 'tabungan', 'saving', 'cadangan'],
            'premium' => ['premi', 'premium', 'biaya', 'budget', 'anggaran', 'bayar'],
            'life' => ['jiwa', 'life', 'meninggal', 'death', 'warisan', 'pewaris'],
            'health' => ['kesehatan', 'health', 'rawat', 'rumah sakit', 'hospital', 'medis', 'medical'],
            'reminder' => ['jatuh tempo', 'due', 'pengingat', 'reminder', 'tagihan', 'bayar polis'],
            'compare' => ['banding', 'compare', 'perbandingan', 'produk', 'pilih', 'mana yang'],
        ];

        foreach ($keywords as $intent => $needles) {
            foreach ($needles as $needle) {
                if (str_contains($text, $needle)) {
                    return $intent;
                }
            }
        }

        return 'unknown';
    }

    /**
     * @param  array<string, mixed>  $context
     * @return array{0: string, 1: string}
     */
    private function answerGreeting(array $context): array
    {
        $name = $context['name'];

        return [
            "Halo {$name}! Saya InsureMind AI. Saya dapat membantu menilai risiko proteksi, menghitung dana darurat, memperkirakan anggaran premi, dan menjelaskan perbandingan produk. Apa yang ingin Anda ketahui?",
            "Hi {$name}! I'm InsureMind AI. I can help assess your protection risk, estimate an emergency fund, plan a premium budget, and explain product comparisons. What would you like to know?",
        ];
    }

    /**
     * @param  array<string, mixed>  $context
     * @return array{0: string, 1: string}
     */
    private function answerProfile(array $context): array
    {
        if (! $context['ready']) {
            return [
                'Profil Anda belum lengkap. Isi dulu usia, penghasilan, pengeluaran, tabungan, dan jumlah tanggungan di halaman Profil agar saya dapat mengitung dengan akurat.',
                'Your profile is incomplete. Please fill in your age, income, expenses, savings, and dependents on the Profile page so I can calculate accurately.',
            ];
        }

        return [
            "Ringkasan profil Anda: penghasilan {$this->rp($context['income'])}/bulan, pengeluaran {$this->rp($context['expense'])}, cicilan {$this->rp($context['debt'])}. Surplus bulanan sekitar {$this->rp($context['surplus'])} dengan {$context['dependents']} tanggungan dan tabungan {$this->rp($context['savings'])}.",
            "Profile summary: income {$this->rp($context['income'])}/month, expenses {$this->rp($context['expense'])}, debt {$this->rp($context['debt'])}. Monthly surplus is about {$this->rp($context['surplus'])} with {$context['dependents']} dependent(s) and savings of {$this->rp($context['savings'])}.",
        ];
    }

    /**
     * @param  array<string, mixed>  $context
     * @return array{0: string, 1: string}
     */
    private function answerRisk(array $context): array
    {
        $risk = $context['risk'];

        if (! $risk) {
            return [
                'Saya belum bisa menilai risiko karena profil Anda belum lengkap. Lengkapi dulu data di halaman Profil, lalu buka Analisis Risiko.',
                'I cannot assess your risk yet because your profile is incomplete. Complete your Profile first, then open Risk Analysis.',
            ];
        }

        $label = match ($risk['level']) {
            'high' => 'tinggi',
            'medium' => 'sedang',
            default => 'rendah',
        };
        $labelEn = match ($risk['level']) {
            'high' => 'high',
            'medium' => 'medium',
            default => 'low',
        };
        $top = collect($risk['suggestions'])->take(2)->pluck('id')->implode(' ');
        $topEn = collect($risk['suggestions'])->take(2)->pluck('en')->implode(' ');

        return [
            "Skor risiko proteksi Anda {$risk['score']}/100 (kategori {$label}). Saran utama: {$top}",
            "Your protection risk score is {$risk['score']}/100 (category {$labelEn}). Key advice: {$topEn}",
        ];
    }

    /**
     * @param  array<string, mixed>  $context
     * @return array{0: string, 1: string}
     */
    private function answerEmergency(array $context): array
    {
        $months = $context['emergency_months'];
        $target = ($context['expense'] + $context['debt']) * 6;
        $gap = max(0, $target - $context['savings']);

        if ($context['expense'] <= 0 && $context['savings'] <= 0) {
            return [
                'Isi dulu penghasilan, pengeluaran, dan tabungan di halaman Profil agar saya dapat menghitung kebutuhan dana darurat Anda.',
                'Please fill in your income, expenses, and savings on the Profile page so I can calculate your emergency fund need.',
            ];
        }

        $advice = $gap > 0
            ? ($context['surplus'] > 0
                ? 'Anda perlu menambah sekitar '.$this->rp($gap).'. Bila menabung '.$this->rp($context['surplus']).' per bulan, target tercapai dalam sekitar '.max(1, (int) ceil($gap / $context['surplus'])).' bulan.'
                : 'Anda belum punya surplus bulanan, jadi prioritaskan menekan pengeluaran agar bisa mulai menabung.')
            : 'Dana darurat Anda sudah memenuhi target 6 bulan pengeluaran.';

        $adviceEn = $gap > 0
            ? ($context['surplus'] > 0
                ? 'You need to add about '.$this->rp($gap).'. Saving '.$this->rp($context['surplus']).' a month gets you there in about '.max(1, (int) ceil($gap / $context['surplus'])).' months.'
                : 'You have no monthly surplus yet, so focus on trimming expenses before starting to save.')
            : 'Your emergency fund already meets the 6-month expense target.';

        return [
            "Dana darurat Anda saat ini menutup sekitar {$months} bulan pengeluaran. Target ideal 6 bulan adalah {$this->rp($target)}. {$advice}",
            "Your emergency fund currently covers about {$months} months of expenses. The ideal 6-month target is {$this->rp($target)}. {$adviceEn}",
        ];
    }

    /**
     * @param  array<string, mixed>  $context
     * @return array{0: string, 1: string}
     */
    private function answerPremium(array $context): array
    {
        $surplus = $context['surplus'];
        $budget = $surplus * 0.2;

        if ($surplus <= 0) {
            return [
                'Surplus bulanan Anda belum tersedia atau nol, sehingga belum ada ruang untuk premi tambahan. Lengkapi profil atau kurangi pengeluaran terlebih dahulu.',
                'Your monthly surplus is not available or at zero, so there is no room for extra premiums yet. Complete your profile or reduce expenses first.',
            ];
        }

        return [
            "Sebagai panduan, alokasikan maksimal 20% surplus bulanan untuk premi, yaitu sekitar {$this->rp($budget)}/bulan. Dengan dana itu, utamakan perlindungan dasar: kesehatan dan jiwa bila Anda punya tanggungan.",
            "As a guideline, allocate at most 20% of your monthly surplus to premiums, roughly {$this->rp($budget)}/month. With that budget, prioritise basic protection: health, plus life cover if you have dependents.",
        ];
    }

    /**
     * @param  array<string, mixed>  $context
     * @return array{0: string, 1: string}
     */
    private function answerLife(array $context): array
    {
        $need = $context['income'] * 12 * ($context['dependents'] > 0 ? 10 : 5);

        if ($context['income'] <= 0) {
            return [
                'Untuk menghitung kebutuhan asuransi jiwa, isi dulu penghasilan bulanan dan jumlah tanggungan di halaman Profil.',
                'To estimate your life insurance need, please fill in your monthly income and dependents on the Profile page.',
            ];
        }

        $status = $context['has_life']
            ? 'Anda sudah memiliki asuransi jiwa; pastikan uang pertanggungannya mendekati estimasi kebutuhan.'
            : 'Anda belum memiliki asuransi jiwa.';
        $statusEn = $context['has_life']
            ? 'You already have life insurance; make sure the coverage is close to the estimated need.'
            : 'You do not have life insurance yet.';

        return [
            "Estimasi kebutuhan asuransi jiwa Anda sekitar {$this->rp($need)} ({$context['dependents']} tanggungan, penggantian ".($context['dependents'] > 0 ? '10' : '5')." tahun penghasilan). {$status}",
            "Your estimated life insurance need is about {$this->rp($need)} ({$context['dependents']} dependent(s), ".($context['dependents'] > 0 ? '10' : '5')." years of income replacement). {$statusEn}",
        ];
    }

    /**
     * @param  array<string, mixed>  $context
     * @return array{0: string, 1: string}
     */
    private function answerHealth(array $context): array
    {
        $status = $context['has_health']
            ? 'Anda sudah memiliki asuransi kesehatan. Periksa batas tahunan, daftar rumah sakit rekanan, dan ketentuan pengecualiannya.'
            : 'Anda belum memiliki asuransi kesehatan. Ini prioritas utama karena biaya rawat inap dapat menguras tabungan.';

        $statusEn = $context['has_health']
            ? 'You already have health insurance. Review the annual limit, the network hospitals, and the exclusions.'
            : 'You do not have health insurance yet. This is the top priority as hospitalisation costs can drain savings.';

        return [
            "{$status} Sebagai gambaran, premi kesehatan bulanan umumnya berkisar 1–3% dari penghasilan bulanan Anda.",
            "{$statusEn} As a guide, a monthly health premium usually ranges from about 1–3% of your monthly income.",
        ];
    }

    /**
     * @param  array<string, mixed>  $context
     * @return array{0: string, 1: string}
     */
    private function answerReminder(array $context): array
    {
        $due = $context['policies_due'];

        if ($due === []) {
            return [
                'Belum ada polis aktif dengan tanggal jatuh tempo. Tambahkan polis di halaman Polis & Pengingat agar saya dapat mengingatkan Anda.',
                'There are no active policies with a due date yet. Add a policy on the Policies & Reminders page so I can remind you.',
            ];
        }

        $lines = collect($due)
            ->map(fn (array $p) => "- {$p['name']}: jatuh tempo {$p['due']} ({$p['status']})")
            ->implode("\n");
        $linesEn = collect($due)
            ->map(fn (array $p) => "- {$p['name']}: due {$p['due']} ({$p['status']})")
            ->implode("\n");

        return [
            "Berikut pengingat terdekat:\n{$lines}",
            "Here are your nearest reminders:\n{$linesEn}",
        ];
    }

    /**
     * @param  array<string, mixed>  $context
     * @return array{0: string, 1: string}
     */
    private function answerCompare(array $context): array
    {
        return [
            'Untuk membandingkan produk, buka halaman Perbandingan. Bandingkan premi bulanan, uang pertanggungan, masa tunggu, dan rasio manfaat terhadap premi. Pastikan juga produk sesuai dengan kebutuhan kategori Anda (kesehatan atau jiwa).',
            'To compare products, open the Comparison page. Compare the monthly premium, coverage limit, waiting period, and benefit-to-premium ratio. Also check that the product matches your category needs (health or life).',
        ];
    }

    /**
     * @param  array<string, mixed>  $context
     * @return array{0: string, 1: string}
     */
    private function answerDefault(string $message, array $context): array
    {
        return [
            'Saya belum menangkap pertanyaan itu. Saya dapat membantu dengan: skor risiko proteksi, kebutuhan dana darurat, anggaran premi, kebutuhan asuransi jiwa/kesehatan, perbandingan produk, dan pengingat jatuh tempo polis.',
            'I did not quite catch that. I can help with: your protection risk score, emergency fund needs, premium budgeting, life/health insurance needs, product comparison, and policy due reminders.',
        ];
    }

    /**
     * Compact Rupiah format for chat messages.
     */
    private function rp(float|int $value): string
    {
        return Money::idr($value);
    }
}
