# Senet Pathshala

Laravel-based school website and management system for Senet Pathshala.

## Current modules

- Public school website and home page
- Contact/enquiry submission
- Admin authentication and protected dashboard
- Notice management
- Gallery management with image uploads
- School information settings
- Admin enquiry management
- Responsive admin layout
- Feature tests and GitHub Actions CI

## Stack

- Laravel 12
- PHP 8.2+
- MySQL 8+
- Bootstrap 5
- Blade
- JavaScript

## Local setup

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Configure the database values in `.env`, then run:

```bash
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Open the application at `http://127.0.0.1:8000`.

### Admin

The admin area is available at `/admin/login`. Create or seed an administrator account before using the protected dashboard.

## Testing

Run the feature test suite with:

```bash
php artisan test
```

GitHub Actions also runs the Laravel test suite on pushes to `main` and pull requests targeting `main`.

## Production checklist

Before deployment:

1. Use a production `.env`; never commit secrets.
2. Set `APP_ENV=production` and `APP_DEBUG=false`.
3. Configure a persistent MySQL database.
4. Run `composer install --no-dev --optimize-autoloader`.
5. Run `php artisan migrate --force`.
6. Run `php artisan db:seed --force` when provisioning the initial admin account.
7. Run `php artisan storage:link`.
8. Run `php artisan config:cache`, `php artisan route:cache`, and `php artisan view:cache`.
9. Point the web server document root to the Laravel `public/` directory.
10. Ensure `storage/` and `bootstrap/cache/` are writable by the web-server user.
11. Configure HTTPS and regular database/storage backups.

## Project roadmap

Future modules can be added incrementally:

- Online admission workflow
- Student, parent and teacher portals
- Attendance
- Fees
- Examination and results

> This repository is being developed incrementally. A module should be treated as production-ready only after its migrations, validation, authorization, tests and deployment requirements have been verified.

<!-- CI trigger: verify latest Laravel bootstrap fixes. -->
