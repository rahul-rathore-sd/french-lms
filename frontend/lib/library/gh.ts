import type { GrammarExample, GrammarTopic, Question } from "./types";

/** Tiny builders so the grammar data files stay compact and readable. */
export const ex = (fr: string, en: string, hi: string): GrammarExample => ({ fr, en, hi });
export const rule = (rule: string, rule_hi: string, ...examples: GrammarExample[]) => ({ rule, rule_hi, examples });
export const q = (q: string, options: string[], correct: number, explain: string): Question => ({ q, options, correct, explain });
export const tb = (caption: string, headers: string[], ...rows: string[][]) => ({ caption, headers, rows });
export type Topic = GrammarTopic;
