#!/bin/bash
|--------------------------------------------------------------------------
| Syscend Campus — Production Deployment Script
|--------------------------------------------------------------------------
|
| Usage: bash deploy.sh
|
| Prerequisites:
|   - PHP 8.2+ with required extensions (pgsql, mbstring, openssl, etc.)
|   - Composer 2.x
|   - Node.js 18+ with npm
|   - PostgreSQL 17
|   - Redis (recommended)
|
| Run this script from the project root directory.
|
|--------------------------------------------------------------------------
*/

set -e

echo "============================================"
echo "  Syscend Campus — Production Deployment"
echo "============================================"
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Step 1: Check prerequisites
echo -e "${YELLOW}[1/12] Checking prerequisites...${NC}"

if ! command -v php &> /dev/null; then
    echo -e "${RED}ERROR: PHP is not installed.${NC}"
    exit 1
fi

PHP_VERSION=$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')
echo "  PHP version: $PHP_VERSION"

if ! command -v composer &> /dev/null; then
    echo -e "${RED}ERROR: Composer is not installed.${NC}"
    exit 1
fi

if ! command -v node &> /dev/null; then
    echo -e "${RED}ERROR: Node.js is not installed.${NC}"
    exit 1
fi

echo -e "${GREEN}  All prerequisites OK.${NC}"
echo ""

# Step 2: Check .env file
echo -e "${YELLOW}[2/12] Checking .env configuration...${NC}"

if [ ! -f .env ]; then
    echo -e "${YELLOW}  .env not found. Copying from .env.production...${NC}"
    cp .env.production .env
    echo -e "${RED}  WARNING: Edit .env with your production values before continuing.${NC}"
    echo "  Required fields: APP_KEY, APP_URL, DB_*, MAIL_*, REDIS_*, SMS_*, ORANGE_MONEY_*, AFRIMONEY_*"
    exit 1
fi

# Check if APP_KEY is set
APP_KEY=$(grep -E '^APP_KEY=' .env | cut -d'=' -f2)
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    echo -e "${YELLOW}  APP_KEY not set. Generating...${NC}"
    php artisan key:generate --force
fi

echo -e "${GREEN}  .env OK.${NC}"
echo ""

# Step 3: Install PHP dependencies
echo -e "${YELLOW}[3/12] Installing Composer dependencies...${NC}"
composer install --no-dev --optimize-autoloader --no-interaction
echo ""

# Step 4: Install Node dependencies
echo -e "${YELLOW}[4/12] Installing npm dependencies...${NC}"
npm ci --production=false
echo ""

# Step 5: Build frontend assets
echo -e "${YELLOW}[5/12] Building frontend assets...${NC}"
npm run build
echo ""

# Step 6: Run database migrations
echo -e "${YELLOW}[6/12] Running database migrations...${NC}"
php artisan migrate --force
echo ""

# Step 7: Seed database (optional — only if DB is empty)
echo -e "${YELLOW}[7/12] Checking if database needs seeding...${NC}"
STUDENT_COUNT=$(php artisan tinker --execute="echo \App\Models\Student::count();" 2>/dev/null || echo "0")
if [ "$STUDENT_COUNT" = "0" ]; then
    echo "  Database is empty. Running seeders..."
    php artisan db:seed --force
else
    echo "  Database has $STUDENT_COUNT students. Skipping seeders."
fi
echo ""

# Step 8: Cache configuration
echo -e "${YELLOW}[8/12] Caching configuration...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
echo ""

# Step 9: Create storage symlink
echo -e "${YELLOW}[9/12] Creating storage symlink...${NC}"
php artisan storage:link --force
echo ""

# Step 10: Set permissions
echo -e "${YELLOW}[10/12] Setting file permissions...${NC}"
chmod -R 775 storage bootstrap/cache
chmod -R 775 public/storage
echo ""

# Step 11: Clear expired password resets
echo -e "${YELLOW}[11/12] Clearing expired password resets...${NC}"
php artisan auth:clear-resets
echo ""

# Step 12: Verify deployment
echo -e "${YELLOW}[12/12] Verifying deployment...${NC}"

# Check route count
ROUTE_COUNT=$(php artisan route:list --columns=method,uri 2>/dev/null | wc -l)
echo "  Routes registered: ~$((ROUTE_COUNT - 2))"

# Check migration count
MIGRATION_COUNT=$(php artisan migrate:status --path=database/migrations 2>/dev/null | grep -c "Yes" || echo "0")
echo "  Migrations run: $MIGRATION_COUNT"

# Check Vite build
if [ -d "public/build" ]; then
    echo "  Frontend build: OK"
else
    echo -e "${RED}  Frontend build: MISSING${NC}"
fi

echo ""
echo "============================================"
echo -e "${GREEN}  Deployment Complete!${NC}"
echo "============================================"
echo ""
echo "Next steps:"
echo "  1. Start the web server:    php artisan serve --host=0.0.0.0 --port=8000"
echo "  2. Start the queue worker:  php artisan queue:work --sleep=3 --tries=3"
echo "  3. Set up cron job:         * * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1"
echo "  4. Configure your web server (Nginx/Apache) to point to the public/ directory"
echo ""
echo "SMS & Mobile Money:"
echo "  SMS credentials are configured per-school via the School Settings page."
echo "  Go to School Admin > Settings > Integrations to configure SMS and payment gateways."
echo ""
echo "Documentation:"
echo "  See SYSCEND_CAMPUS_IMPLEMENTATION_REPORT.md for full details."
echo ""
