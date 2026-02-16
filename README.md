# Rivek Men's Salon - Queue Management System

A real-time token-based queue management system for Rivek Men's Salon, Ahmedabad. Customers get tokens, staff manages the queue, and a live display shows who's being served.

## Pages

| Page | URL | Description |
|------|-----|-------------|
| Customer Token | `index.php` | Customers enter name & phone to get a queue token |
| Queue Display | `display.php` | TV/monitor screen showing now-serving and waiting list (auto-refreshes) |
| Staff Dashboard | `staff.php` | Password-protected admin panel to manage the queue |
| Login | `login.php` | Staff authentication page |

## Features

- **4-chair system** — up to 4 customers can be served simultaneously
- **Real-time updates** — display and staff pages auto-refresh
- **Queue actions** — call next, call specific, mark done, no-show, back-to-queue
- **Audio chime** — display page plays a sound when a new customer is called
- **Password-protected admin** — staff page requires login via `.env` password
- **SEO blocked** — all pages have `noindex/nofollow` meta tags + `robots.txt`

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

### 2. Environment

Create a `.env` file in the project root:

```
ADMIN_PASSWORD=YourStaffPassword
```

This password is used for the staff dashboard login.

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
