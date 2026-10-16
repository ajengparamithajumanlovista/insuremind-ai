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

## InsureMind AI — Running Without Laragon

This project uses **SQLite** as its default database, so it **does not require
Laragon, MySQL, or any external database server**. You only need PHP 8.2+ with
the `pdo_sqlite` extension (enabled by default).

### Quick setup (automatic)

Run the setup script for your operating system. The script creates `.env` and
`database/database.sqlite`, then runs the migrations and seeds all demo data
(accounts, products, policies, chats, documents, AI review cases, and audit
logs).

**Windows (PowerShell):**
```powershell
./scripts/setup-database.ps1
```

**Windows (double-click / Command Prompt):**
```bat
scripts\setup-database.bat
```

**macOS / Linux / Git Bash / WSL:**
```bash
chmod +x scripts/setup-database.sh
./scripts/setup-database.sh
```

When setup is complete, start the application:
```bash
php artisan serve
```

### Manual setup

To perform the setup steps yourself:

```bash
# 1. Copy the environment configuration
cp .env.example .env        # Windows: copy .env.example .env

# 2. Create the SQLite database file
#    Windows:  type nul > database\database.sqlite
touch database/database.sqlite

# 3. Generate the application key
php artisan key:generate

# 4. Run migrations and seed demo data
php artisan migrate:fresh --seed
```

> Note: The project's default `.env` is configured with `DB_CONNECTION=sqlite`.
> To use MySQL in production, set `DB_CONNECTION=mysql` and update the
> credentials in `.env`. The seeders will continue to work as-is because they
> all use Eloquent (not MySQL-specific SQL).

### Demo accounts

All accounts use the password `password`.

| Role     | Email                     |
|----------|---------------------------|
| Admin    | `admin@insuremind.ai`     |
| Reviewer | `reviewer@insuremind.ai`  |
| User     | `user@insuremind.ai`      |

If the admin account is missing or you need to reset its password, run:

```bash
php artisan insuremind:admin
# or with your own credentials:
php artisan insuremind:admin --email=email@anda.com --password=rahasia123
```

### Can't access the admin area? Check the following

1. **Make sure you are using an admin account** — sign in with
   `admin@insuremind.ai` / `password`. Regular accounts
   (`user@insuremind.ai`) **cannot** access `/admin` (they will receive a 403)
   and are redirected to their personal dashboard.
2. **Make sure you are using the correct URL** — run `php artisan serve`, then
   open `http://127.0.0.1:8000`. Do not mix domains (e.g. `<name>.test` from
   Laragon) with a different server, as the session cookies will not match.
3. **Clear old cookies / sessions** — after replacing the database
   (`migrate:fresh`), old session cookies become invalid. Sign out, clear the
   cookies for that host, then sign in again.
4. **Clear the configuration cache** — run:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan view:clear
   ```
5. **Restore the admin account** — run `php artisan insuremind:admin`, then sign
   in again with `admin@insuremind.ai` / `password`.

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
