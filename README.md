# Académie de Français (Laravel)

A French learning platform (CEFR courses A1–C1, grammar, flashcards, lexicon with English/Hindi, exam prep, books)
with a small admin console, a blog and an API for the **Hermes** agent.

**Runtime: Laravel 13 (PHP 8.3+) + MySQL.** No Node.js is needed on the server.

## How it is put together

| Part | Where | What it does |
| --- | --- | --- |
| Backend, API, admin auth, blog, sitemap | `app/`, `routes/`, `database/`, `resources/views/blog` | Laravel. API lives at `/api/v1/*` |
| Learner UI (courses, library, flashcards, admin screens) | `frontend/` → built into `public/` | Source is a Next.js app that is exported **once** to plain static files. The result is committed in `public/`, so deploying never needs Node |
| Public blog (`/blog/`, `/blog/{slug}/`) and `/sitemap.xml` | Laravel + Blade | Server-rendered for SEO |

The API keeps the original contract (`/api/v1/leads`, `blog`, `books`, `vocabulary`, `clients`, `social`, `emails`, `admin/*`, `stats`, `status`), so the UI and the Hermes agent work unchanged.

Auth: the admin signs in at `/admin/` (password hash in `.env`) and gets a session cookie; Hermes uses `Authorization: Bearer <HERMES_API_KEY>`. Hermes can only draft emails, an admin must approve them.

## Run locally

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan french:keys          # prints ADMIN_PASSWORD_HASH and HERMES_API_KEY for .env (and the admin password)
php artisan migrate --seed       # uses SQLite by default (database/database.sqlite)
php artisan serve
```

## Deploy (VPS / CloudPanel, PHP 8.3)

```bash
git clone <this repo> /home/<site-user>/htdocs/<domain>   # or pull to update
cd /home/<site-user>/htdocs/<domain>
composer install --no-dev --optimize-autoloader
cp .env.example .env && php artisan key:generate
# edit .env: APP_ENV=production, APP_DEBUG=false, APP_URL, SITE_URL, DB_*, SESSION_SECURE_COOKIE=true,
#            then run: php artisan french:keys  and paste the two values
php artisan migrate --force --seed
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

Web root must be `public/`. Make `storage/` and `bootstrap/cache/` writable by the PHP user.

## Changing the learner UI

```powershell
cd frontend
npm ci
npm run dev                       # edit app/, components/, lib/
cd ..
powershell -File scripts\build-frontend.ps1   # builds and copies the result into public/
```

## Notes

- Book PDFs are **not** in the repository (copyright). Put them in `public/books/` using the file names listed in `frontend/lib/library/books.json`; the cover images are included.
- Secrets live only in `.env` (git-ignored). Never commit `.env`.
- Next step for a full Laravel UI: replace the exported pages with Blade/Livewire page by page. The API and database do not need to change for that.
