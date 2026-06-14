# Hospital Management System

Laravel 10 based HMS for diagnostic centers and hospitals — invoices/lab, OPD, IPD/admits, pharmacy POS, finance, HR/attendance, and reports.

## Requirements

- PHP 8.1+
- MySQL 8+
- Composer
- Node.js 18+ (optional — admin UI uses static assets in `public/backend/assets/`)

## Quick setup

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan storage:link
```

Default admin login is created by seeders (`RolePermissionSeeder`). Check `database/seeders/` for credentials after seeding.

Admin panel: `/admin/login`

## Main modules

| Module | Path prefix | Notes |
|--------|-------------|--------|
| Dashboard (live) | `/admin` | Real-time KPIs, patient intelligence |
| Diagnostic / Lab | `/admin/invoices`, `/admin/labs` | Invoices, test reports |
| OPD | `/admin/doctor-serials` | Doctor serial queue |
| Hospital / IPD | `/admin/admits`, `/admin/recepts` | Admissions, hospital billing |
| Pharmacy | `/admin/pharmacy-*` | Stock, purchases, POS sales |
| Finance | `/admin/costs`, `/admin/earns`, `/admin/payments` | Costs, earnings, payments |
| Reports | `/admin/reports/*` | Collections, balance, references, PDF export |
| HR | `/admin/employees`, `/attendance` | Employees, leave, attendance |

## Permissions

Uses [Spatie Laravel Permission](https://github.com/spatie/laravel-permission) on the `admin` guard. Run `php artisan db:seed --class=RolePermissionSeeder` after migrations.

## Subdomain multi-tenant (optional)

Tenant databases are configured in `config/subdomain.php` — add each hospital subdomain there:

```php
'alsunnah' => [
    'database' => 'dreammak_alsunnah',
    'username' => 'dreammak_alsunnah',
    'password' => 'dreammak_alsunnah',
    'host' => '127.0.0.1',
],
```

Unknown subdomains use the `default` block (falls back to `DB_*` from `.env`). Local `127.0.0.1` uses the `127` entry.

## Patient 360

- Navbar search: type patient name or phone (2+ chars)
- Full profile: `/admin/patients/profile?phone=...` or `?user_id=...`
- From patient list: click the ID card icon

## Maintenance (Super Admin only)

- Clear cache: `POST /admin/system/clear-cache`
- Install HR schema columns: `POST /admin/system/install-hr-schema`

## Development

```bash
php artisan serve
composer test   # or: php artisan test
./vendor/bin/pint
```

## Security notes

- Public device routes (`/fingerprint-*`, `/attendance-api/*`) are for hardware integration — restrict by network/firewall in production.
- Never expose `/admin` without HTTPS in production.
- Run pending migrations before deploy (including pharmacy `status` column migration if present).

## License

MIT
