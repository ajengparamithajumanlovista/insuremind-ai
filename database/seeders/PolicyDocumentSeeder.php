<?php

namespace Database\Seeders;

use App\Models\PolicyDocument;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Dokumen polis contoh beserta jejak pemrosesannya.
 *
 * Mencakup tiga status agar halaman "Dokumen Polis" admin menampilkan
 * variasi lengkap: `analysed` (selesai), `review` (menunggu tinjauan), dan
 * `failed` (gagal) — status gagal tidak pernah ditampilkan sebagai sukses.
 */
class PolicyDocumentSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->documents() as $document) {
            $user = User::query()->where('email', $document['email'])->first();

            if (! $user) {
                continue;
            }

            // Hindari duplikasi saat seeder dijalankan ulang.
            if ($user->policyDocuments()->where('original_name', $document['original_name'])->exists()) {
                continue;
            }

            $model = $user->policyDocuments()->create([
                'original_name' => $document['original_name'],
                'disk' => 'local',
                'path' => 'demo/'.$document['original_name'],
                'mime_type' => $document['mime_type'],
                'size_bytes' => $document['size_bytes'],
                'category' => $document['category'],
                'status' => $document['status'],
                'extraction_status' => $document['extraction_status'],
                'analysis_status' => $document['analysis_status'],
                'summary' => $document['summary'] ?? null,
                'references' => $document['references'] ?? null,
                'failure_reason' => $document['failure_reason'] ?? null,
                'processed_at' => $document['processed_at'] ?? null,
                'review_requested_at' => $document['review_requested_at'] ?? null,
                'created_at' => $document['created_at'],
                'updated_at' => $document['created_at'],
            ]);

            foreach ($document['events'] as $index => $event) {
                $at = Carbon::parse($document['created_at'])->addMinutes($index * 2);

                $model->events()->create([
                    'stage' => $event['stage'],
                    'status' => $event['status'],
                    'message' => $event['message'] ?? null,
                    'meta' => $event['meta'] ?? null,
                    'created_at' => $at,
                    'updated_at' => $at,
                ]);
            }
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function documents(): array
    {
        return [
            $this->analysedDocument(),
            $this->reviewDocument(),
            $this->failedDocument(),
        ];
    }

    /**
     * Dokumen yang selesai dianalisis penuh.
     *
     * @return array<string, mixed>
     */
    private function analysedDocument(): array
    {
        return [
            'email' => 'user@insuremind.ai',
            'original_name' => 'polis-sehatku-2024.pdf',
            'mime_type' => 'application/pdf',
            'size_bytes' => 842_000,
            'category' => 'health',
            'status' => 'analysed',
            'extraction_status' => 'completed',
            'analysis_status' => 'completed',
            'summary' => [
                'provider' => 'SehatKu',
                'product' => 'Rawatinap Plus',
                'coverage_limit' => 'Rp 500.000.000',
                'waiting_period' => '30 hari',
                'highlights' => ['Rawat inap', 'Rawat jalan kanker', 'Cuci darah'],
            ],
            'references' => [
                ['page' => 1, 'label' => 'Identitas pemegang polis'],
                ['page' => 3, 'label' => 'Manfaat dan batas pertanggungan'],
                ['page' => 7, 'label' => 'Pengecualian'],
            ],
            'processed_at' => now()->subDays(6),
            'created_at' => now()->subDays(7),
            'events' => [
                ['stage' => 'upload', 'status' => 'succeeded', 'message' => 'Berkas diterima.'],
                ['stage' => 'validation', 'status' => 'succeeded', 'message' => 'Tipe dan ukuran berkas valid.'],
                ['stage' => 'extraction', 'status' => 'succeeded', 'message' => 'Teks berhasil diekstraksi dari 8 halaman.'],
                ['stage' => 'analysis', 'status' => 'succeeded', 'message' => 'Analisis AI selesai.'],
                ['stage' => 'summary', 'status' => 'succeeded', 'message' => 'Ringkasan dibuat.'],
            ],
        ];
    }

    /**
     * Dokumen yang menunggu tinjauan manual.
     *
     * @return array<string, mixed>
     */
    private function reviewDocument(): array
    {
        return [
            'email' => 'user@insuremind.ai',
            'original_name' => 'polis-amanah-2023.pdf',
            'mime_type' => 'application/pdf',
            'size_bytes' => 1_204_000,
            'category' => 'life',
            'status' => 'review',
            'extraction_status' => 'completed',
            'analysis_status' => 'completed',
            'summary' => [
                'provider' => 'Amanah Medika',
                'product' => 'Keluarga Sejahtera',
                'coverage_limit' => 'Rp 750.000.000',
                'highlights' => ['Penggantian penghasilan', 'Manfaat meninggal'],
            ],
            'references' => [
                ['page' => 2, 'label' => 'Ringkasan polis'],
            ],
            'processed_at' => now()->subDays(2),
            'review_requested_at' => now()->subDays(1),
            'created_at' => now()->subDays(3),
            'events' => [
                ['stage' => 'upload', 'status' => 'succeeded', 'message' => 'Berkas diterima.'],
                ['stage' => 'validation', 'status' => 'succeeded', 'message' => 'Berkas lolos validasi.'],
                ['stage' => 'extraction', 'status' => 'succeeded', 'message' => 'Ekstraksi selesai.'],
                ['stage' => 'analysis', 'status' => 'succeeded', 'message' => 'Analisis selesai, namun rujukan pasal perlu diverifikasi.'],
                ['stage' => 'review', 'status' => 'started', 'message' => 'Dokumen diajukan untuk tinjauan manual.'],
            ],
        ];
    }

    /**
     * Dokumen yang gagal diproses.
     *
     * @return array<string, mixed>
     */
    private function failedDocument(): array
    {
        return [
            'email' => 'budi@insuremind.ai',
            'original_name' => 'scan-polis-buram.jpg',
            'mime_type' => 'image/jpeg',
            'size_bytes' => 3_560_000,
            'category' => 'work',
            'status' => 'failed',
            'extraction_status' => 'failed',
            'analysis_status' => 'pending',
            'failure_reason' => 'Teks tidak dapat diekstraksi: kualitas pindai terlalu rendah.',
            'processed_at' => now()->subHours(20),
            'created_at' => now()->subHours(21),
            'events' => [
                ['stage' => 'upload', 'status' => 'succeeded', 'message' => 'Berkas diterima.'],
                ['stage' => 'validation', 'status' => 'succeeded', 'message' => 'Berkas lolos validasi.'],
                ['stage' => 'extraction', 'status' => 'failed', 'message' => 'Ekstraksi gagal: gambar tidak terbaca.'],
            ],
        ];
    }
}
