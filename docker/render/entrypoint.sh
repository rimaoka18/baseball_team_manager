#!/bin/sh
set -e

# Respect DB_DATABASE if set (it should point at the persistent disk mount on
# Render, e.g. /var/data/database.sqlite) so migrations/writes land there
# instead of the container's ephemeral filesystem. Falls back to Laravel's
# own default path when unset (e.g. local `docker run` without a disk).
DB_PATH="${DB_DATABASE:-/var/www/database/database.sqlite}"
mkdir -p "$(dirname "$DB_PATH")"
if [ ! -f "$DB_PATH" ]; then
    echo "Creating SQLite database file at $DB_PATH"
    touch "$DB_PATH"
fi

# Same persistent disk holds player photos, at /var/data/photos (see the
# "photos" disk in config/filesystems.php). Ensure the directory exists, then
# (re-)create the public/photos and public/storage symlinks — these must be
# created here rather than at Docker build time, since the "photos" disk root
# is only known once /var/data is actually mounted, which happens at runtime.
if [ -d /var/data ]; then
    mkdir -p /var/data/photos
fi
php artisan storage:link || true

if [ -z "$APP_KEY" ]; then
    echo "WARNING: APP_KEY is not set — generating one for this run only." >&2
    echo "Sessions and any encrypted data will be invalidated on every restart/deploy." >&2
    echo "Set APP_KEY as a persistent Render environment variable instead (see README notes)." >&2
    php artisan key:generate --force
fi

php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan migrate --force

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
