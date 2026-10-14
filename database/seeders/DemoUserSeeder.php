<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Akun contoh untuk mencoba seluruh fitur aplikasi.
 *
 * Seeder ini melengkapi akun inti pada DatabaseSeeder dengan beberapa
 * pengguna biasa yang mewakili variasi status akun (aktif, nonaktif,
 * ditangguhkan). Data dibuat idempoten (`updateOrCreate` berdasarkan email)
 * sehingga aman dijalankan berulang kali.
 */
class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->users() as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => Hash::make('password'),
                    'role' => $user['role'],
                    'status' => $user['status'],
                    'phone' => $user['phone'],
                    'last_activity_at' => $user['last_activity_at'],
                    'admin_note' => $user['admin_note'] ?? null,
                ]
            );
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function users(): array
    {
        return [
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@insuremind.ai',
                'role' => 'user',
                'status' => 'active',
                'phone' => '0812-3456-7890',
                'last_activity_at' => now()->subHours(3),
            ],
            [
                'name' => 'Siti Rahmawati',
                'email' => 'siti@insuremind.ai',
                'role' => 'user',
                'status' => 'active',
                'phone' => '0813-9876-5432',
                'last_activity_at' => now()->subDay(),
            ],
            [
                'name' => 'Andi Wijaya',
                'email' => 'andi@insuremind.ai',
                'role' => 'user',
                'status' => 'active',
                'phone' => '0857-1122-3344',
                'last_activity_at' => now()->subDays(6),
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'dewi@insuremind.ai',
                'role' => 'user',
                'status' => 'inactive',
                'phone' => '0821-5566-7788',
                'last_activity_at' => now()->subMonths(2),
                'admin_note' => 'Menonaktifkan akun atas permintaan sendiri; riwayat tetap tersimpan.',
            ],
            [
                'name' => 'Rudi Hermawan',
                'email' => 'rudi@insuremind.ai',
                'role' => 'user',
                'status' => 'suspended',
                'phone' => '0878-2233-4455',
                'last_activity_at' => now()->subDays(20),
                'admin_note' => 'Ditangguhkan sementara menunggu verifikasi identitas.',
            ],
        ];
    }
}
