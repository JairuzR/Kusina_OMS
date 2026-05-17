# KusinaOMS — Restaurant Operations Management System

A web-based system that digitizes restaurant floor operations for small restaurants in the Philippines. Built with Laravel 13, Tailwind CSS, and MySQL.

## Problem Being Solved

Small restaurants rely on manual processes — handwritten orders, verbal kitchen communication, paper inventory — causing lost orders, stockouts, and inaccurate sales tracking. KusinaOMS digitizes the entire operation.

## Tech Stack

- **Backend:** Laravel 13, PHP 8.3
- **Frontend:** Blade Templates, Tailwind CSS, Alpine.js, Chart.js
- **Database:** MySQL 8.x
- **Auth:** Laravel Breeze + Spatie Laravel Permission
- **PDF:** barryvdh/laravel-dompdf
- **AI:** Google Gemini API + Ollama (llama3.2) fallback
- **Queue:** Laravel Database Queue

## Features

- Role-based access control (Admin, Manager, Cashier, Waiter, Kitchen Staff)
- Order management with live kitchen display
- Table management with visual floor map
- Reservation system
- Menu management with categories and item toggling
- Inventory tracking with stock adjustments and supplier management
- AI-powered reorder suggestions (Gemini + Ollama)
- Audit logging for all system actions
- Notifications system with bell dropdown
- Reports with charts and CSV/PDF export
- PDF receipt generation (thermal 80mm layout)
- Site settings management
- Backup system with manual trigger and email notification
- Import/Export via CSV templates

## Installation

```bash
git clone https://github.com/yourusername/kusina-oms.git
cd kusina-oms

composer install
npm install

cp .env.example .env
php artisan key:generate

# Configure your database in .env
php artisan migrate --seed

npm run build
php artisan storage:link
```

## Environment Variables

```env
DB_DATABASE=kusina_oms
DB_USERNAME=root
DB_PASSWORD=

GEMINI_API_KEY=your_key_here
GEMINI_MODEL=gemini-2.0-flash

OLLAMA_ENABLED=true
OLLAMA_BASE_URL=http://localhost:11434
OLLAMA_MODEL=llama3.2

QUEUE_CONNECTION=database
MAIL_MAILER=smtp
```

## Queue Worker (required for AI suggestions)

```bash
php artisan queue:work --queue=ai-tasks
```

## Test Accounts

| Role    | Email                 | Password |
| ------- | --------------------- | -------- |
| Admin   | admin@kusinaoms.com   | password |
| Manager | manager@kusinaoms.com | password |
| Cashier | cashier@kusinaoms.com | password |
| Waiter  | waiter@kusinaoms.com  | password |
| Kitchen | kitchen@kusinaoms.com | password |

## Project Structure

app/
├── Console/Commands/ # BackupDatabase scheduled command
├── Http/Controllers/ # All feature controllers
├── Jobs/ # GenerateReorderSuggestion queue job
├── Mail/ # BackupCompletedMail
├── Models/ # Eloquent models
├── Notifications/ # GeneralNotification
├── Providers/ # AppServiceProvider
└── Services/ # AIServiceManager, InventoryAIService
resources/views/
├── audit-logs/
├── backups/
├── import/
├── inventory/
├── layouts/ # app.blade.php (main layout)
├── menu/
├── notifications/
├── orders/
├── pdf/ # PDF templates
├── reports/
├── reservations/
├── settings/
├── tables/
└── users/
