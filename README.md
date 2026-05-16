# Laravel Portfolio Starter Kit

A professional Laravel starter kit for portfolio and small admin projects. It includes authentication, role-based access, a dashboard, user management, project management with image uploads, filters, demo seeders and Pest tests.

## Stack

- Laravel 13
- Livewire 4
- Tailwind CSS 4
- Spatie Laravel Permission
- Pest
- Vite

## Features

- Admin dashboard with project and user metrics
- Roles: `admin`, `editor`, `user`
- User CRUD restricted to admins
- Project CRUD restricted to admins and editors
- Project image upload to the public disk
- Search and status/role filters
- Demo seeders with ready-to-use accounts
- Public portfolio landing page that lists published projects

## Demo Accounts

All demo accounts use the password `password`.

| Role | Email |
| --- | --- |
| Admin | `admin@example.com` |
| Editor | `editor@example.com` |
| User | `user@example.com` |

## Installation

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan storage:link
php artisan migrate --seed
npm run build
```

Start the development server:

```bash
composer run dev
```

Open `/login` and sign in with `admin@example.com` / `password`.

## Testing

```bash
php artisan test
```

Or run Pest directly:

```bash
vendor/bin/pest
```

## Roles And Access

- `admin`: dashboard, user CRUD, project CRUD
- `editor`: dashboard, project CRUD
- `user`: dashboard only

Role middleware aliases are configured in `bootstrap/app.php`.

## Project Images

Uploaded project images are stored on the `public` disk under `projects/`. Run `php artisan storage:link` after installation so images are available through `/storage`.

## Useful Commands

```bash
php artisan migrate:fresh --seed
php artisan test
vendor/bin/pint
npm run build
```

## License

MIT
