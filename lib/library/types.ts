export type Level = "A1" | "A2" | "B1" | "B2" | "C1";
export const LEVELS: Level[] = ["A1", "A2", "B1", "B2", "C1"];

export interface Passage {
  fr: string; // may contain line breaks (poems)
  en: string;
}

export interface GlossaryItem {
  fr: string;
  en: string;
  hi?: string;
}

export interface Question {
  q: string;
  options: string[];
  correct: number;
  explain: string;
}

export interface TextItem {
  slug: string;
  kind: "poem" | "story" | "maxims";
  title: string;
  title_en: string;
  author: string;
  year?: string;
  level: Level;
  minutes: number;
  summary_en: string;
  summary_hi: string;
  source: "Public domain" | "Original";
  passages: Passage[];
  glossary: GlossaryItem[];
  questions: Question[];
}

export interface TranslationPair {
  fr: string;
  en: string;
  level: Level;
  topic: string;
  /** Other accepted French answers (optional). */
  alt?: string[];
}

export interface GrammarExample {
  fr: string;
  en: string;
}

export interface GrammarTable {
  caption: string;
  headers: string[];
  rows: string[][];
}

export interface GrammarTopic {
  slug: string;
  level: Level;
  title: string;
  summary: string;
  rules: { rule: string; examples: GrammarExample[] }[];
  tables?: GrammarTable[];
  tips: string[];
}
