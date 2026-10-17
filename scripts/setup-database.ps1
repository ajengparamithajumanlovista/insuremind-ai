<#
.SYNOPSIS
    Menyiapkan database InsureMind AI TANPA memerlukan Laragon/MySQL.

.DESCRIPTION
    Skrip ini menggunakan SQLite (bawaan Laravel) sehingga tidak butuh server
    database eksternal. Skrip akan:
      1. Menyalin .env.example menjadi .env bila belum ada.
      2. Membuat file database/database.sqlite bila belum ada.
      3. Menjalankan migrasi (`php artisan migrate --force`) TANPA menghapus
         data, lalu menjalankan seeder hanya bila tabel users masih kosong.

    Dapat dijalankan di Windows (PowerShell), macOS, dan Linux selama PHP 8.2+
    dan ekstensi pdo_sqlite tersedia.

.EXAMPLE
    # Windows PowerShell
    php -S localhost:8000 -t public # start the application
    ./scripts/setup-database.ps1
#>

$ErrorActionPreference = 'Stop'

# Change to the project root (the parent directory of this scripts folder).
$projectRoot = Split-Path -Parent $PSScriptRoot
Set-Location $projectRoot

Write-Host '==> Memeriksa PHP...' -ForegroundColor Cyan
if (-not (Get-Command php -ErrorAction SilentlyContinue)) {
    Write-Error 'PHP tidak ditemukan pada PATH. Pasang PHP 8.2+ terlebih dahulu.'
}

$hasSqlite = php -m | Select-String -Pattern 'pdo_sqlite'
if (-not $hasSqlite) {
    Write-Error 'Ekstensi pdo_sqlite tidak aktif. Aktifkan pada php.ini terlebih dahulu.'
}

Write-Host '==> Menyiapkan file .env...' -ForegroundColor Cyan
if (-not (Test-Path '.env')) {
    Copy-Item '.env.example' '.env'
    Write-Host '    .env dibuat dari .env.example (DB_CONNECTION=sqlite).'
} else {
    Write-Host '    .env sudah ada, dilewati.'
}

Write-Host '==> Membuat file database SQLite...' -ForegroundColor Cyan
if (-not (Test-Path 'database/database.sqlite')) {
    New-Item -ItemType File -Path 'database/database.sqlite' -Force | Out-Null
    Write-Host '    database/database.sqlite dibuat.'
} else {
    Write-Host '    database/database.sqlite sudah ada.'
}

Write-Host '==> Membuat APP_KEY (bila belum ada)...' -ForegroundColor Cyan
php artisan key:generate --force

Write-Host '==> Menjalankan migrasi (tanpa menghapus data yang sudah ada)...' -ForegroundColor Cyan
php artisan migrate --force

# Seeder hanya dijalankan bila tabel users masih kosong, supaya akun yang sudah
# terdaftar tidak pernah tertimpa atau terduplikasi.
php -r 'require "vendor/autoload.php"; $app = require "bootstrap/app.php"; $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap(); $db = $app->make("db")->connection(); exit($db->getSchemaBuilder()->hasTable("users") && $db->table("users")->exists() ? 0 : 1);'
if ($LASTEXITCODE -eq 0) {
    Write-Host '    Akun sudah ada, seeding dilewati.'
} else {
    Write-Host '    Tabel users masih kosong, menjalankan DatabaseSeeder...'
    php artisan db:seed --force
}

Write-Host ''
Write-Host 'Selesai. Database SQLite siap dipakai tanpa Laragon.' -ForegroundColor Green
Write-Host 'Jalankan aplikasi dengan: php artisan serve' -ForegroundColor Green
