# Syscend Campus

School Management Platform for Sierra Leone — built with Laravel 13 + React (Inertia.js).

## Stack

- **Backend:** Laravel 13, PostgreSQL 17, PHP 8.3+
- **Frontend:** React 19, TypeScript, Tailwind CSS 4, Inertia.js 3, Zustand
- **Build:** Vite 8, Composer, npm

## Quick Start

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install
npm run dev
php artisan serve
```

## Production

```bash
composer install --no-dev --optimize-autoloader
npm run build
php artisan migrate --force
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

See `deploy.sh` for automated deployment.

## License

MIT
