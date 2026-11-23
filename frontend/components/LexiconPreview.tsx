"use client";

import { useMemo, useState } from "react";
import { Search, Volume2 } from "lucide-react";
import { initialVocabulary } from "@/lib/data";
import { useSpeech } from "./useSpeech";

const LEVELS = ["ALL", "A1", "A2", "B1", "B2", "C1"];

export default function LexiconPreview() {
  const [q, setQ] = useState("");
  const [level, setLevel] = useState("ALL");
  const { speak, speaking } = useSpeech();

  const words = useMemo(() => {
    const s = q.trim().toLowerCase();
    return initialVocabulary
      .filter((w) => (level === "ALL" ? true : w.cefr_level === level))
      .filter(
        (w) =>
          !s ||
          w.french_word.toLowerCase().includes(s) ||
          w.meaning_en.toLowerCase().includes(s) ||
          w.meaning_hi.includes(s)
      )
      .slice(0, 6);
  }, [q, level]);

  return (
    <div>
      <div className="flex flex-col gap-3 sm:flex-row sm:items-center">
        <label className="relative flex-1">
          <Search className="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-muted" />
          <input
            value={q}
            onChange={(e) => setQ(e.target.value)}
            placeholder="Search French, English or Hindi…"
            className="h-12 w-full rounded-full border border-line bg-surface pl-11 pr-4 text-sm outline-none transition placeholder:text-muted focus:border-accent"
          />
        </label>
        <div className="flex flex-wrap gap-1.5" role="tablist" aria-label="Filter by level">
          {LEVELS.map((l) => (
            <button
              key={l}
              role="tab"
              aria-selected={level === l}
              onClick={() => setLevel(l)}
              className={`rounded-full px-3.5 py-2 text-xs font-medium transition ${
                level === l ? "bg-fg text-bg" : "border border-line text-muted hover:text-fg"
              }`}
            >
              {l === "ALL" ? "All" : l}
            </button>
          ))}
        </div>
      </div>

      <ul className="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        {words.map((w) => (
          <li key={w.id} className="group panel rounded-2xl p-5 transition hover:border-accent/40 hover:shadow-soft">
            <div className="flex items-start justify-between gap-3">
              <div>
                <h3 className="font-display text-xl font-semibold">{w.french_word}</h3>
                <p className="mt-0.5 font-mono text-xs text-muted">{w.ipa_phonetic} · {w.part_of_speech}</p>
              </div>
              <button
                onClick={() => speak(w.french_word)}
                aria-label={`Pronounce ${w.french_word}`}
                className={`grid h-9 w-9 shrink-0 place-items-center rounded-full border border-line text-muted transition hover:border-accent hover:text-accent ${
                  speaking === w.french_word ? "border-accent text-accent" : ""
                }`}
              >
                <Volume2 className="h-4 w-4" />
              </button>
            </div>
            <p className="mt-3 line-clamp-2 text-sm text-fg">{w.meaning_en}</p>
            <p className="mt-1 line-clamp-1 text-sm text-muted">{w.meaning_hi}</p>
            <span className="mt-4 inline-block rounded-full bg-raised px-2.5 py-1 text-[11px] font-medium text-muted">{w.cefr_level}</span>
          </li>
        ))}
        {words.length === 0 && (
          <li className="col-span-full rounded-2xl border border-dashed border-line p-10 text-center text-sm text-muted">
            No words match that search.
          </li>
        )}
      </ul>
    </div>
  );
}
