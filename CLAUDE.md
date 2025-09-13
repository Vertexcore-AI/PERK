# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 12 application using:
- PHP 8.2+
- Vite for asset bundling
- Tailwind CSS 4
- SQLite as the default database
- Queue system with database driver

## Essential Commands

### Development
```bash
# Start all development services (server, queue worker, and vite)
composer run dev

# Or start services individually:
php artisan serve           # Start development server
php artisan queue:listen    # Start queue worker
npm run dev                 # Start Vite dev server
```

### Build & Testing
```bash
# Build frontend assets
npm run build

# Run tests
composer test
# Or directly:
php artisan test

# Code formatting (Laravel Pint)
./vendor/bin/pint

# Run specific test file
php artisan test tests/Feature/ExampleTest.php
php artisan test tests/Unit/ExampleTest.php
```

### Database
```bash
# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Fresh migration (drop all tables and re-run)
php artisan migrate:fresh

# Seed database
php artisan db:seed
```

### Common Artisan Commands
```bash
# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Generate application key
php artisan key:generate

# Create controllers, models, migrations
php artisan make:controller ControllerName
php artisan make:model ModelName -m  # with migration
php artisan make:migration create_table_name

# Tinker (REPL)
php artisan tinker
```

## Architecture

### Directory Structure
- `app/Http/Controllers` - HTTP controllers
- `app/Models` - Eloquent models
- `app/Providers` - Service providers
- `routes/web.php` - Web routes
- `routes/console.php` - Console commands
- `database/migrations` - Database migrations
- `database/seeders` - Database seeders
- `resources/views` - Blade templates
- `resources/js` - JavaScript files
- `resources/css` - CSS files (Tailwind)
- `public/` - Public assets and entry point
- `tests/Feature` - Feature tests
- `tests/Unit` - Unit tests

### Configuration
- Database: SQLite (configured in `.env`)
- Queue: Database driver
- Session: Database driver
- Cache: Database driver
- Mail: Log driver (development)

### Frontend Build
The project uses Vite with Laravel plugin for asset compilation. Configuration is in `vite.config.js`. Tailwind CSS 4 is configured with the new Vite plugin approach.