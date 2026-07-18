#!/bin/bash
#
# Syscend Campus - Production Deployment Script
# Target: Ubuntu 26.04 LTS / Nginx / PHP 8.5 / PostgreSQL 18
#
# Usage: sudo bash deploy.sh
#
set -euo pipefail

APP_DIR="/var/www/syscend-campus"
DB_NAME="syscend"
DB_USER="syscend"
DB_PASS="CHANGE_ME_TO_A_STRONG_PASSWORD"
PHP_VERSION="8.5"
SERVER_IP=""  # Auto-detected below

# ─── Colors ───────────────────────────────────────────────────────
RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'; BLUE='\033[0;34m'; NC='\033[0m'
log()   { echo -e "${GREEN}[INFO]${NC} $1"; }
warn()  { echo -e "${YELLOW}[WARN]${NC} $1"; }
err()   { echo -e "${RED}[ERROR]${NC} $1"; }
step()  { echo -e "\n${BLUE}━━━ Step $1: $2 ━━━${NC}"; }

# ─── Pre-flight ───────────────────────────────────────────────────
if [[ $EUID -ne 0 ]]; then
    err "This script must be run as root (sudo bash deploy.sh)"
    exit 1
fi

SERVER_IP=$(curl -s ifconfig.me 2>/dev/null || curl -s icanhazip.com 2>/dev/null || echo "YOUR_SERVER_IP")
log "Detected server IP: $SERVER_IP"

cd "$APP_DIR"

# ─── Step 1: System packages ──────────────────────────────────────
step 1 "Installing system dependencies"

apt-get update -qq

# PHP 8.5 + required extensions
apt-get install -y -qq \
    php${PHP_VERSION}-fpm \
    php${PHP_VERSION}-pgsql \
    php${PHP_VERSION}-mbstring \
    php${PHP_VERSION}-xml \
    php${PHP_VERSION}-curl \
    php${PHP_VERSION}-zip \
    php${PHP_VERSION}-bcmath \
    php${PHP_VERSION}-gd \
    php${PHP_VERSION}-intl \
    php${PHP_VERSION}-opcache \
    php${PHP_VERSION}-readline \
    php${PHP_VERSION}-dom \
    php${PHP_VERSION}-fileinfo \
    php${PHP_VERSION}-tokenizer \
    php${PHP_VERSION}-redis 2>/dev/null || warn "php-redis not available, skipping (not critical)"

# PostgreSQL client
apt-get install -y -qq postgresql-client

# Nginx
apt-get install -y -qq nginx

# Node.js (for Vite build) - if not already installed
if ! command -v node &> /dev/null; then
    curl -fsSL https://deb.nodesource.com/setup_22.x | bash -
    apt-get install -y -qq nodejs
fi

# Supervisor for queue worker
apt-get install -y -qq supervisor

log "System packages installed."

# ─── Step 2: Create database user and database ────────────────────
step 2 "Configuring PostgreSQL"

# Generate a strong password if still default
if [[ "$DB_PASS" == "CHANGE_ME_TO_A_STRONG_PASSWORD" ]]; then
    DB_PASS=$(openssl rand -base64 32 | tr -dc 'a-zA-Z0-9' | head -c 24)
    warn "Generated database password: $DB_PASS"
    warn "SAVE THIS PASSWORD: $DB_PASS"
fi

# Create user and database
sudo -u postgres psql -tc "SELECT 1 FROM pg_roles WHERE rolname='$DB_USER'" | grep -q 1 || \
    sudo -u postgres psql -c "CREATE USER $DB_USER WITH PASSWORD '$DB_PASS';"

sudo -u postgres psql -tc "SELECT 1 FROM pg_database WHERE datname='$DB_NAME'" | grep -q 1 || \
    sudo -u postgres psql -c "CREATE DATABASE $DB_NAME OWNER $DB_USER;"

sudo -u postgres psql -c "GRANT ALL PRIVILEGES ON DATABASE $DB_NAME TO $DB_USER;"
sudo -u postgres psql -d $DB_NAME -c "GRANT ALL ON SCHEMA public TO $DB_USER;"
sudo -u postgres psql -d $DB_NAME -c "ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON TABLES TO $DB_USER;"
sudo -u postgres psql -d $DB_NAME -c "ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON SEQUENCES TO $DB_USER;"

log "PostgreSQL user '$DB_USER' and database '$DB_NAME' ready."

# ─── Step 3: Write production .env ────────────────────────────────
step 3 "Writing production .env"

cp "$APP_DIR/.env" "$APP_DIR/.env.backup" 2>/dev/null || true

cat > "$APP_DIR/.env" << ENVEOF
APP_NAME="Syscend Campus"
APP_ENV=production
APP_KEY=base64:ZpCkLBJB1ug8aXD5AvcGRc9IUuWmzleWOQPDhCoW/rE=
APP_DEBUG=false
APP_URL=http://${SERVER_IP}
APP_TIMEZONE=Africa/Freetown

GEMINI_API_KEY=

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file

BCRYPT_ROUNDS=12

LOG_CHANNEL=daily
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=warning

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=${DB_NAME}
DB_USERNAME=${DB_USER}
DB_PASSWORD=${DB_PASS}
DB_SSLMODE=prefer

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_PATH=/
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=587
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@syscend.com"
MAIL_FROM_NAME="\${APP_NAME}"

SMS_PROVIDER=vonage
VONAGE_API_KEY=
VONAGE_API_SECRET=
VONAGE_SENDER_ID=Syscend

ORANGE_MONEY_MERCHANT_KEY=
ORANGE_MONEY_API_USER=
ORANGE_MONEY_API_KEY=
ORANGE_MONEY_RETURN_URL=http://${SERVER_IP}/school/fees/payments/callback?method=orange_money
ORANGE_MONEY_CANCEL_URL=http://${SERVER_IP}/school/fees/payments/callback?method=orange_money&status=cancelled
ORANGE_MONEY_WEBHOOK_URL=http://${SERVER_IP}/api/v1/payments/webhook/orange-money

AFRIMONEY_API_KEY=
AFRIMONEY_APP_ID=
AFRIMONEY_APP_SECRET=

VITE_APP_NAME="\${APP_NAME}"
ENVEOF

log ".env written with APP_URL=http://${SERVER_IP}"

# ─── Step 4: Test database connection ─────────────────────────────
step 4 "Testing database connection"

PGPASSWORD="$DB_PASS" psql -h 127.0.0.1 -U "$DB_USER" -d "$DB_NAME" -c "SELECT 1 AS connection_test;" && \
    log "Database connection successful." || \
    { err "Database connection FAILED. Check credentials."; exit 1; }

# ─── Step 5: Run migrations and seeders ───────────────────────────
step 5 "Running migrations and seeders"

php artisan migrate --force
log "Migrations complete."

php artisan db:seed --force
log "Seeders complete."

# ─── Step 6: Laravel production optimizations ─────────────────────
step 6 "Optimizing Laravel for production"

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Storage link (confirm it exists)
php artisan storage:link --force 2>/dev/null || true

# Clear stale caches
php artisan cache:clear
php artisan auth:cache-clear 2>/dev/null || true
php artisan permissions:cache-reset 2>/dev/null || true

log "Laravel caches built."

# ─── Step 7: Build frontend ───────────────────────────────────────
step 7 "Building frontend assets"

npm ci --production=false 2>/dev/null || npm install
npm run build

log "Frontend built."

# ─── Step 8: File permissions ─────────────────────────────────────
step 8 "Setting file permissions"

# Determine the web server user
WEB_USER="www-data"

chown -R ${WEB_USER}:${WEB_USER} "$APP_DIR"
chmod -R 755 "$APP_DIR/storage"
chmod -R 755 "$APP_DIR/bootstrap/cache"
find "$APP_DIR/storage" -type d -exec chmod 775 {} \;
find "$APP_DIR/bootstrap/cache" -type d -exec chmod 775 {} \;
find "$APP_DIR/storage" -type f -exec chmod 664 {} \;

# Make .env readable only by root
chmod 600 "$APP_DIR/.env"

log "Permissions set."

# ─── Step 9: Configure Nginx ──────────────────────────────────────
step 9 "Configuring Nginx"

# Remove default site
rm -f /etc/nginx/sites-enabled/default

# Copy our config
cp "$APP_DIR/deploy/nginx-syscend.conf" /etc/nginx/sites-available/syscend

# Replace YOUR_SERVER_IP with actual IP
sed -i "s/YOUR_SERVER_IP/${SERVER_IP}/g" /etc/nginx/sites-available/syscend

# Enable site
ln -sf /etc/nginx/sites-available/syscend /etc/nginx/sites-enabled/syscend

# Test config
nginx -t && log "Nginx config valid." || { err "Nginx config invalid!"; exit 1; }

# ─── Step 10: Configure PHP-FPM ──────────────────────────────────
step 10 "Configuring PHP-FPM"

PHP_FPM_POOL="/etc/php/${PHP_VERSION}/fpm/pool.d/www.conf"

# Ensure FPM listens on Unix socket
if [[ -f "$PHP_FPM_POOL" ]]; then
    # Backup
    cp "$PHP_FPM_POOL" "${PHP_FPM_POOL}.bak"

    # Optimize for production
    sed -i 's/^;listen.owner = .*/listen.owner = www-data/' "$PHP_FPM_POOL"
    sed -i 's/^;listen.group = .*/listen.group = www-data/' "$PHP_FPM_POOL"
    sed -i 's/^listen = .*/listen = \/var\/run\/php\/php8.5-fpm.sock/' "$PHP_FPM_POOL"

    # Increase limits
    sed -i 's/^pm\.max_children = .*/pm.max_children = 25/' "$PHP_FPM_POOL" 2>/dev/null || true
    sed -i 's/^pm\.start_servers = .*/pm.start_servers = 5/' "$PHP_FPM_POOL" 2>/dev/null || true
    sed -i 's/^pm\.min_spare_servers = .*/pm.min_spare_servers = 3/' "$PHP_FPM_POOL" 2>/dev/null || true
    sed -i 's/^pm\.max_spare_servers = .*/pm.max_spare_servers = 10/' "$PHP_FPM_POOL" 2>/dev/null || true

    log "PHP-FPM pool optimized."
else
    warn "PHP-FPM pool config not found at $PHP_FPM_POOL, using defaults."
fi

# ─── Step 11: Configure queue worker with Supervisor ──────────────
step 11 "Configuring queue worker"

cat > /etc/supervisor/conf.d/syscend-worker.conf << 'SUPEREOF'
[program:syscend-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/syscend-campus/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/syscend-campus/storage/logs/worker.log
stopwaitsecs=3600
SUPEREOF

supervisorctl reread
supervisorctl update
supervisorctl start syscend-worker:* 2>/dev/null || true

log "Queue worker configured (2 processes)."

# ─── Step 12: Configure logrotate ────────────────────────────────
step 12 "Configuring log rotation"

cat > /etc/logrotate.d/syscend << 'LOGEOF'
/var/www/syscend-campus/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0664 www-data www-data
    sharedscripts
}
LOGEOF

log "Log rotation configured."

# ─── Step 13: Restart services ────────────────────────────────────
step 13 "Restarting services"

systemctl restart php${PHP_VERSION}-fpm
systemctl restart nginx
systemctl restart supervisor
systemctl enable php${PHP_VERSION}-fpm nginx supervisor 2>/dev/null || true

log "All services restarted."

# ─── Step 14: Verify deployment ───────────────────────────────────
step 14 "Verifying deployment"

# Test PHP-FPM socket
if [[ -S "/var/run/php/php${PHP_VERSION}-fpm.sock" ]]; then
    log "PHP-FPM socket is listening."
else
    warn "PHP-FPM socket not found. Checking alternative locations..."
    ls -la /var/run/php/ 2>/dev/null || warn "No /var/run/php/ directory"
fi

# Test HTTP response
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "http://127.0.0.1/" 2>/dev/null || echo "000")
if [[ "$HTTP_CODE" == "200" ]]; then
    log "HTTP test: $HTTP_CODE - Application is responding!"
elif [[ "$HTTP_CODE" == "302" || "$HTTP_CODE" == "301" ]]; then
    log "HTTP test: $HTTP_CODE - Redirect (likely to login - OK)"
else
    warn "HTTP test returned: $HTTP_CODE"
    warn "Checking Nginx error log..."
    tail -20 /var/log/nginx/error.log 2>/dev/null || true
fi

# Check worker processes
SUPERVISOR_STATUS=$(supervisorctl status 2>/dev/null || echo "supervisor not responding")
echo "$SUPERVISOR_STATUS"

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo -e "${GREEN} DEPLOYMENT COMPLETE${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "  URL:          http://${SERVER_IP}"
echo "  Admin Email:  syscend@gmail.com"
echo "  Admin Pass:   Demo@123"
echo "  School Admin: samuel540wisesamura@gmail.com / Demo@123"
echo "  Database:     ${DB_NAME} (user: ${DB_USER})"
echo "  DB Password:  ${DB_PASS}"
echo "  PHP-FPM:      php${PHP_VERSION}-fpm"
echo "  Queue:        2 workers via Supervisor"
echo "  Logs:         storage/logs/"
echo ""
echo "  ⚠  SAVE THE DATABASE PASSWORD: ${DB_PASS}"
echo "  ⚠  For HTTPS, install certbot: apt install certbot python3-certbot-nginx && certbot --nginx"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
