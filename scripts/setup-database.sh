#!/usr/bin/env bash
#
# Set up the InsureMind AI database WITHOUT requiring Laragon/MySQL.
#
# This script uses SQLite (Laravel's default), so no external database server is
# needed. Works on macOS, Linux, and Git Bash/WSL on Windows.
#
# Pemakaian:
#   chmod +x scripts/setup-database.sh
#   ./scripts/setup-database.sh

set -euo pipefail

# Change to the project root (the parent directory of this scripts folder).
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$PROJECT_ROOT"

echo "==> Memeriksa PHP..."
if ! command -v php >/dev/null 2>&1; then
    echo "PHP tidak ditemukan pada PATH. Pasang PHP 8.2+ terlebih dahulu." >&2
    exit 1
fi

if ! php -m | grep -qi 'pdo_sqlite'; then
    echo "Ekstensi pdo_sqlite tidak aktif. Aktifkan pada php.ini terlebih dahulu." >&2
    exit 1
fi

echo "==> Menyiapkan file .env..."
if [ ! -f .env ]; then
    cp .env.example .env
    echo "    .env dibuat dari .env.example (DB_CONNECTION=sqlite)."
else
    echo "    .env sudah ada, dilewati."
fi

echo "==> Membuat file database SQLite..."
if [ ! -f database/database.sqlite ]; then
    touch database/database.sqlite
    echo "    database/database.sqlite dibuat."
else
    echo "    database/database.sqlite sudah ada."
fi

echo "==> Membuat APP_KEY (bila belum ada)..."
php artisan key:generate --force

echo "==> Menjalankan migrasi + seeding data demo..."
php artisan migrate:fresh --seed --force

echo ""
echo "Selesai. Database SQLite siap dipakai tanpa Laragon."
echo "Jalankan aplikasi dengan: php artisan serve"
