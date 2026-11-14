# Académie de Français · Classical French LMS & Cultural Sanctuary 🇨🇦

A bilingual French learning management system and cultural sanctuary engineered for deep linguistic fluency, classical literature immersion (A1–C2), and continuous vocabulary ingestion.

---

## 🏛️ Key Features

- **CEFR Structured Syllabus (A1–C2)**:
  - From A1 acoustic foundations to C2 administrative rhetoric and classical philosophical disquisitions.
  - Interactive quizzes with instant acoustic validation and progress tracking.
- **Classical Immersion Vocabulary Sanctuary**:
  - French terms with IPA pronunciation and Web Speech API audio synthesis.
  - Bilingual academic definitions (**English & Hindi / हिन्दी अर्थ**).
  - Practical usage guides (**Use Cases**) explaining exact situational and conversational context.
- **Academic Administration Console**:
  - Comprehensive dashboard at `/admin` for curriculum, scholar management, and lexicon updates.
  - Live editing of Hindi translations and usage guides.
- **Hermes Autonomous Ingestion API**:
  - Secure REST endpoint (`/api/v1/vocabulary`) authenticated via token header.
  - Ingests daily batch vocabulary directly from autonomous background agents.
- **Modern Scholarly Aesthetic**:
  - Canadian Maple Leaf crest and official palette (`#D52B1E`).
  - Single-click Dark Mode persistence.
  - Active 7-day academic streak system.
  - Zero clutter, clean typography (`Plus Jakarta Sans` & `Merriweather`).

---

## 🚀 Deployment on Render

This repository is pre-configured with a production-ready `Dockerfile` and `render.yaml` Blueprint.

### Deploy in 3 Steps:
1. Link this repository in your [Render Dashboard](https://dashboard.render.com).
2. Choose **Web Service** -> **Docker Runtime** (or apply the included `render.yaml` Blueprint).
3. Set the following Environment Variables in Render:
   - `APP_KEY`: Generate via `php artisan key:generate --show`
   - `HERMES_API_KEY`: Secret API token for Hermes background sync
   - `APP_ENV`: `production`
   - `APP_DEBUG`: `false`
   - `SESSION_DRIVER`: `cookie`

The container will automatically execute migrations and seed the initial classical curriculum on first boot.

---

## 🔒 Security & Privacy Notice
- No sensitive API keys or database files are committed to this repository.
- Environment secrets are loaded exclusively at runtime via environment variables.
- Password hashes use high-entropy bcrypt encryption.
