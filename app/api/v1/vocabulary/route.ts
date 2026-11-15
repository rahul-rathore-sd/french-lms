import { NextResponse } from "next/server";
import { initialVocabulary, VocabularyWord } from "@/lib/data";
import { checkHermesKey } from "@/lib/auth";
import { listIngestedWords, upsertWord } from "@/lib/store";

export const dynamic = "force-dynamic";

const CEFR = ["A1", "A2", "B1", "B2", "C1", "C2"];

function text(value: unknown, max: number): string {
  return typeof value === "string" ? value.trim().slice(0, max) : "";
}

export async function GET(req: Request) {
  const { searchParams } = new URL(req.url);
  const category = searchParams.get("category");
  const level = searchParams.get("level");
  const search = searchParams.get("search");

  // Words ingested at runtime override built-in words with the same spelling.
  const merged = new Map<string, VocabularyWord>();
  try {
    for (const w of [...initialVocabulary, ...(await listIngestedWords())]) {
      merged.set(w.french_word.toLowerCase(), w);
    }
  } catch (err) {
    console.error("Vocabulary storage unavailable, serving built-in lexicon only:", err);
    for (const w of initialVocabulary) merged.set(w.french_word.toLowerCase(), w);
  }

  let filtered = Array.from(merged.values());
  if (category) filtered = filtered.filter((w) => w.category.toLowerCase() === category.toLowerCase());
  if (level) filtered = filtered.filter((w) => w.cefr_level.toLowerCase() === level.toLowerCase());
  if (search) {
    const q = search.toLowerCase();
    filtered = filtered.filter(
      (w) =>
        w.french_word.toLowerCase().includes(q) ||
        w.meaning_en.toLowerCase().includes(q) ||
        w.meaning_hi.includes(q) ||
        w.use_case.toLowerCase().includes(q)
    );
  }

  return NextResponse.json({ success: true, count: filtered.length, data: filtered });
}

export async function POST(req: Request) {
  const keyStatus = checkHermesKey(req);
  if (keyStatus === "unconfigured") {
    return NextResponse.json(
      { error: "Ingestion is disabled: HERMES_API_KEY is not configured on the server." },
      { status: 503 }
    );
  }
  if (keyStatus !== "ok") {
    return NextResponse.json({ error: "Unauthorized. A valid Bearer token is required." }, { status: 401 });
  }

  let body: Record<string, unknown>;
  try {
    body = await req.json();
  } catch {
    return NextResponse.json({ error: "Invalid JSON body." }, { status: 400 });
  }

  const french_word = text(body.french_word, 120);
  const meaning_en = text(body.meaning_en, 500);
  const meaning_hi = text(body.meaning_hi, 500);
  const use_case = text(body.use_case, 800);

  if (!french_word || !meaning_en || !meaning_hi || !use_case) {
    return NextResponse.json(
      { error: "french_word, meaning_en, meaning_hi and use_case are required." },
      { status: 422 }
    );
  }

  const level = text(body.cefr_level, 2).toUpperCase();
  const cefr_level = CEFR.includes(level) ? level : "B2";
  const example = text(body.example_sentence, 500) || french_word;
  const ipa = text(body.ipa_phonetic, 120) || "/.../";

  try {
    const { word, created } = await upsertWord({
      word: french_word,
      french_word,
      ipa,
      ipa_phonetic: ipa,
      part_of_speech: text(body.part_of_speech, 60) || "nom",
      meaning: meaning_en,
      meaning_en,
      meaning_hi,
      level: cefr_level,
      cefr_level,
      category: text(body.category, 80) || "General",
      example_fr: example,
      example_sentence: example,
      use_case,
      notes: text(body.notes, 500) || "Ingested via Hermes agent",
      source: "Hermes",
      created_at: new Date().toISOString(),
    });
    return NextResponse.json({
      success: true,
      message: created ? "New word added to the lexicon." : "Word updated.",
      data: word,
    });
  } catch (err) {
    console.error("Vocabulary ingestion failed:", err);
    return NextResponse.json({ error: "Storage error. Please try again." }, { status: 500 });
  }
}
