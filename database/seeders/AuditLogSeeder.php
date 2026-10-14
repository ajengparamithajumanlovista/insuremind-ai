<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\User;
use App\Support\AuditCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Contoh baris Audit Log untuk seluruh kategori.
 *
 * Tabel ini append-only sehingga baris tidak diperbarui: seeder hanya
 * menambahkan contoh log bila datanya belum ada (dicek lewat jumlah baris),
 * agar riwayat audit tidak terduplikasi saat dijalankan ulang.
 *
 * Kolom `before`/`after` hanya berisi metadata aman; tidak ada kata sandi,
 * token, maupun isi polis lengkap yang disimpan di sini.
 */
class AuditLogSeeder extends Seeder
{
    public function run(): void
    {
        // Audit Log bersifat append-only; jangan menambah ulang bila sudah ada.
        if (AuditLog::query()->exists()) {
            return;
        }

        foreach ($this->entries() as $entry) {
            $actor = User::query()->where('email', $entry['actor'])->first();

            AuditLog::create([
                'event_id' => (string) Str::uuid(),
                'user_id' => $actor?->getKey(),
                'actor_role' => $actor?->role ?? 'system',
                'actor_name' => $actor?->name,
                'category' => $entry['category'],
                'action' => $entry['action'],
                'description' => $entry['description'],
                'subject_type' => $entry['subject_type'] ?? null,
                'subject_id' => $entry['subject_id'] ?? null,
                'subject_label' => $entry['subject_label'] ?? null,
                'result' => $entry['result'] ?? 'success',
                'before' => $entry['before'] ?? null,
                'after' => $entry['after'] ?? null,
                'reason' => $entry['reason'] ?? null,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Seeder/1.0',
                'request_id' => (string) Str::uuid(),
                'created_at' => $entry['created_at'] ?? now(),
            ]);
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function entries(): array
    {
        return [
            [
                'actor' => 'admin@insuremind.ai',
                'category' => AuditCategory::AUTHENTICATION,
                'action' => 'auth.login',
                'description' => 'Admin masuk ke aplikasi.',
                'created_at' => now()->subHours(5),
            ],
            [
                'actor' => 'admin@insuremind.ai',
                'category' => AuditCategory::USER_MANAGEMENT,
                'action' => 'user.created',
                'description' => 'Menambahkan akun pengguna baru.',
                'subject_type' => User::class,
                'subject_id' => 4,
                'subject_label' => 'Andi Wijaya',
                'after' => ['role' => 'user', 'status' => 'active'],
                'created_at' => now()->subDays(4),
            ],
            [
                'actor' => 'admin@insuremind.ai',
                'category' => AuditCategory::INSURANCE_PRODUCT,
                'action' => 'product.updated',
                'description' => 'Memperbarui premi bulanan produk SehatKu Rawatinap Plus.',
                'subject_type' => 'App\\Models\\InsuranceProduct',
                'subject_id' => 1,
                'subject_label' => 'Rawatinap Plus',
                'before' => ['monthly_premium' => 350000],
                'after' => ['monthly_premium' => 385000],
                'reason' => 'Penyesuaian premi oleh penyedia.',
                'created_at' => now()->subDays(3),
            ],
            [
                'actor' => 'admin@insuremind.ai',
                'category' => AuditCategory::POLICY_DOCUMENT,
                'action' => 'document.downloaded',
                'description' => 'Mengunduh dokumen polis-sehatku-2024.pdf.',
                'subject_type' => 'App\\Models\\PolicyDocument',
                'subject_id' => 1,
                'subject_label' => 'polis-sehatku-2024.pdf',
                'created_at' => now()->subDays(2),
            ],
            [
                'actor' => 'reviewer@insuremind.ai',
                'category' => AuditCategory::AI_QUALITY_REVIEW,
                'action' => 'review.needs_fix',
                'description' => 'Kasus #2 ditandai needs_fix.',
                'subject_type' => 'App\\Models\\AiReviewCase',
                'subject_id' => 2,
                'subject_label' => 'Kasus #2',
                'before' => ['status' => 'in_review'],
                'after' => ['status' => 'needs_fix'],
                'reason' => 'Jawaban terlalu umum; perlu rujukan.',
                'created_at' => now()->subDay(),
            ],
            [
                'actor' => 'admin@insuremind.ai',
                'category' => AuditCategory::AI_ANALYSIS,
                'action' => 'analysis.completed',
                'description' => 'Analisis dokumen polis-sehatku-2024.pdf selesai.',
                'subject_type' => 'App\\Models\\PolicyDocument',
                'subject_id' => 1,
                'subject_label' => 'polis-sehatku-2024.pdf',
                'created_at' => now()->subHours(30),
            ],
            [
                'actor' => 'admin@insuremind.ai',
                'category' => AuditCategory::SECURITY,
                'action' => 'security.login_failed',
                'description' => 'Percobaan masuk gagal untuk akun rudi@insuremind.ai.',
                'result' => 'failed',
                'reason' => 'Kata sandi salah.',
                'created_at' => now()->subHours(20),
            ],
            [
                'actor' => 'user@insuremind.ai',
                'category' => AuditCategory::CHAT,
                'action' => 'chat.message_sent',
                'description' => 'Pengguna mengirim pesan ke asisten AI.',
                'created_at' => now()->subHours(10),
            ],
            [
                'actor' => 'admin@insuremind.ai',
                'category' => AuditCategory::AUDIT,
                'action' => 'audit.exported',
                'description' => 'Mengekspor Audit Log untuk periode berjalan.',
                'created_at' => now()->subHours(6),
            ],
        ];
    }
}
