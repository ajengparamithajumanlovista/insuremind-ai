<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;

/**
 * Profil finansial contoh agar analisis risiko dan simulator risiko hidup
 * langsung menghasilkan keluaran yang bermakna (tidak kosong).
 *
 * Setiap profil sengaja dibuat dengan skenario berbeda sehingga halaman
 * "Analisis Risiko" menampilkan variasi tingkat kebutuhan (low/medium/high).
 */
class UserProfileSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->profiles() as $email => $profile) {
            $user = User::query()->where('email', $email)->first();

            if (! $user) {
                continue;
            }

            UserProfile::updateOrCreate(
                ['user_id' => $user->getKey()],
                $profile
            );
        }
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function profiles(): array
    {
        return [
            // Kebutuhan tinggi: usia matang, banyak tanggungan, dana darurat minim.
            'user@insuremind.ai' => [
                'age' => 42,
                'occupation' => 'Manajer Operasional',
                'city' => 'Jakarta',
                'monthly_income' => 25_000_000,
                'monthly_expense' => 14_000_000,
                'monthly_debt' => 5_500_000,
                'savings' => 20_000_000,
                'dependents' => 3,
                'has_health_insurance' => false,
                'has_life_insurance' => false,
                'has_work_insurance' => false,
                'existing_coverage' => 100_000_000,
                'education_years' => 4,
                'education_cost' => 180_000_000,
                'notes' => 'Contoh profil dengan kebutuhan perlindungan tinggi untuk demo analisis risiko.',
            ],

            // Kebutuhan sedang: sudah punya asuransi kesehatan, tanggungan sedikit.
            'budi@insuremind.ai' => [
                'age' => 34,
                'occupation' => 'Software Engineer',
                'city' => 'Bandung',
                'monthly_income' => 18_000_000,
                'monthly_expense' => 8_500_000,
                'monthly_debt' => 2_000_000,
                'savings' => 45_000_000,
                'dependents' => 1,
                'has_health_insurance' => true,
                'has_life_insurance' => false,
                'has_work_insurance' => true,
                'existing_coverage' => 250_000_000,
                'education_years' => 10,
                'education_cost' => 120_000_000,
                'notes' => 'Sudah memiliki asuransi kesehatan dari kantor.',
            ],

            // Kebutuhan rendah: tanpa tanggungan, dana darurat sehat.
            'siti@insuremind.ai' => [
                'age' => 28,
                'occupation' => 'Desainer Grafis',
                'city' => 'Yogyakarta',
                'monthly_income' => 12_000_000,
                'monthly_expense' => 5_000_000,
                'monthly_debt' => 0,
                'savings' => 60_000_000,
                'dependents' => 0,
                'has_health_insurance' => true,
                'has_life_insurance' => true,
                'has_work_insurance' => false,
                'existing_coverage' => 300_000_000,
                'education_years' => 0,
                'education_cost' => 0,
                'notes' => 'Belum berkeluarga dan dana darurat sudah memadai.',
            ],

            // Kebutuhan tinggi: penghasilan kecil, tanggungan banyak.
            'andi@insuremind.ai' => [
                'age' => 50,
                'occupation' => 'Wirausaha',
                'city' => 'Surabaya',
                'monthly_income' => 9_000_000,
                'monthly_expense' => 7_500_000,
                'monthly_debt' => 3_000_000,
                'savings' => 5_000_000,
                'dependents' => 4,
                'has_health_insurance' => false,
                'has_life_insurance' => false,
                'has_work_insurance' => false,
                'existing_coverage' => 0,
                'education_years' => 3,
                'education_cost' => 90_000_000,
                'notes' => 'Arus kas ketat dengan tanggungan besar; prioritas proteksi.',
            ],
        ];
    }
}
