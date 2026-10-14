<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Default administrator (dashboard access).
        User::updateOrCreate(
            ['email' => 'admin@insuremind.ai'],
            [
                'name' => 'Admin InsureMind',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
            ]
        );

        // Default reviewer for the AI Quality Review queue.
        User::updateOrCreate(
            ['email' => 'reviewer@insuremind.ai'],
            [
                'name' => 'Reviewer InsureMind',
                'password' => Hash::make('password'),
                'role' => 'reviewer',
                'status' => 'active',
            ]
        );

        // Default regular user.
        User::updateOrCreate(
            ['email' => 'user@insuremind.ai'],
            [
                'name' => 'User InsureMind',
                'password' => Hash::make('password'),
                'role' => 'user',
                'status' => 'active',
            ]
        );

        // Akun pengguna tambahan untuk mencoba seluruh peran & status akun.
        $this->call(DemoUserSeeder::class);

        // Sample insurance products (clearly labelled as demo data).
        $this->call(InsuranceProductSeeder::class);

        // Data demo agar setiap fitur aplikasi langsung dapat dicoba:
        // profil finansial → analisis risiko → simulasi → polis → chat →
        // dokumen → kasus tinjauan AI → riwayat katalog → audit log.
        // Urutan penting karena seeder berikutnya bergantung pada pengguna,
        // produk, dan percakapan yang sudah dibuat di atas.
        $this->call([
            UserProfileSeeder::class,
            PolicySeeder::class,
            RiskAssessmentSeeder::class,
            RiskSimulationSeeder::class,
            ChatSeeder::class,
            PolicyDocumentSeeder::class,
            AiReviewCaseSeeder::class,
            ProductHistorySeeder::class,
            AuditLogSeeder::class,
        ]);
    }
}
