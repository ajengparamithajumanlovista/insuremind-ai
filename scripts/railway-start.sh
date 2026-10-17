#!/usr/bin/env bash
#
# InsureMind AI — start script for Railway (and other ephemeral-filesystem hosts).
#
# The container filesystem is recreated on every deploy, so anything stored in
# the project folder is lost. This script keeps the SQLite database and the
# user-uploaded documents on the mounted Railway Volume (default: /data).
#
# Usage (Railway "Start Command"):
#   bash scripts/railway-start.sh
#
# The script NEVER drops, wipes, or re-seeds existing data:
#   - the SQLite file is created only when it does not exist yet;
#   - migrations run incrementally (`migrate --force`, no `migrate:fresh`);
#   - the seeder runs only when the `users` table is still empty.
#
# Environment variables (all optional, defaults shown):
#   DATA_ROOT           /data
#   DB_CONNECTION       sqlite                       only SQLite is bootstrapped here
#   DB_DATABASE         $DATA_ROOT/database.sqlite   SQLite file location
#   DOCUMENTS_DISK      documents                    disk name for user uploads
#   DOCUMENTS_DISK_ROOT $DATA_ROOT/storage/documents
#   PORT                8080                         port used by `artisan serve`
#   RUN_SEEDER          true                         master switch for demo seeding
#   APP_KEY             -                            generated on boot if empty
#   Storage/logs/cache  -                            placed under $DATA_ROOT/storage

set -euo pipefail

# ---------------------------------------------------------------------------
# Paths
# ---------------------------------------------------------------------------

PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$PROJECT_ROOT"

DATA_ROOT="${DATA_ROOT:-/data}"
DATA_ROOT="${DATA_ROOT%/}"

# Use the volume only when it is actually mounted and writable; otherwise fall
# back to the project folders so the container still boots (data will not
# survive a redeploy, but the app stays available).
if [ -d "$DATA_ROOT" ] && [ -w "$DATA_ROOT" ]; then
    USE_VOLUME=1
else
    USE_VOLUME=0
    echo "[start] WARNING: $DATA_ROOT is not a writable volume; using project folders."
    echo "[start] Attach a Railway Volume mounted at $DATA_ROOT to persist data across deploys."
fi

export DB_CONNECTION="${DB_CONNECTION:-sqlite}"

if [ "$USE_VOLUME" -eq 1 ]; then
    export DB_DATABASE="${DB_DATABASE:-$DATA_ROOT/database.sqlite}"
    export DOCUMENTS_DISK="${DOCUMENTS_DISK:-documents}"
    # Absolute path outside the project folder => survives redeploys.
    export DOCUMENTS_DISK_ROOT="${DOCUMENTS_DISK_ROOT:-$DATA_ROOT/storage/documents}"
    STORAGE_BASE="$DATA_ROOT/storage"
else
    export DB_DATABASE="${DB_DATABASE:-$PROJECT_ROOT/database/database.sqlite}"
    export DOCUMENTS_DISK="${DOCUMENTS_DISK:-documents}"
    export DOCUMENTS_DISK_ROOT="${DOCUMENTS_DISK_ROOT:-$PROJECT_ROOT/storage/app/private/documents}"
    STORAGE_BASE="$PROJECT_ROOT/storage"
fi

APP_STORAGE="$STORAGE_BASE/app"
LOG_DIR="${LOG_DIR:-$STORAGE_BASE/logs}"
FRAMEWORK_CACHE="$STORAGE_BASE/framework"

PORT="${PORT:-8080}"
RUN_SEEDER="${RUN_SEEDER:-true}"

echo "[start] PROJECT_ROOT=$PROJECT_ROOT"
echo "[start] DB_CONNECTION=$DB_CONNECTION"
echo "[start] DB_DATABASE=$DB_DATABASE"
echo "[start] DOCUMENTS_DISK=$DOCUMENTS_DISK"
echo "[start] DOCUMENTS_DISK_ROOT=$DOCUMENTS_DISK_ROOT"
echo "[start] PORT=$PORT"

# ---------------------------------------------------------------------------
# Helpers
# ---------------------------------------------------------------------------

# Extract the configured SQLite file path (used only when DB_DATABASE is empty
# but config/database.php already resolves a path).
resolve_sqlite_path() {
    php -r '
        require $argv[1]."/vendor/autoload.php";
        $app = require $argv[1]."/bootstrap/app.php";
        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        echo (string) config("database.connections.".config("database.default").".database");
    ' "$PROJECT_ROOT" 2>/dev/null || true
}

# Succeeds (exit 0) only when the `users` table exists AND contains at least one
# row. Any error is treated as "already seeded" so seeding is skipped rather
# than risking duplicate accounts.
users_table_has_rows() {
    php -r '
        try {
            require $argv[1]."/vendor/autoload.php";
            $app = require $argv[1]."/bootstrap/app.php";
            $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
            $db = $app->make("db")->connection();
            if (! $db->getSchemaBuilder()->hasTable("users")) {
                exit(1);
            }
            exit($db->table("users")->exists() ? 0 : 1);
        } catch (Throwable $e) {
            exit(0);
        }
    ' "$PROJECT_ROOT" 2>/dev/null
}

# ---------------------------------------------------------------------------
# 1. Filesystem preparation (never overwrites existing files)
# ---------------------------------------------------------------------------

mkdir -p "$APP_STORAGE/private" "$APP_STORAGE/public" "$LOG_DIR" "$DOCUMENTS_DISK_ROOT" \
    "$FRAMEWORK_CACHE/cache" "$FRAMEWORK_CACHE/sessions" "$FRAMEWORK_CACHE/views" \
    "$FRAMEWORK_CACHE/testing"

# Point Laravel's storage directory at the persistent volume when available, so
# logs, compiled views, sessions, and the private/public disks all survive a
# redeploy. LARAVEL_STORAGE_PATH is honoured by bootstrap/app.php.
if [ "$USE_VOLUME" -eq 1 ]; then
    export LARAVEL_STORAGE_PATH="$STORAGE_BASE"
fi

# SQLite only needs an existing, writable file. `touch` only updates the
# timestamp of an existing file and never truncates it, but we check first so
# an existing database is provably left untouched.
if [ "$DB_CONNECTION" = "sqlite" ]; then
    if [ -z "${DB_DATABASE:-}" ] || [ "$DB_DATABASE" = ":memory:" ]; then
        DB_DATABASE="$(resolve_sqlite_path)"
        export DB_DATABASE
    fi

    if [ -z "${DB_DATABASE:-}" ] || [ "$DB_DATABASE" = ":memory:" ]; then
        echo "[start] ERROR: cannot resolve the SQLite path; set DB_DATABASE explicitly." >&2
        exit 1
    fi

    if [ ! -f "$DB_DATABASE" ]; then
        mkdir -p "$(dirname "$DB_DATABASE")"
        touch "$DB_DATABASE"
        echo "[start] Created new SQLite database: $DB_DATABASE"
    else
        echo "[start] Keeping existing SQLite database: $DB_DATABASE"
    fi
fi

# ---------------------------------------------------------------------------
# 2. Application key and cleared caches
# ---------------------------------------------------------------------------

if [ -z "${APP_KEY:-}" ]; then
    APP_KEY="$(php artisan key:generate --show --no-ansi | tr -d '[:space:]')"
    export APP_KEY
    echo "[start] APP_KEY was empty; generated one for this boot."
    echo "[start] IMPORTANT: add APP_KEY to the Railway variables so it stays stable."
fi

# Drop a stale compiled config/cache so the paths above always apply.
php artisan config:clear --no-ansi >/dev/null 2>&1 || true
php artisan cache:clear --no-ansi >/dev/null 2>&1 || true
php artisan view:clear --no-ansi >/dev/null 2>&1 || true

# ---------------------------------------------------------------------------
# 3. Migrations (incremental — existing tables and rows are preserved)
# ---------------------------------------------------------------------------

echo "[start] Running migrations (no fresh / no wipe)..."
php artisan migrate --force --no-ansi

# ---------------------------------------------------------------------------
# 4. Seeding — only when the users table is still empty
# ---------------------------------------------------------------------------

if [ "$RUN_SEEDER" != "true" ]; then
    echo "[start] RUN_SEEDER=$RUN_SEEDER; seeding skipped."
elif users_table_has_rows; then
    echo "[start] Users already exist; seeder skipped (existing accounts untouched)."
else
    echo "[start] Users table is empty; running DatabaseSeeder..."
    php artisan db:seed --force --no-ansi
fi

# ---------------------------------------------------------------------------
# 5. Public storage symlink (harmless when it already exists)
# ---------------------------------------------------------------------------

php artisan storage:link --no-ansi >/dev/null 2>&1 || true

# ---------------------------------------------------------------------------
# 6. Serve
# ---------------------------------------------------------------------------

echo "[start] Starting server on 0.0.0.0:$PORT"
exec php artisan serve --host=0.0.0.0 --port="$PORT"
