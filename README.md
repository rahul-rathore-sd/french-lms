# Académie de Français

A premium French learning platform: CEFR courses (A1–C1), native-quality audio,
spaced-repetition flashcards and a lexicon with English and Hindi translations.

**Stack:** Next.js 15 (App Router) · TypeScript · Tailwind CSS · Clerk auth · Resend email

## Features
- Courses and lessons with audio (normal and slow), quizzes and next-lesson flow
- XP, levels, day streak and daily goal, saved in the browser (`localStorage`)
- `/practice` flashcards scheduled with an SM-2 style algorithm (keys: Space, 1–4, P)
- Lexicon search by French, English or Hindi, filtered by level and category
- Light and dark themes, responsive layout
- Admin console (`/admin`) to review applications, restricted to admins
- Hermes ingestion API to add words automatically

## Getting started
```bash
npm install
cp .env.example .env.local   # then fill in your own values
npm run dev
```

## Configuration
All secrets live in environment variables (see `.env.example`). Never commit `.env*` files.

| Variable | Purpose |
| --- | --- |
| `NEXT_PUBLIC_CLERK_PUBLISHABLE_KEY`, `CLERK_SECRET_KEY` | Clerk authentication |
| `CLERK_WEBHOOK_SECRET` | Verifies Clerk webhooks (`/api/webhooks/clerk`) |
| `RESEND_API_KEY`, `RESEND_FROM_EMAIL` | Welcome emails |
| `NEXT_PUBLIC_SITE_URL` | Public URL used in emails |
| `ADMIN_EMAILS` | Comma-separated emails allowed into `/admin` (or set Clerk `publicMetadata.role = "admin"`) |
| `DATABASE_URL` | PostgreSQL connection string. Applications and ingested words persist here (tables are created automatically) |
| `HERMES_API_KEY` | Bearer token for `POST /api/v1/vocabulary` (ingestion is disabled while unset) |

## API
- `GET /api/v1/vocabulary` public lexicon; `POST` requires `Authorization: Bearer <HERMES_API_KEY>`
- `POST /api/v1/admissions` public application form; `GET`/`PATCH` are admin only
- `GET /api/v1/status` health and content counts

## Database
Set `DATABASE_URL` to any PostgreSQL database and the app creates its tables on first use
(`admissions`, `vocabulary`). Without it, data is kept in memory and is lost on restart,
which is fine for local development.

On Render: create a **PostgreSQL** instance, copy its **Internal Database URL** and add it to the
web service as `DATABASE_URL`. Learner progress (XP, streaks, flashcards) is stored in the
learner's browser, not in the database.

## Deploying on Render
This repo includes a `Dockerfile` (Next.js standalone build), so a Render **Docker** web service works out of the box.

1. Web Service, Runtime **Docker**, branch `main`, auto-deploy on.
2. Add the environment variables listed above. `NEXT_PUBLIC_*` values are baked into the browser bundle at **build** time, so set them before the first deploy and trigger a new deploy if you change them.
3. The container listens on `$PORT` (default 10000).

If the dashboard shows an old version of the site, open the service **Events/Logs** in Render: a failed build keeps the last successful deploy online.
