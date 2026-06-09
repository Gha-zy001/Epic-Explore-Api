# Epic Explore API

A Laravel 12 backend API powering a gamified travel-exploration platform. Features include
a full gamification engine (XP, levels, quests, rewards), content modules
(places, hotels, restaurants, banks, trips, reviews, favorites), AI-powered recommendations
via Google Gemini, Cloudinary media management, and a full **Filament v3 admin panel**
with **CSV import** for content.

---

## Features

- **User Accounts & Authentication** — Sanctum-token auth for two roles (User, Guider).
- **Admin Panel (Filament v3)** — Full CRUD for every model, role-based access control,
  dashboard stats, charts, and a System Control page for cache/migration/seed actions.
- **CSV Import** — Built-in Filament importer for `states`, `places`, `hotels`,
  `restaurants`, and `banks`. Sample CSVs in `storage/app/import-samples/`.
- **Gamification Engine** — XP, level progression, streaks, daily/epic quests, reward logs.
- **AI-powered Recommendations** — Google Gemini integration with fallback heuristics.
- **Cloudinary Media** — Trip image uploads via Cloudinary SDK.
- **OTP Verification** — Cryptographically secure (`random_int`), per-identifier attempt
  cap, configurable TTL.
- **Rate Limiting** — `throttle` middleware on every auth endpoint.
- **Automated API Documentation** — Scribe (knuckleswtf/scribe).
- **CI Pipeline** — GitHub Actions workflow: Pint + PHPUnit + PHPStan.

---

## Stack

- **Backend:** Laravel 12, PHP 8.2
- **Admin:** Filament 3.2, Spatie Laravel-Permission 6
- **Auth:** Laravel Sanctum
- **DB:** MySQL 8 / PostgreSQL 14+ (also supports SQLite for testing)
- **Storage:** Cloudinary (images) + public disk (local)
- **AI:** Google Gemini (`gemini-1.5-flash`)
- **Tests:** PHPUnit 11
- **Deploy:** Docker (richarvey/nginx-php-fpm) + Vercel (serverless PHP 0.6.1)

---

## Getting Started

### Prerequisites

- PHP >= 8.2
- Composer 2
- MySQL 8 or PostgreSQL 14+
- Node.js 18+ (for Vite assets, optional for API-only use)
- Git

### Installation

```bash
git clone <repository-url>
cd Epic-Explore-Api
composer install
cp .env.example .env
php artisan key:generate
```

Edit `.env` and set:

```env
APP_NAME=EpicExplore
APP_URL=http://localhost:8000
APP_DEBUG=false   # never true in production

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=epic_explore
DB_USERNAME=root
DB_PASSWORD=

# Cloudinary (rotate any leaked key in your dashboard)
CLOUDINARY_CLOUD_NAME=...
CLOUDINARY_API_KEY=...
CLOUDINARY_API_SECRET=...
CLOUDINARY_FOLDER=laravel-cloud

# AI
GEMINI_API_KEY=...

# Admin panel
ADMIN_PANEL_PATH=admin
ADMIN_EMAIL=admin@epicexplore.test
ADMIN_PASSWORD=ChangeThisNow!
```

### Run migrations + seed

```bash
php artisan migrate --seed
php artisan storage:link
```

### Install the Filament admin panel

```bash
# Linux / macOS
bash scripts/install-filament.sh

# Windows (PowerShell)
.\scripts\install-filament.ps1
```

Then visit: **http://localhost:8000/admin**

### Start the dev server

```bash
php artisan serve
# API:  http://localhost:8000/api
# Docs: http://localhost:8000/docs
# Admin: http://localhost:8000/admin
```

---

## Admin Panel (Filament v3)

Navigate to `/admin` (or your custom `ADMIN_PANEL_PATH`). Login with the credentials you
seeded in `AdminSeeder`.

### Sections

| Group | Resources |
|---|---|
| **Content** | States, Places, Hotels, Restaurants, Banks — each with CSV import |
| **Gamification** | Quests, Visits, Reward Logs |
| **Community** | Users, Guiders, Trips, Reviews, Contacts, Favorites |
| **System** | Admins, Roles, OTPs, **System Control** page |

### CSV Import

Sample files in `storage/app/import-samples/`:

- `places.csv` → `name,state,description,address`
- `hotels.csv` → `name,state,address,price,rate`
- `restaurants.csv` → `name,state,address,rate`
- `banks.csv` → `name,state,location,rate`
- `states.csv` → `name,description`

To import:
1. Open the resource (e.g. **Places**) in `/admin`.
2. Click the **Import** button above the table.
3. Upload your CSV / XLSX.
4. Watch the progress bar; you'll get a notification with the result.

Each importer is "upsert-style" — it matches by `(name, state_id)` so re-imports update
existing rows instead of duplicating.

### Roles & Permissions

Three roles are seeded:

- `super-admin` — every permission.
- `admin` — every permission (no role/user management).
- `moderator` — read-only on most resources, can approve contacts and delete reviews.

Manage in **System → Roles**.

### System Control

The **System Control** page (System → System Control) lets you:

- Clear all caches (application, config, routes, views).
- Flush the OTP / cache store.
- Optimize the application for production.
- Run migrations and seeders.
- Regenerate Scribe API documentation.

---

## API

The API base path is `/api`. All `/api/user/...` and `/api/guider/...` endpoints are
documented in the Postman collection and via Scribe.

### Authentication (User)

```http
POST /api/user/register
POST /api/user/login            (rate-limited 5/min)
POST /api/user/logout           (auth:sanctum)
POST /api/user/forgot_password  (rate-limited 3/min)
POST /api/user/verify-otp       (Guider only, rate-limited 5/min)
```

All `auth:sanctum` endpoints require `Authorization: Bearer <token>`.

### CSV sample

```csv
name,state,description,address
Pyramids of Giza,Cairo,Ancient wonder,Al Haram Giza
```

---

## Security

- **Cloudinary credentials** must be set in `.env` (never hard-coded in source).
  The leaked key from previous versions has been rotated.
- `APP_DEBUG` is `false` in `vercel.json` for production deploys.
- All auth endpoints have rate limiting (`throttle:5,1` or `throttle:3,1`).
- Trip / image uploads are scoped to the authenticated user (IDOR-safe).
- OTP generator uses `random_int()` and is capped at 5 attempts per identifier.
- Passwords are hashed via Laravel's `Hash` cast.
- Sanctum tokens are stored in `personal_access_tokens` and can be revoked.

---

## Testing

```bash
php artisan test
```

PHPUnit runs against an in-memory SQLite database. Coverage includes:

- `tests/Feature/ExplorationTest.php` — check-in, XP, leaderboard, quests, AI.
- `tests/Feature/GuiderAuthTest.php` — registration + OTP verification.
- `tests/Unit/OtpServiceTest.php` — token generation, attempt cap.

Run lint:

```bash
vendor/bin/pint --test
```

Run static analysis:

```bash
vendor/bin/phpstan analyse
```

CI runs all three (Pint + PHPUnit + PHPStan) on PHP 8.2 and 8.3 in `.github/workflows/ci.yml`.

---

## Postman

A full Postman collection is in the project root: `epic_explore_api_full.postman_collection.json`.

---

## License

MIT.
