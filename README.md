# Rivek Men's Salon - Queue Management System

A real-time token-based queue management system for Rivek Men's Salon, Ahmedabad. Customers get tokens, staff manages the queue, and a live display shows who's being served.

## Pages

| Page | URL | Description |
|------|-----|-------------|
| Customer Token | `index.php` | Customers enter name & phone to get a queue token |
| Queue Display | `display.php` | TV/monitor screen showing now-serving and waiting list (auto-refreshes) |
| Staff Dashboard | `staff.php` | Password-protected admin panel to manage the queue |
| Admin Panel | `admin.php` | Admin-only staff and service/pricing CRUD (icon-first, Tailwind CDN UI) |
| Login | `login.php` | Staff authentication page |

Pretty URLs (via `.htaccess`):

- `domain/` → customer page
- `domain/admin` → admin panel (auto-redirects to login if not authenticated)
- `domain/display` → queue display

## Features

- **4-chair system** — up to 4 customers can be served simultaneously
- **Real-time updates** — display and staff pages auto-refresh
- **Queue actions** — call next, call specific, mark done, no-show, back-to-queue
- **Audio chime** — display page plays a sound when a new customer is called
- **Password-protected admin** — staff page requires login via `.env` password
- **SEO blocked** — all pages have `noindex/nofollow` meta tags + `robots.txt`
- POS schema auto-bootstrap: POS/admin APIs auto-create missing POS tables and seed defaults if not present (for safer first run).
- Admin/staff icon UI uses Lucide icons via CDN (`https://lucide.dev/`).

## Setup

### 1. Database

Create a MySQL database, then copy and configure the config file:

```bash
cp config.example.php config.php
```

Edit `config.php` with your MySQL credentials (host, database, user, password).

Run the setup script to create the `tokens` table:

```bash
php db_setup.php
```


### Migration workflow (recommended)

You can keep using `database.sql` / `db_setup.php`, but now you also have versioned migrations:

```bash
php migrate.php
```

Rollback last batch:

```bash
php migrate_rollback.php
```

Migration files are in `db/migrations/`.

### 2. Environment

Create a `.env` file in the project root:

```
ADMIN_PASSWORD=YourAdminPassword
STAFF_PASSWORD=YourStaffPassword
```

Admin and staff can use the same login URL. Role is decided by password.

### 3. Deploy

Upload all files to your PHP hosting. The project requires:

- PHP 7.4+
- MySQL 5.7+
- Apache with `mod_rewrite` (for `.htaccess` rules)

### File Structure

```
salon/
├── index.php              # Customer-facing token page
├── display.php            # Queue display for TV/monitor
├── staff.php              # Staff dashboard (protected)
├── login.php              # Staff login page
├── api.php                # JSON API endpoint
├── config.php             # Database credentials (gitignored)
├── config.example.php     # Template for config.php
├── auth.php               # Session auth helper
├── db_setup.php           # Database table setup script
├── migrate.php            # Run pending migrations
├── migrate_rollback.php   # Rollback last migration batch
├── robots.txt             # Block search engine crawling
├── .env                   # Admin password (gitignored)
├── .htaccess              # Apache security rules
└── models/
    ├── Model.php          # Base ORM class
    └── Token.php          # Token model with domain methods
```

## API Endpoints

All endpoints go through `api.php?action=<action>`:

| Action | Method | Body | Description |
|--------|--------|------|-------------|
| `create_token` | POST | `{name, phone}` | Create a new queue token |
| `get_queue` | GET | — | Get serving and waiting lists |
| `next` | POST | — | Call the next waiting customer |
| `call_specific` | POST | `{id}` | Call a specific waiting customer |
| `done` | POST | `{id}` | Mark a serving customer as done |
| `noshow` | POST | `{id}` | Mark a serving customer as no-show |
| `back_to_queue` | POST | `{id}` | Send a serving customer back to the queue |
| `stats` | GET | — | Get counts (total, waiting, serving, done, no-show) |
| `get_staff` | GET | — | Get active staff list for POS |
| `get_services` | GET | — | Get active services catalog for POS |
| `create_sale` | POST | `{token_id, staff_id, items, discount?, tax?, payment_method}` | Create POS bill |
| `get_sale` | GET | `sale_id` query | Fetch bill header + items |
| `daily_sales` | GET | — | Daily sales totals + breakdown by service |
| `staff_sales` | GET | — | Daily sales grouped by staff |

| `admin_list_staff` | GET | — | Admin-only staff listing |
| `admin_create_staff` | POST | `{name, icon}` | Admin-only create staff |
| `admin_update_staff` | POST | `{id, ...}` | Admin-only update/toggle staff |
| `admin_list_services` | GET | — | Admin-only service listing |
| `admin_create_service` | POST | `{name, price, icon}` | Admin-only create service |
| `admin_update_service` | POST | `{id, ...}` | Admin-only update/toggle service |


## Architecture direction (Laravel-style)

Laravel primarily follows **MVC** with clear layer separation:

- **Routes/Controller**: request entry + orchestration
- **Model**: database entities and persistence
- **Service layer** (optional but recommended): business rules/workflows
- **Policies/Middleware**: authorization and role checks

This project now mirrors that direction in lightweight PHP by:
- Keeping API actions in `api.php` as controller-like handlers.
- Keeping DB schema and table responsibilities separate (`tokens` for queue, `sales*` for POS).
- Enforcing role-based access for admin CRUD (staff/services) via `auth.php` guards.


## CI/CD with migrations

GitHub Actions now includes:

- **CI workflow** (`.github/workflows/ci.yml`):
  - runs PHP lint checks
  - runs `php migrate.php`
  - verifies idempotency by running migrations again
  - rolls back last batch and reapplies migrations

- **CD workflow** (`.github/workflows/deploy.yml`):
  - runs `php migrate.php` before FTP deploy when DB secrets are configured.

Required deploy secrets for migration step:
- `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`
- `ADMIN_PASSWORD`, `STAFF_PASSWORD`

