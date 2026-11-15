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
| `HERMES_API_KEY` | Bearer token for `POST /api/v1/vocabulary` (ingestion is disabled while unset) |

## API
- `GET /api/v1/vocabulary` public lexicon; `POST` requires `Authorization: Bearer <HERMES_API_KEY>`
- `POST /api/v1/admissions` public application form; `GET`/`PATCH` are admin only
- `GET /api/v1/status` health and content counts

> Applications and ingested words are held in memory and reset when the server restarts.
> Attach a database before relying on them in production.
