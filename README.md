# TCF Canada Prep (Laravel)

A personal, private study site to reach **NCLC 7 (about CEFR B2) in TCF Canada**: a daily plan, grammar,
listening, reading, writing and speaking practice, spaced-repetition flashcards and a searchable lexicon.
Dark mode is the default (easy on the eyes) with a light-mode switch.

**Stack:** Laravel 13 (PHP 8.3+), Blade, Tailwind (compiled), about 4 KB of vanilla JS. No Node.js on the server,
no sessions, no cookies. Page weight is roughly 9 KB of CSS and JS (gzip).

## What is where

| Page | What it does |
| --- | --- |
| `/` Today | Day number, stage (A1 → NCLC 7), progress, and the 2-hour checklist for the day |
| `/learn`, `/r/{slug}` | Library: grammar, listening, reading, writing and speaking items by level |
| `/practice` | Flashcards with spaced repetition (schedule kept in the browser) |
| `/words` | Searchable lexicon (French, English, Hindi) with text-to-speech |
| `/tcf` | TCF Canada format, NCLC 7 target numbers and an honest timeline |
| `/level-check` | A1/A2 can-do checklists and a mini quiz |

Progress, streak and card schedule are stored in the browser (`localStorage`), so the server is stateless.

## The plan engine

`config/tcf.php` holds the start date, minutes per day and the hours per stage (about 470 h from zero to NCLC 7).
`App\Support\Plan` turns that into "day N, stage X". Change `TCF_START_DATE` / `TCF_DAILY_MINUTES` in `.env`.

## Daily resources (automation)

A scheduled job adds new material every day through the API (bearer key `HERMES_API_KEY`):

- `GET  /api/v1/plan/today` : today's stage, quotas and what already exists (avoids repeats)
- `POST /api/v1/materials`  : add one item. `kind` is `grammar | reading | listening | writing | speaking | vocab` (validated strictly)
- `GET  /api/v1/materials`, `DELETE /api/v1/materials/{slug}`, `GET|POST /api/v1/vocabulary`

Audio is generated in the browser with text-to-speech, so no audio files are stored.

## Run locally

```bash
composer install && npm install
cp .env.example .env && php artisan key:generate
php artisan french:keys          # prints HERMES_API_KEY for .env
php artisan migrate --seed       # SQLite by default; seeds grammar, exercises, tasks and vocabulary
npm run build                    # compiles CSS/JS into public/build (committed: the server has no Node)
php artisan serve
php artisan test
```

## Deploy (VPS / CloudPanel, PHP 8.3)

```bash
composer install --no-dev --optimize-autoloader
cp .env.example .env && php artisan key:generate   # then set APP_ENV=production, APP_DEBUG=false, DB_*, SITE_URL, HERMES_API_KEY, TCF_START_DATE
php artisan migrate --force --seed
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

Web root is `public/`. The site is private by default (`FRENCH_NOINDEX=true`: `X-Robots-Tag: noindex`, `robots.txt` disallow-all);
put it behind a password or an IP allow-list on the web server.

## Notes

- Exam numbers (`resources/data/*.json`) come from published IRCC / France Éducation international bands. Always re-check them on the official pages before booking.
- Starter content was exported from the earlier version of this project (`database/seeders/data/*.json`).
