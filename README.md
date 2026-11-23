# TCF Canada Prep (Laravel)

A personal, private study site to reach **NCLC 7 (about CEFR B2) in TCF Canada**: a daily plan, grammar,
listening, reading, writing and speaking practice, spaced-repetition flashcards and a searchable lexicon.
Dark mode is the default (easy on the eyes) with a light-mode switch.

**Stack:** Laravel 13 (PHP 8.3+), Blade, Tailwind (compiled), about 4.5 KB of vanilla JS. No Node.js on the server.
Page weight is roughly 10 KB of CSS and JS (gzip).

**Several learners, one site:** each person creates a profile (name + PIN) and gets their own plan (start date, level,
minutes per day), progress, streak and flashcard schedule, synced to the server so it follows them to every device.
Login uses a small token cookie (only a hash is stored), PIN attempts are rate-limited, and the web server's password
still keeps strangers out.

## What is where

| Page | What it does |
| --- | --- |
| `/` Today | Day number, stage (A1 → NCLC 7), progress, and the 2-hour checklist for the day |
| `/learn`, `/r/{slug}` | Library: grammar, listening, reading, writing and speaking items by level |
| `/practice` | Flashcards with spaced repetition (schedule kept in the browser) |
| `/words` | Searchable lexicon (French, English, Hindi) with text-to-speech |
| `/tcf` | TCF Canada format, NCLC 7 target numbers and an honest timeline |
| `/level-check` | A1/A2 can-do checklists and a mini quiz |

Progress, streak and card schedule are cached in the browser and synced to the server per learner (`learner_states`).

## The plan engine

`config/tcf.php` holds the default start date, minutes per day and the hours per stage (about 470 h from zero to NCLC 7).
`App\Support\Plan` turns a learner's own settings (start date, minutes, "credit" hours for a higher starting level) into
"day N, stage X". Defaults for new profiles come from `TCF_START_DATE` / `TCF_DAILY_MINUTES` / `TCF_WISH_MONTHS`; `TCF_MAX_LEARNERS` limits profiles.

## Daily resources (automation)

A scheduled job adds new material every day through the API (bearer key `HERMES_API_KEY`):

- `GET  /api/v1/plan/today` : the levels learners are on today (`levels_needed`), quotas, and what already exists per level (avoids repeats). No names or PINs
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
