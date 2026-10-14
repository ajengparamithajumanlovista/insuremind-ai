<?php

namespace Database\Seeders;

use App\Models\Policy;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Polis contoh beserta tanggal jatuh tempo yang bervariasi.
 *
 * Variasi tanggal sengaja dibuat agar halaman "Polis & Pengingat" menampilkan
 * ketiga status pengingat sekaligus: overdue, due_soon, dan upcoming.
 */
class PolicySeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->policies() as $email => $policies) {
            $user = User::query()->where('email', $email)->first();

            if (! $user) {
                continue;
            }

            foreach ($policies as $policy) {
                Policy::updateOrCreate(
                    [
                        'user_id' => $user->getKey(),
                        'policy_number' => $policy['policy_number'],
                    ],
                    $policy,
                );
            }
        }
    }

    /**
     * @return array<string, array<int, array<string, mixed>>>
     */
    private function policies(): array
    {
        return [
            'user@insuremind.ai' => [
                [
                    'provider_name' => 'SehatKu',
                    'policy_name' => 'Rawatinap Plus',
                    'policy_number' => 'SK-2024-000123',
                    'category' => 'health',
                    'premium_amount' => 385_000,
                    'coverage_amount' => 500_000_000,
                    'payment_period' => 'monthly',
                    'start_date' => now()->subYears(2)->startOfDay(),
                    // Sudah lewat → status pengingat "overdue".
                    'next_due_date' => now()->subDays(5)->startOfDay(),
                    'reminder_days' => 14,
                    'notes' => 'Polis kesehatan keluarga utama.',
                    'is_active' => true,
                ],
                [
                    'provider_name' => 'Amanah Medika',
                    'policy_name' => 'Keluarga Sejahtera',
                    'policy_number' => 'AM-2023-445566',
                    'category' => 'life',
                    'premium_amount' => 1_950_000,
                    'coverage_amount' => 750_000_000,
                    'payment_period' => 'annual',
                    'start_date' => now()->subYears(3)->startOfDay(),
                    // Jatuh tempo dalam 4 hari → "due_soon".
                    'next_due_date' => now()->addDays(4)->startOfDay(),
                    'reminder_days' => 14,
                    'notes' => 'Asuransi jiwa untuk penggantian penghasilan.',
                    'is_active' => true,
                ],
                [
                    'provider_name' => 'Masa Depan',
                    'policy_name' => 'Pendidikan Plus',
                    'policy_number' => 'MD-2024-778899',
                    'category' => 'education',
                    'premium_amount' => 690_000,
                    'coverage_amount' => 400_000_000,
                    'payment_period' => 'monthly',
                    'start_date' => now()->subMonths(8)->startOfDay(),
                    // Masih lama → "upcoming".
                    'next_due_date' => now()->addMonths(2)->startOfDay(),
                    'reminder_days' => 14,
                    'notes' => 'Dana pendidikan anak pertama.',
                    'is_active' => true,
                ],
            ],

            'budi@insuremind.ai' => [
                [
                    'provider_name' => 'SehatKu',
                    'policy_name' => 'Rawat Jalan Basic',
                    'policy_number' => 'SK-2025-000777',
                    'category' => 'health',
                    'premium_amount' => 520_000,
                    'coverage_amount' => 250_000_000,
                    'payment_period' => 'quarterly',
                    'start_date' => now()->subMonths(10)->startOfDay(),
                    'next_due_date' => now()->addDays(9)->startOfDay(),
                    'reminder_days' => 10,
                    'notes' => 'Polis rawat jalan tambahan.',
                    'is_active' => true,
                ],
                [
                    'provider_name' => 'Tangguh Proteksi',
                    'policy_name' => 'Kecelakaan Kerja',
                    'policy_number' => 'TP-2024-112233',
                    'category' => 'work',
                    'premium_amount' => 310_000,
                    'coverage_amount' => 150_000_000,
                    'payment_period' => 'monthly',
                    'start_date' => now()->subYears(1)->startOfDay(),
                    'next_due_date' => now()->addDays(21)->startOfDay(),
                    'reminder_days' => 7,
                    'notes' => 'Perlindungan risiko kerja.',
                    'is_active' => true,
                ],
            ],

            'siti@insuremind.ai' => [
                [
                    'provider_name' => 'Amanah Medika',
                    'policy_name' => 'Sehat Mandiri',
                    'policy_number' => 'AM-2025-909090',
                    'category' => 'health',
                    'premium_amount' => 275_000,
                    'coverage_amount' => 200_000_000,
                    'payment_period' => 'monthly',
                    'start_date' => now()->subMonths(5)->startOfDay(),
                    'next_due_date' => now()->addDays(12)->startOfDay(),
                    'reminder_days' => 14,
                    'notes' => 'Polis kesehatan pribadi.',
                    'is_active' => true,
                ],
                [
                    'provider_name' => 'Cerdas Jiwa',
                    'policy_name' => 'Proteksi Diri',
                    'policy_number' => 'CJ-2023-303030',
                    'category' => 'life',
                    'premium_amount' => 950_000,
                    'coverage_amount' => 400_000_000,
                    'payment_period' => 'semiannual',
                    'start_date' => now()->subYears(2)->startOfDay(),
                    'next_due_date' => now()->subDays(1)->startOfDay(),
                    'reminder_days' => 7,
                    'notes' => 'Asuransi jiwa perlindungan diri.',
                    'is_active' => true,
                ],
            ],
        ];
    }
}
