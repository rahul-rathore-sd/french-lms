"use client";

import { useEffect, useState } from "react";
import { initialVocabulary, VocabularyWord } from "@/lib/data";

/** Static lexicon merged with words ingested live through the API. */
export function useLexicon(): VocabularyWord[] {
  const [words, setWords] = useState<VocabularyWord[]>(initialVocabulary);

  useEffect(() => {
    let cancelled = false;
    fetch("/api/v1/vocabulary")
      .then((r) => (r.ok ? r.json() : null))
      .then((json) => {
        if (cancelled || !json?.data) return;
        const merged = new Map<string, VocabularyWord>();
        [...initialVocabulary, ...(json.data as VocabularyWord[])].forEach((w) =>
          merged.set(w.french_word.toLowerCase(), w)
        );
        setWords(Array.from(merged.values()));
      })
      .catch(() => {
        /* offline: keep the static lexicon */
      });
    return () => {
      cancelled = true;
    };
  }, []);

  return words;
}
