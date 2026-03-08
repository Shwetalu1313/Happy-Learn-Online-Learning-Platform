# Happy Learn Online Learning Platform

Happy Learn is a Laravel 12 web platform that combines:
- online learning
- course management and enrollment
- teacher/student dashboards
- forum discussion with replies
- admin analytics, export, and print reporting
- centralized in-app notification system

This repository is actively updated. For detailed session-by-session changes, see:
- `PROJECT_UPDATE_REPORT.md`

## Current Stack

- Laravel `^12.0`
- PHP `^8.2` (Sail runtime uses PHP 8.4 image)
- MySQL
- Redis
- Mailpit
- Vite + Bootstrap 5 + Sass
- Docker Compose (Laravel Sail based)

## Major Features (Current)

- Authentication and email verification
- Google OAuth login
- Role-based access (admin/teacher/student)
- Category, subcategory, course, lesson, exercise, question flows
- Course enrollment and payments (free/points/card flow in app)
- Job opportunities module
- Forum with comment reply threading
- Global search (MySQL-backed, not Elasticsearch)
- Admin dashboard with:
  - metrics
  - timeline chart
  - CSV export
  - print layouts: `A5`, `A4`, `A3`, `A1`
- Centralized notifications with admin configuration
- Premium dark-mode UI for student/teacher side
- Light-mode admin portal

## Recommended Run Mode: Docker (Sail)

This project is configured to run in Docker using `compose.yaml`.

### 1. Prerequisites

- Docker Desktop (with Compose)
- Git

### 2. Setup

```bash
git clone <your-repo-url>
cd Happy-Learn-Online-Learning-Platform
cp .env.example .env
```

### 3. Start Containers

```bash
docker compose up -d
```

### 4. Install Dependencies in Container

```bash
docker compose exec -T laravel.test composer install
docker compose exec -T laravel.test php artisan key:generate
docker compose exec -T laravel.test php artisan migrate --force
```

### 5. Frontend Build

```bash
npm install
npm run build
```

### 6. Open App

- App: `http://localhost` (or your `APP_PORT`)
- Mailpit: `http://localhost:8025` (default)

## Common Commands

```bash
# Rebuild Blade cache
docker compose exec -T laravel.test php artisan view:cache

# Clear caches
docker compose exec -T laravel.test php artisan optimize:clear

# Run tests
docker compose exec -T laravel.test php artisan test

# Check routes
docker compose exec -T laravel.test php artisan route:list
```

## Storage and Upload Notes

- Public storage is mapped to a Docker volume:
  - `sail-public-storage:/var/www/html/public/storage`
- This keeps uploaded images persistent across container restarts.
- UI image rendering now includes fallbacks to prevent broken image links on missing assets.

## Performance Notes

- Runtime tuning is mounted via:
  - `docker/php/99-performance.ini`
- Includes opcache tuning and reduced debug overhead for better route response.
- Heavy admin JS initialization was refactored to lazy-load where possible.
- Student/teacher dashboard animations use performance-safe reveal strategy:
  - `IntersectionObserver`
  - `opacity + transform` only
  - `prefers-reduced-motion` support

## Troubleshooting

### `vendor/autoload.php` missing when running `php artisan`

Cause: dependencies not installed in current runtime.

Fix:
- Use Docker flow above and run `composer install` in `laravel.test` container.

### Docker container name conflict

If you see a conflict like an existing `...-mysql-1` container:

```bash
docker ps -a
docker rm -f <conflicting-container-name-or-id>
docker compose up -d
```

### Images not showing

- Ensure `public/storage` mapping is active in `compose.yaml`.
- Recheck uploaded path data in DB.
- Fallbacks are implemented for course visuals, but invalid custom paths can still affect legacy records.

## Project Documentation

- Ongoing update log:
  - `PROJECT_UPDATE_REPORT.md`
- Route definitions:
  - `routes/web.php`
- Docker config:
  - `compose.yaml`

## License

This project currently inherits Laravel skeleton licensing context (`MIT`) unless your organization policy overrides it.
