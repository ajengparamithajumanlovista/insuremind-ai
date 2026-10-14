<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Membuat / mengatur ulang akun admin
|--------------------------------------------------------------------------
|
| Akun ini dibuat oleh DatabaseSeeder, tetapi bila data admin terhapus atau
| kata sandinya tidak diketahui, perintah berikut memulihkan akses ke area
| admin tanpa perlu menjalankan ulang seluruh seeder.
|
| Contoh:
|   php artisan insuremind:admin                        # admin@insuremind.ai / password
|   php artisan insuremind:admin --email=me@contoh.com --password=rahasia123
*/
Artisan::command('insuremind:admin {--email=admin@insuremind.ai} {--password=password}', function () {
    $email = (string) $this->option('email');
    $password = (string) $this->option('password');

    $user = User::updateOrCreate(
        ['email' => $email],
        [
            'name' => 'Admin InsureMind',
            'password' => Hash::make($password),
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]
    );

    $this->info("Akun admin siap -> {$user->email} (kata sandi: {$password})");
    $this->line('Masuk di /login lalu Anda akan diarahkan ke /admin.');
})->purpose('Membuat atau mengatur ulang akun admin InsureMind AI');

