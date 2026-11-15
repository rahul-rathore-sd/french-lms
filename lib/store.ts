import "server-only";
import { hasDatabase, pool, ensureSchema } from "./db";
import { initialAdmissionRequests, initialVocabulary } from "./data";
import type { AdmissionRequest, VocabularyWord } from "./types";

/* In-memory fallback (used only when DATABASE_URL is not set) ------------- */
const memAdmissions: AdmissionRequest[] = [...initialAdmissionRequests];
const memVocabulary: VocabularyWord[] = [];

type AdmissionRow = {
  id: number;
  full_name: string;
  email: string;
  target_cefr: AdmissionRequest["target_cefr"];
  academic_background: string;
  motivation: string;
  status: AdmissionRequest["status"];
  submitted_at: Date;
  reviewed_at: Date | null;
  rejection_reason: string | null;
};

const toAdmission = (r: AdmissionRow): AdmissionRequest => ({
  id: r.id,
  full_name: r.full_name,
  email: r.email,
  target_cefr: r.target_cefr,
  academic_background: r.academic_background,
  motivation: r.motivation,
  status: r.status,
  submitted_at: r.submitted_at.toISOString(),
  reviewed_at: r.reviewed_at?.toISOString(),
  rejection_reason: r.rejection_reason ?? undefined,
});

export const storageMode = hasDatabase ? "postgres" : "memory";

/* Admissions --------------------------------------------------------------- */

export async function listAdmissions(status?: string | null): Promise<AdmissionRequest[]> {
  if (!hasDatabase) {
    return status && status !== "all" ? memAdmissions.filter((r) => r.status === status) : [...memAdmissions];
  }
  await ensureSchema();
  const { rows } =
    status && status !== "all"
      ? await pool().query<AdmissionRow>("SELECT * FROM admissions WHERE status = $1 ORDER BY submitted_at DESC", [status])
      : await pool().query<AdmissionRow>("SELECT * FROM admissions ORDER BY submitted_at DESC");
  return rows.map(toAdmission);
}

export type NewAdmission = Pick<
  AdmissionRequest,
  "full_name" | "email" | "target_cefr" | "academic_background" | "motivation"
>;

/** Returns the created application, or null when the email already applied. */
export async function createAdmission(input: NewAdmission): Promise<AdmissionRequest | null> {
  if (!hasDatabase) {
    if (memAdmissions.some((r) => r.email.toLowerCase() === input.email.toLowerCase())) return null;
    const created: AdmissionRequest = {
      ...input,
      id: Math.max(0, ...memAdmissions.map((r) => r.id)) + 1,
      status: "pending",
      submitted_at: new Date().toISOString(),
    };
    memAdmissions.unshift(created);
    return created;
  }
  await ensureSchema();
  const { rows } = await pool().query<AdmissionRow>(
    `INSERT INTO admissions (full_name, email, target_cefr, academic_background, motivation)
     VALUES ($1,$2,$3,$4,$5)
     ON CONFLICT (lower(email)) DO NOTHING
     RETURNING *`,
    [input.full_name, input.email, input.target_cefr, input.academic_background, input.motivation]
  );
  return rows[0] ? toAdmission(rows[0]) : null;
}

export async function decideAdmission(
  id: number,
  status: AdmissionRequest["status"],
  reason?: string
): Promise<AdmissionRequest | null> {
  if (!hasDatabase) {
    const i = memAdmissions.findIndex((r) => r.id === id);
    if (i === -1) return null;
    memAdmissions[i] = {
      ...memAdmissions[i],
      status,
      reviewed_at: new Date().toISOString(),
      rejection_reason: reason || memAdmissions[i].rejection_reason,
    };
    return memAdmissions[i];
  }
  await ensureSchema();
  const { rows } = await pool().query<AdmissionRow>(
    `UPDATE admissions
        SET status = $2, reviewed_at = now(), rejection_reason = COALESCE(NULLIF($3, ''), rejection_reason)
      WHERE id = $1 RETURNING *`,
    [id, status, reason ?? ""]
  );
  return rows[0] ? toAdmission(rows[0]) : null;
}

/* Vocabulary (words ingested at runtime; the built-in lexicon stays in code) */

export async function listIngestedWords(): Promise<VocabularyWord[]> {
  if (!hasDatabase) return [...memVocabulary];
  await ensureSchema();
  const { rows } = await pool().query<{ id: number; data: VocabularyWord }>(
    "SELECT id, data FROM vocabulary ORDER BY created_at DESC"
  );
  // DB ids are offset so they never collide with built-in word ids (1..9999).
  return rows.map((r) => ({ ...r.data, id: 10_000 + r.id }));
}

export async function upsertWord(word: Omit<VocabularyWord, "id">): Promise<{ word: VocabularyWord; created: boolean }> {
  if (!hasDatabase) {
    const i = memVocabulary.findIndex((w) => w.french_word.toLowerCase() === word.french_word.toLowerCase());
    if (i >= 0) {
      memVocabulary[i] = { ...word, id: memVocabulary[i].id };
      return { word: memVocabulary[i], created: false };
    }
    const created = { ...word, id: 10_000 + memVocabulary.length + 1 } as VocabularyWord;
    memVocabulary.unshift(created);
    return { word: created, created: true };
  }
  await ensureSchema();
  const { rows } = await pool().query<{ id: number; inserted: boolean }>(
    `INSERT INTO vocabulary (french_word, data) VALUES ($1, $2)
     ON CONFLICT (lower(french_word)) DO UPDATE SET data = EXCLUDED.data
     RETURNING id, (xmax = 0) AS inserted`,
    [word.french_word, JSON.stringify(word)]
  );
  const row = rows[0];
  return { word: { ...word, id: 10_000 + row.id } as VocabularyWord, created: row.inserted };
}

export { initialVocabulary };
