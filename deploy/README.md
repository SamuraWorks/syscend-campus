# Syscend Campus - Deployment Guide

## Quick Start

1. SSH into your server
2. Clone the repo (if not already):
   ```bash
   cd /var/www
   sudo git clone git@github.com:SamuraWorks/syscend-campus.git
   cd syscend-campus
   sudo git checkout main
   ```
3. Run the deployment script:
   ```bash
   sudo bash deploy/deploy.sh
   ```
4. Run verification:
   ```bash
   sudo bash deploy/verify.sh
   ```

## What the Deploy Script Does

| Step | Action |
|------|--------|
| 1 | Installs PHP 8.5 extensions (pgsql, mbstring, xml, curl, zip, bcmath, gd, intl, opcache), Nginx, Supervisor |
| 2 | Creates PostgreSQL user `syscend` and database `syscend` with strong password |
| 3 | Writes production `.env` (APP_ENV=production, APP_DEBUG=false, daily logs, secure sessions) |
| 4 | Tests database connection |
| 5 | Runs `php artisan migrate --force` and `db:seed --force` |
| 6 | Caches config, routes, views, events |
| 7 | Builds frontend with `npm run build` |
| 8 | Sets file permissions (www-data ownership, 755 dirs, .env 600) |
| 9 | Configures Nginx (public/ root, PHP-FPM, security headers, gzip) |
| 10 | Optimizes PHP-FPM pool |
| 11 | Configures queue worker with Supervisor (2 processes) |
| 12 | Sets up logrotate for Laravel logs |
| 13 | Restarts all services |

## Credentials

| Item | Value |
|------|-------|
| URL | `http://YOUR_SERVER_IP` |
| Super Admin | `syscend@gmail.com` / `Demo@123` |
| School Admin | `samuel540wisesamura@gmail.com` / `Demo@123` |
| DB Name | `syscend` |
| DB User | `syscend` |
| DB Password | Generated during deploy (shown in output) |

## Post-Deploy

### SSL with Let's Encrypt
```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com
```

### Check queue workers
```bash
supervisorctl status
supervisorctl restart syscend-worker:*
```

### View logs
```bash
tail -f /var/www/syscend-campus/storage/logs/laravel.log
tail -f /var/log/nginx/error.log
```

### Re-run migrations
```bash
cd /var/www/syscend-campus
sudo -u www-data php artisan migrate --force
```

### Clear caches
```bash
cd /var/www/syscend-campus
sudo -u www-data php artisan cache:clear
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
```

## Troubleshooting

### 502 Bad Gateway
- PHP-FPM not running: `systemctl status php8.5-fpm`
- Socket mismatch: check `listen` in `/etc/php/8.5/fpm/pool.d/www.conf`
- Restart: `systemctl restart php8.5-fpm nginx`

### Database connection refused
- PostgreSQL down: `systemctl status postgresql`
- Wrong credentials: check `.env` DB_* values
- User permissions: `sudo -u postgres psql -c "\du"`

### Storage 403 errors
- Missing storage link: `php artisan storage:link`
- Wrong permissions: `chown -R www-data:www-data storage bootstrap/cache`

### Queue jobs not processing
- Workers stopped: `supervisorctl restart syscend-worker:*`
- Check worker logs: `tail -f storage/logs/worker.log`
- Check failed jobs: `php artisan queue:failed`

### Blank page / 500 error
- Check `storage/logs/laravel.log`
- Enable debug temporarily: set `APP_DEBUG=true` in `.env`
- Run: `php artisan config:clear && php artisan config:cache`
