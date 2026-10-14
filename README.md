<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## InsureMind AI — Menjalankan Tanpa Laragon

Proyek ini menggunakan **SQLite** sebagai database default sehingga **tidak
memerlukan Laragon, MySQL, atau server database eksternal apa pun**. Cukup
tersedia PHP 8.2+ dengan ekstensi `pdo_sqlite` (aktif secara bawaan).

### Cara cepat (otomatis)

Jalankan skrip penyiapan sesuai sistem operasi Anda. Skrip ini akan membuat
`.env`, membuat file `database/database.sqlite`, lalu menjalankan migrasi
sekaligus mengisi seluruh data demo (akun, produk, polis, chat, dokumen,
kasus tinjauan AI, dan audit log).

**Windows (PowerShell):**
```powershell
./scripts/setup-database.ps1
```

**Windows (klik dua kali / CMD):**
```bat
scripts\setup-database.bat
```

**macOS / Linux / Git Bash / WSL:**
```bash
chmod +x scripts/setup-database.sh
./scripts/setup-database.sh
```

Setelah selesai, jalankan aplikasi:
```bash
php artisan serve
```

### Cara manual

Bila ingin melakukan langkah-langkahnya sendiri:

```bash
# 1. Salin konfigurasi lingkungan
cp .env.example .env        # Windows: copy .env.example .env

# 2. Buat file database SQLite
#    Windows:  type nul > database\database.sqlite
touch database/database.sqlite

# 3. Buat aplikasi key
php artisan key:generate

# 4. Migrasi + isi data demo
php artisan migrate:fresh --seed
```

> Catatan: `.env` bawaan proyek sudah disetel `DB_CONNECTION=sqlite`. Bila Anda
> ingin memakai MySQL kelas produksi, ubah `DB_CONNECTION=mysql` beserta
> kredensialnya pada `.env` — seeder tetap bekerja apa adanya karena seluruh
> seeder memakai Eloquent (bukan SQL khusus MySQL).

### Akun demo

Semua akun memakai kata sandi `password`.

| Peran    | Email                     |
|----------|---------------------------|
| Admin    | `admin@insuremind.ai`     |
| Reviewer | `reviewer@insuremind.ai`  |
| User     | `user@insuremind.ai`      |

Bila akun admin hilang atau kata sandinya perlu diatur ulang, jalankan:

```bash
php artisan insuremind:admin
# atau dengan kredensial sendiri:
php artisan insuremind:admin --email=email@anda.com --password=rahasia123
```

### Tidak bisa masuk ke area admin? Cek hal berikut

1. **Pastikan memakai akun admin** — login dengan `admin@insuremind.ai` /
   `password`. Akun biasa (`user@insuremind.ai`) memang **tidak** boleh membuka
   `/admin` (akan mendapat 403) dan akan diarahkan ke dashboard pribadi.
2. **Pastikan mengakses URL yang benar** — jalankan `php artisan serve` lalu buka
   `http://127.0.0.1:8000`. Jangan mencampur domain (mis. `<nama>.test` dari
   Laragon) dengan server yang berbeda karena cookie sesi tidak akan cocok.
3. **Hapus cookie / sesi lama** — setelah database diganti (`migrate:fresh`),
   cookie sesi lama menjadi tidak valid. Lakukan logout, bersihkan cookie untuk
   host tersebut, lalu login ulang.
4. **Bersihkan cache konfigurasi** — jalankan:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan view:clear
   ```
5. **Pulihkan akun admin** — jalankan `php artisan insuremind:admin` lalu login
   ulang dengan `admin@insuremind.ai` / `password`.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
