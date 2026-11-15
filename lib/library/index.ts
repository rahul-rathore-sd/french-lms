import { poems } from "./poems";
import { literature } from "./literature";
import { translationBank } from "./translation";
import { grammar } from "./grammar";
import type { Level, TextItem } from "./types";

export * from "./types";
export { poems, literature, translationBank, grammar };

export type Section = "poetry" | "literature" | "translate" | "grammar";

export const SECTIONS: { id: Section; label: string; blurb: string }[] = [
  { id: "poetry", label: "Poetry", blurb: "Songs, fables and verse from Ronsard to Baudelaire, with line-by-line translation." },
  { id: "literature", label: "Literature", blurb: "Graded stories, then classics by Perrault, Voltaire, Flaubert and Maupassant." },
  { id: "translate", label: "Translate", blurb: "Build French sentences and decode them back to English, by level." },
  { id: "grammar", label: "Grammar", blurb: "Clear rules, examples and verb tables for every level." },
];

export const textsFor = (section: Section): TextItem[] =>
  section === "poetry" ? poems : section === "literature" ? literature : [];

export const countByLevel = (section: Section, level: Level): number => {
  switch (section) {
    case "poetry":
      return poems.filter((t) => t.level === level).length;
    case "literature":
      return literature.filter((t) => t.level === level).length;
    case "translate":
      return translationBank.filter((t) => t.level === level).length;
    case "grammar":
      return grammar.filter((t) => t.level === level).length;
  }
};
