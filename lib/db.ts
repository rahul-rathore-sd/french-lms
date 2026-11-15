import "server-only";
import { Pool } from "pg";

/**
 * Optional PostgreSQL connection. When DATABASE_URL is not set the app falls back
 * to in-memory storage (see lib/store.ts), so local development needs no database.
 */
export const hasDatabase = Boolean(process.env.DATABASE_URL);

declare global {
  // eslint-disable-next-line no-var
  var __pgPool: Pool | undefined;
  // eslint-disable-next-line no-var
  var __pgReady: Promise<void> | undefined;
}

function createPool(): Pool {
  const url = process.env.DATABASE_URL as string;
  const local = /@(localhost|127\.0\.0\.1|\[::1\])[:/]/.test(url);
  const sslOff = process.env.DATABASE_SSL === "false";
  return new Pool({
    connectionString: url,
    max: 5,
    idleTimeoutMillis: 30_000,
    ssl: local || sslOff ? undefined : { rejectUnauthorized: false },
  });
}

export function pool(): Pool {
  if (!hasDatabase) throw new Error("DATABASE_URL is not configured");
  return (globalThis.__pgPool ??= createPool());
}

const SCHEMA = `
CREATE TABLE IF NOT EXISTS admissions (
  id            SERIAL PRIMARY KEY,
  full_name     TEXT NOT NULL,
  email         TEXT NOT NULL,
  target_cefr   TEXT NOT NULL DEFAULT 'B1',
  academic_background TEXT NOT NULL DEFAULT '',
  motivation    TEXT NOT NULL,
  status        TEXT NOT NULL DEFAULT 'pending',
  submitted_at  TIMESTAMPTZ NOT NULL DEFAULT now(),
  reviewed_at   TIMESTAMPTZ,
  rejection_reason TEXT
);
CREATE UNIQUE INDEX IF NOT EXISTS admissions_email_key ON admissions (lower(email));

CREATE TABLE IF NOT EXISTS vocabulary (
  id            SERIAL PRIMARY KEY,
  french_word   TEXT NOT NULL,
  data          JSONB NOT NULL,
  created_at    TIMESTAMPTZ NOT NULL DEFAULT now()
);
CREATE UNIQUE INDEX IF NOT EXISTS vocabulary_word_key ON vocabulary (lower(french_word));
`;

/** Creates tables on first use (idempotent). */
export function ensureSchema(): Promise<void> {
  if (!globalThis.__pgReady) {
    globalThis.__pgReady = pool()
      .query(SCHEMA)
      .then(() => undefined)
      .catch((err) => {
        globalThis.__pgReady = undefined; // retry on next request
        throw err;
      });
  }
  return globalThis.__pgReady;
}
