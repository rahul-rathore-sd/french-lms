import { poems } from "./poems";
import { literature } from "./literature";
import { translationBank } from "./translation";
import { grammar as grammarBase } from "./grammar";
import { grammarA } from "./grammar-a";
import { grammarB1 } from "./grammar-b";
import { grammarC } from "./grammar-c";
import { hiSummary } from "./hi-patch";
import { examGuides, nclcTable, roadmap, practice, writingTasks, speakingTasks } from "./exams";
import type { GrammarTopic, Level, TextItem } from "./types";

export * from "./types";
const order: Level[] = ["A1", "A2", "B1", "B2", "C1"];

/** Every grammar topic, ordered A1 → C1, with Hindi summaries merged in. */
export const grammar: GrammarTopic[] = [
  ...grammarBase.map((g) => ({ ...g, title_hi: hiSummary[g.slug]?.title, summary_hi: hiSummary[g.slug]?.summary })),
  ...grammarA,
  ...grammarB1,
  ...grammarC,
].sort((a, b) => order.indexOf(a.level) - order.indexOf(b.level));

export { poems, literature, translationBank, examGuides, nclcTable, roadmap, practice, writingTasks, speakingTasks };

export type Section = "poetry" | "literature" | "translate" | "grammar" | "exams" | "resources";

export const SECTIONS: { id: Section; label: string; blurb: string }[] = [
  { id: "poetry", label: "Poetry", blurb: "Songs, fables and verse from Ronsard to Baudelaire, with line-by-line translation." },
  { id: "literature", label: "Literature", blurb: "Graded stories, then classics by Perrault, Voltaire, Flaubert and Maupassant." },
  { id: "translate", label: "Translate", blurb: "Build French sentences and decode them back to English, by level." },
  { id: "grammar", label: "Grammar", blurb: "Simple rules with Hindi meaning, examples, tables and quizzes: from zero to C1." },
  { id: "exams", label: "Exams", blurb: "TEF Canada, TCF Canada and DELF/DALF: formats, NCLC scores, practice and a roadmap beyond NCLC 7." },
  { id: "resources", label: "Resources", blurb: "Your books with covers, plus the best free websites, podcasts and tools for learning French." },
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
    case "resources":
      return 0;
    case "exams":
      return practice.filter((t) => t.level === level).length + writingTasks.filter((t) => t.level === level).length + speakingTasks.filter((t) => t.level === level).length;
  }
};
