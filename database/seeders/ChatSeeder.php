<?php

namespace Database\Seeders;

use App\Models\ChatFeedback;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Percakapan contoh dengan chatbot beserta umpan baliknya.
 *
 * Dibuat dalam format yang sama dengan ChatController: setiap giliran berisi
 * pesan pengguna (role "user") diikuti jawaban asisten (role "assistant").
 * Sebagian jawaban menyertakan `meta.references` sehingga
 * ChatMessage::hasVerifiableReference() bernilai benar, dan sebagian lagi
 * sengaja tanpa referensi untuk memicu kasus AI Quality Review.
 */
class ChatSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->threads() as $email => $thread) {
            $user = User::query()->where('email', $email)->first();

            if (! $user) {
                continue;
            }

            // Hindari duplikasi saat seeder dijalankan ulang.
            if ($user->chatMessages()->exists()) {
                continue;
            }

            foreach ($thread as $turn) {
                $user->chatMessages()->create([
                    'role' => 'user',
                    'body_id' => $turn['q_id'],
                    'body_en' => $turn['q_en'],
                    'meta' => ['engine' => 'user'],
                ]);

                $assistantMessage = $user->chatMessages()->create([
                    'role' => 'assistant',
                    'body_id' => $turn['a_id'],
                    'body_en' => $turn['a_en'],
                    'meta' => $turn['meta'],
                ]);

                if (! empty($turn['feedback'])) {
                    ChatFeedback::create([
                        'chat_message_id' => $assistantMessage->getKey(),
                        'user_id' => $user->getKey(),
                        'sentiment' => $turn['feedback']['sentiment'],
                        'reason' => $turn['feedback']['reason'] ?? null,
                        'comment' => $turn['feedback']['comment'] ?? null,
                    ]);
                }
            }
        }
    }

    /**
     * @return array<string, array<int, array<string, mixed>>>
     */
    private function threads(): array
    {
        return [
            'user@insuremind.ai' => $this->highNeedThread(),
            'budi@insuremind.ai' => $this->mediumThread(),
        ];
    }

    /**
     * Percakapan untuk profil dengan kebutuhan proteksi tinggi.
     *
     * @return array<int, array<string, mixed>>
     */
    private function highNeedThread(): array
    {
        return [
            [
                'q_id' => 'Bagaimana skor risiko saya?',
                'q_en' => 'How is my risk score?',
                'a_id' => "Skor kebutuhan perlindungan Anda berada pada tingkat tinggi. Penyebab utamanya: dana darurat belum memadai, 3 tanggungan keluarga, dan Anda belum memiliki asuransi kesehatan maupun jiwa.\n\nSumber: Perhitungan analisis risiko atas profil Anda.",
                'a_en' => "Your protection-need score is high. Main drivers: an insufficient emergency fund, 3 family dependents, and no health or life insurance yet.\n\nSource: Risk analysis over your profile.",
                'meta' => [
                    'engine' => 'rule-based',
                    'intent' => 'risk',
                    'references' => [
                        ['label' => 'Profil pengguna', 'type' => 'profile'],
                    ],
                ],
            ],
            [
                'q_id' => 'Berapa dana darurat yang saya butuhkan?',
                'q_en' => 'How much emergency fund do I need?',
                'a_id' => "Dengan pengeluaran dan cicilan sekitar Rp 19.500.000 per bulan, dana darurat ideal 3-6 bulan berada di kisaran Rp 58.500.000 sampai Rp 117.000.000. Tabungan Anda saat ini Rp 20.000.000, sehingga masih ada selisih yang perlu ditutup.",
                'a_en' => "With monthly expenses and instalments of about IDR 19,500,000, an ideal 3-6 month emergency fund is roughly IDR 58,500,000 to 117,000,000. Your current savings of IDR 20,000,000 still leave a gap to close.",
                'meta' => [
                    'engine' => 'rule-based',
                    'intent' => 'emergency_fund',
                    'references' => [
                        ['label' => 'Profil pengguna', 'type' => 'profile'],
                    ],
                ],
            ],
            [
                // Jawaban sengaja tanpa referensi untuk memicu kasus review.
                'q_id' => 'Apakah saya perlu asuransi jiwa?',
                'q_en' => 'Do I need life insurance?',
                'a_id' => 'Ya, sebaiknya Anda mempertimbangkan asuransi jiwa karena penghasilan Anda menopang keluarga.',
                'a_en' => 'Yes, you should consider life insurance because your income supports a family.',
                'meta' => [
                    'engine' => 'rule-based',
                    'intent' => 'life',
                ],
                'feedback' => [
                    'sentiment' => 'down',
                    'reason' => 'missing_reference',
                    'comment' => 'Jawaban belum menyertakan rujukan perhitungan.',
                ],
            ],
        ];
    }

    /**
     * Percakapan untuk profil dengan kebutuhan proteksi sedang.
     *
     * @return array<int, array<string, mixed>>
     */
    private function mediumThread(): array
    {
        return [
            [
                'q_id' => 'Kapan jatuh tempo polis saya?',
                'q_en' => 'When are my policy due dates?',
                'a_id' => "Berikut pengingat terdekat:\n- Rawat Jalan Basic: jatuh tempo dalam 9 hari (due_soon)\n- Kecelakaan Kerja: jatuh tempo dalam 21 hari (upcoming)",
                'a_en' => "Here are your nearest reminders:\n- Rawat Jalan Basic: due in 9 days (due_soon)\n- Kecelakaan Kerja: due in 21 days (upcoming)",
                'meta' => [
                    'engine' => 'rule-based',
                    'intent' => 'reminder',
                    'references' => [
                        ['label' => 'Daftar polis aktif', 'type' => 'policy'],
                    ],
                ],
                'feedback' => [
                    'sentiment' => 'up',
                ],
            ],
            [
                'q_id' => 'Berapa anggaran premi yang wajar?',
                'q_en' => 'What is a reasonable premium budget?',
                'a_id' => "Sebagai panduan, total premi yang sehat umumnya 5-10% dari penghasilan bulanan. Dengan penghasilan Rp 18.000.000, kisaran wajar adalah Rp 900.000 sampai Rp 1.800.000 per bulan.",
                'a_en' => "As a guide, a healthy total premium is usually 5-10% of monthly income. With an income of IDR 18,000,000, a reasonable range is IDR 900,000 to 1,800,000 per month.",
                'meta' => [
                    'engine' => 'rule-based',
                    'intent' => 'budget',
                    'references' => [
                        ['label' => 'Profil pengguna', 'type' => 'profile'],
                    ],
                ],
            ],
        ];
    }
}
