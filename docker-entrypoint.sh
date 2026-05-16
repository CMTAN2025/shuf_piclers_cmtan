#!/bin/sh
set -e

# Write .env from environment variables
cat > .env <<EOF
APP_NAME=Laravel
APP_ENV=${APP_ENV:-production}
APP_KEY=${APP_KEY:-}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL:-http://localhost}

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=${DB_CONNECTION:-pgsql}
DB_URL=${DB_URL:-}

SESSION_DRIVER=file
SESSION_LIFETIME=120

CACHE_STORE=file
QUEUE_CONNECTION=sync
EOF

php artisan key:generate --force
php artisan migrate --force
php artisan serve --host=0.0.0.0 --port=10000
