#!/bin/bash
#
# Post-Deployment Verification Script for Syscend Campus
# Run this after deploy.sh to verify everything is working.
#
set -uo pipefail

RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'; NC='\033[0m'
pass() { echo -e "${GREEN}✓${NC} $1"; }
fail() { echo -e "${RED}✗${NC} $1"; FAILURES=$((FAILURES+1)); }
warn() { echo -e "${YELLOW}!${NC} $1"; }
FAILURES=0

echo "━━━ Syscend Campus - Deployment Verification ━━━"
echo ""

# 1. Check services
echo "── Services ──"
for svc in nginx php8.5-fpm postgresql supervisor; do
    if systemctl is-active --quiet "$svc" 2>/dev/null; then
        pass "$svc is running"
    else
        fail "$svc is NOT running"
    fi
done
echo ""

# 2. Check PHP extensions
echo "── PHP Extensions ──"
for ext in pgsql mbstring xml curl zip bcmath gd intl opcache; do
    if php -m 2>/dev/null | grep -qi "^${ext}$"; then
        pass "php-${ext}"
    else
        fail "php-${ext} NOT installed"
    fi
done
echo ""

# 3. Check Nginx config
echo "── Nginx ──"
if nginx -t 2>&1 | grep -q "successful"; then
    pass "Nginx config is valid"
else
    fail "Nginx config has errors"
fi

if [[ -L /etc/nginx/sites-enabled/syscend ]]; then
    pass "Syscend site is enabled"
else
    fail "Syscend site NOT enabled"
fi
echo ""

# 4. Check database
echo "── Database ──"
APP_DIR="/var/www/syscend-campus"
DB_USER=$(grep DB_USERNAME "$APP_DIR/.env" | cut -d= -f2)
DB_PASS=$(grep DB_PASSWORD "$APP_DIR/.env" | cut -d= -f2)
DB_NAME=$(grep DB_DATABASE "$APP_DIR/.env" | cut -d= -f2)

if PGPASSWORD="$DB_PASS" psql -h 127.0.0.1 -U "$DB_USER" -d "$DB_NAME" -c "SELECT 1;" &>/dev/null; then
    pass "Database connection OK"
else
    fail "Database connection FAILED"
fi

# Check migration table
TABLE_COUNT=$(PGPASSWORD="$DB_PASS" psql -h 127.0.0.1 -U "$DB_USER" -d "$DB_NAME" -t -c "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='public';" 2>/dev/null | tr -d ' ')
if [[ "$TABLE_COUNT" -gt 20 ]]; then
    pass "Database has $TABLE_COUNT tables (migrations ran)"
else
    warn "Database has only $TABLE_COUNT tables - may need migrations"
fi
echo ""

# 5. Check Laravel
echo "── Laravel ──"
cd "$APP_DIR"

ENV_OK=$(php artisan tinker --execute="echo app()->environment();" 2>/dev/null)
if [[ "$ENV_OK" == "production" ]]; then
    pass "APP_ENV=production"
else
    warn "APP_ENV=$ENV_OK (expected production)"
fi

if [[ -f bootstrap/cache/config.php ]]; then
    pass "Config cache exists"
else
    fail "Config cache NOT found"
fi

if [[ -f bootstrap/cache/routes-v7.php ]]; then
    pass "Route cache exists"
else
    fail "Route cache NOT found"
fi
echo ""

# 6. Check HTTP
echo "── HTTP ──"
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "http://127.0.0.1/" 2>/dev/null)
if [[ "$HTTP_CODE" == "200" || "$HTTP_CODE" == "302" || "$HTTP_CODE" == "301" ]]; then
    pass "HTTP response: $HTTP_CODE"
else
    fail "HTTP response: $HTTP_CODE"
fi

# Check Vite assets
VITE_HASH=$(ls public/build/assets/app-*.js 2>/dev/null | head -1)
if [[ -n "$VITE_HASH" ]]; then
    pass "Frontend assets built"
else
    fail "Frontend assets NOT found"
fi
echo ""

# 7. Check queue workers
echo "── Queue Workers ──"
WORKER_STATUS=$(supervisorctl status syscend-worker:* 2>/dev/null | grep -c "RUNNING" || echo "0")
if [[ "$WORKER_STATUS" -ge 1 ]]; then
    pass "$WORKER_STATUS queue workers running"
else
    warn "No queue workers running"
fi
echo ""

# 8. Check file permissions
echo "── Permissions ──"
if [[ -f "$APP_DIR/.env" ]]; then
    PERMS=$(stat -c %a "$APP_DIR/.env" 2>/dev/null)
    if [[ "$PERMS" == "600" ]]; then
        pass ".env permissions: $PERMS (secure)"
    else
        warn ".env permissions: $PERMS (should be 600)"
    fi
fi

if [[ -d "$APP_DIR/storage" ]]; then
    OWNER=$(stat -c %U "$APP_DIR/storage" 2>/dev/null)
    if [[ "$OWNER" == "www-data" ]]; then
        pass "storage/ owned by www-data"
    else
        warn "storage/ owned by $OWNER (should be www-data)"
    fi
fi
echo ""

# Summary
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
if [[ $FAILURES -eq 0 ]]; then
    echo -e "${GREEN} ALL CHECKS PASSED${NC}"
else
    echo -e "${RED} $FAILURES CHECK(S) FAILED${NC}"
fi
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
