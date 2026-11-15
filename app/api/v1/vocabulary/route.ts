import { NextResponse } from "next/server";
import { initialVocabulary, VocabularyWord } from "@/lib/data";

// In-memory persistent store during runtime (can be swapped with database)
let lexiconMemory: VocabularyWord[] = [...initialVocabulary];

export async function GET(req: Request) {
  const { searchParams } = new URL(req.url);
  const category = searchParams.get("category");
  const level = searchParams.get("level");
  const search = searchParams.get("search");

  let filtered = [...lexiconMemory];

  if (category) {
    filtered = filtered.filter(
      (w) => w.category.toLowerCase() === category.toLowerCase()
    );
  }

  if (level) {
    filtered = filtered.filter(
      (w) => w.cefr_level.toLowerCase() === level.toLowerCase()
    );
  }

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

  return NextResponse.json({
    success: true,
    count: filtered.length,
    data: filtered,
  });
}

export async function POST(req: Request) {
  const authHeader = req.headers.get("authorization");
  const expectedKey = process.env.HERMES_API_KEY || "hermes_classical_secure_token_2026";

  if (!authHeader || authHeader !== `Bearer ${expectedKey}`) {
    return NextResponse.json(
      { error: "Unauthorized. Valid Bearer token required for Hermes Ingestion." },
      { status: 401 }
    );
  }

  try {
    const body = await req.json();
    const {
      french_word,
      ipa_phonetic,
      part_of_speech,
      meaning_en,
      meaning_hi,
      use_case,
      example_sentence,
      category = "Classical Literature",
      cefr_level = "B2",
      notes,
    } = body;

    if (!french_word || !meaning_en || !meaning_hi || !use_case) {
      return NextResponse.json(
        {
          error:
            "Missing required fields: french_word, meaning_en, meaning_hi (हिन्दी), and use_case are strictly required.",
        },
        { status: 422 }
      );
    }

    const existingIndex = lexiconMemory.findIndex(
      (w) => w.french_word.toLowerCase() === french_word.toLowerCase()
    );

    const newWord: VocabularyWord = {
      id: existingIndex >= 0 ? lexiconMemory[existingIndex].id : lexiconMemory.length + 1,
      french_word,
      ipa_phonetic: ipa_phonetic || "/.../",
      part_of_speech: part_of_speech || "nom",
      meaning_en,
      meaning_hi,
      use_case,
      example_sentence: example_sentence || french_word,
      category,
      cefr_level,
      notes: notes || "Autonomous ingestion via Hermes Agent",
      created_at: new Date().toISOString(),
    };

    if (existingIndex >= 0) {
      lexiconMemory[existingIndex] = newWord;
    } else {
      lexiconMemory.unshift(newWord);
    }

    return NextResponse.json({
      success: true,
      message: existingIndex >= 0 ? "Term updated successfully" : "New term ingested into Sanctuary Lexicon",
      data: newWord,
    });
  } catch (err: unknown) {
    const errorMsg = err instanceof Error ? err.message : "Internal error";
    return NextResponse.json({ error: errorMsg }, { status: 500 });
  }
}
