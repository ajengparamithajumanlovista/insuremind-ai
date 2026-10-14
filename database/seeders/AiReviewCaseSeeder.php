<?php

namespace Database\Seeders;

use App\Models\AiReviewAction;
use App\Models\AiReviewCase;
use App\Models\ChatMessage;
use App\Models\PolicyDocument;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Kasus AI Quality Review contoh beserta riwayat tindakannya.
 *
 * Mencakup kasus yang berasal dari jawaban chat (tanpa referensi, feedback
 * negatif) maupun dari analisis dokumen yang gagal. Variasi status dibuat
 * agar antrean review menampilkan pending, in_review, needs_fix, dan done.
 * Setiap keputusan reviewer disertai baris pada `ai_review_actions`.
 */
class AiReviewCaseSeeder extends Seeder
{
    public function run(): void
    {
        $reviewer = User::query()->where('email', 'reviewer@insuremind.ai')->first();
        $admin = User::query()->where('email', 'admin@insuremind.ai')->first();

        foreach ($this->cases() as $case) {
            $user = User::query()->where('email', $case['email'])->first();

            if (! $user) {
                continue;
            }

            $attributes = [
                'user_id' => $user->getKey(),
                'chat_message_id' => $this->chatMessageId($case),
                'policy_document_id' => $this->documentId($case),
                'category' => $case['category'] ?? null,
                'issue_type' => $case['issue_type'],
                'issues' => $case['issues'] ?? null,
                'question' => $case['question'] ?? null,
                'answer' => $case['answer'] ?? null,
                'references' => $case['references'] ?? null,
                'status' => $case['status'],
                'has_reference' => $case['has_reference'] ?? false,
                'negative_feedback_count' => $case['negative_feedback_count'] ?? 0,
                'reviewed_by' => $this->reviewerId($case, $reviewer, $admin),
                'reviewed_at' => $case['reviewed_at'] ?? null,
                'review_decision' => $case['review_decision'] ?? null,
                'review_note' => $case['review_note'] ?? null,
            ];

            $model = AiReviewCase::updateOrCreate(
                [
                    'user_id' => $user->getKey(),
                    'issue_type' => $case['issue_type'],
                    'status' => $case['status'],
                    'question' => $case['question'] ?? null,
                ],
                $attributes,
            );

            foreach ($case['actions'] ?? [] as $action) {
                AiReviewAction::updateOrCreate(
                    [
                        'ai_review_case_id' => $model->getKey(),
                        'action' => $action['action'],
                        'to_status' => $action['to_status'],
                    ],
                    [
                        'user_id' => $this->actorId($action, $reviewer, $admin),
                        'from_status' => $action['from_status'] ?? null,
                        'note' => $action['note'] ?? null,
                    ],
                );
            }
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function cases(): array
    {
        return [
            $this->missingReferenceCase(),
            $this->negativeFeedbackCase(),
            $this->failedAnalysisCase(),
        ];
    }

    /**
     * Jawaban tanpa referensi yang dapat diverifikasi (masih menunggu).
     *
     * @return array<string, mixed>
     */
    private function missingReferenceCase(): array
    {
        return [
            'email' => 'user@insuremind.ai',
            'chat_message_excerpt' => 'mempertimbangkan asuransi jiwa karena penghasilan Anda',
            'category' => 'life',
            'issue_type' => 'missing_reference',
            'issues' => ['missing_reference'],
            'question' => 'Apakah saya perlu asuransi jiwa?',
            'answer' => 'Ya, sebaiknya Anda mempertimbangkan asuransi jiwa karena penghasilan Anda menopang keluarga.',
            'status' => 'pending',
            'has_reference' => false,
            'negative_feedback_count' => 0,
        ];
    }

    /**
     * Jawaban dengan feedback negatif yang sudah ditinjau (needs_fix).
     *
     * @return array<string, mixed>
     */
    private function negativeFeedbackCase(): array
    {
        return [
            'email' => 'budi@insuremind.ai',
            'category' => 'health',
            'issue_type' => 'negative_feedback',
            'issues' => ['negative_feedback', 'missing_reference'],
            'question' => 'Apakah polis kesehatan saya sudah cukup?',
            'answer' => 'Polis kesehatan Anda sepertinya sudah cukup untuk kebutuhan saat ini.',
            'references' => null,
            'status' => 'needs_fix',
            'has_reference' => false,
            'negative_feedback_count' => 1,
            'reviewed_at' => now()->subDays(1),
            'review_decision' => 'needs_fix',
            'review_note' => 'Jawaban terlalu umum; perlu menambahkan rujukan batas tahunan polis.',
            'actions' => [
                ['action' => 'claim', 'from_status' => 'pending', 'to_status' => 'in_review', 'note' => 'Mulai meninjau.'],
                ['action' => 'needs_fix', 'from_status' => 'in_review', 'to_status' => 'needs_fix', 'note' => 'Jawaban terlalu umum; perlu menambahkan rujukan batas tahunan polis.'],
            ],
        ];
    }

    /**
     * Kasus dari analisis dokumen yang gagal.
     *
     * @return array<string, mixed>
     */
    private function failedAnalysisCase(): array
    {
        return [
            'email' => 'budi@insuremind.ai',
            'document_name' => 'scan-polis-buram.jpg',
            'category' => 'work',
            'issue_type' => 'analysis_failed',
            'issues' => ['analysis_failed'],
            'question' => 'Analisis dokumen scan-polis-buram.jpg',
            'answer' => 'Teks tidak dapat diekstraksi: kualitas pindai terlalu rendah.',
            'status' => 'done',
            'has_reference' => false,
            'negative_feedback_count' => 0,
            'reviewed_at' => now()->subHours(18),
            'review_decision' => 'done',
            'review_note' => 'Dikonfirmasi gagal; pengguna diminta mengunggah ulang dengan kualitas lebih baik.',
            'actions' => [
                ['action' => 'claim', 'from_status' => 'pending', 'to_status' => 'in_review', 'note' => 'Mulai meninjau.'],
                ['action' => 'done', 'from_status' => 'in_review', 'to_status' => 'done', 'note' => 'Dikonfirmasi gagal; minta unggah ulang.', 'by' => 'admin'],
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $case
     */
    private function chatMessageId(array $case): ?int
    {
        if (empty($case['chat_message_excerpt'])) {
            return null;
        }

        $user = User::query()->where('email', $case['email'])->first();

        return ChatMessage::query()
            ->where('user_id', $user?->getKey())
            ->where('role', 'assistant')
            ->where('body_id', 'like', '%'.$case['chat_message_excerpt'].'%')
            ->value('id');
    }

    /**
     * @param  array<string, mixed>  $case
     */
    private function documentId(array $case): ?int
    {
        if (empty($case['document_name'])) {
            return null;
        }

        $user = User::query()->where('email', $case['email'])->first();

        return PolicyDocument::query()
            ->where('user_id', $user?->getKey())
            ->where('original_name', $case['document_name'])
            ->value('id');
    }

    /**
     * @param  array<string, mixed>  $case
     */
    private function reviewerId(array $case, ?User $reviewer, ?User $admin): ?int
    {
        if (empty($case['status']) || ! in_array($case['status'], ['valid', 'needs_fix', 'escalated', 'done', 'in_review'], true)) {
            return null;
        }

        return $reviewer?->getKey() ?? $admin?->getKey();
    }

    /**
     * @param  array<string, mixed>  $action
     */
    private function actorId(array $action, ?User $reviewer, ?User $admin): ?int
    {
        return ($action['by'] ?? 'reviewer') === 'admin'
            ? $admin?->getKey()
            : $reviewer?->getKey();
    }
}
