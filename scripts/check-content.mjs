// Sanity checks for the learning content. Run: node --experimental-strip-types scripts/check-content.mjs
import { poems } from "../lib/library/poems.ts";
import { literature } from "../lib/library/literature.ts";
import { translationBank } from "../lib/library/translation.ts";
import { grammar as g1 } from "../lib/library/grammar.ts";
import { grammarA } from "../lib/library/grammar-a.ts";
import { grammarB1 } from "../lib/library/grammar-b.ts";
import { grammarC } from "../lib/library/grammar-c.ts";
import { practice } from "../lib/library/exams.ts";
const grammar = [...g1, ...grammarA, ...grammarB1, ...grammarC];

const problems = [];
const slugs = new Set();

for (const t of [...poems, ...literature]) {
  if (slugs.has(t.slug)) problems.push(`duplicate slug ${t.slug}`);
  slugs.add(t.slug);
  if (!t.passages.length) problems.push(`${t.slug}: no passages`);
  if (!t.questions.length) problems.push(`${t.slug}: no questions`);
  for (const q of t.questions) {
    if (q.correct < 0 || q.correct >= q.options.length) problems.push(`${t.slug}: bad correct index`);
    if (new Set(q.options).size !== q.options.length) problems.push(`${t.slug}: duplicate options in "${q.q}"`);
  }
}

const seen = new Set();
for (const p of translationBank) {
  if (seen.has(p.fr)) problems.push(`duplicate translation ${p.fr}`);
  seen.add(p.fr);
}

const gslugs = new Set();
for (const g of grammar) {
  if (gslugs.has(g.slug)) problems.push(`duplicate grammar slug ${g.slug}`);
  gslugs.add(g.slug);
  for (const tb of g.tables ?? []) {
    for (const row of tb.rows) if (row.length !== tb.headers.length) problems.push(`${g.slug}: table "${tb.caption}" row width mismatch`);
  }
}

for (const g of grammar) for (const qu of g.quiz ?? []) if (qu.correct < 0 || qu.correct >= qu.options.length) problems.push(`${g.slug}: bad quiz index`);
for (const e of practice) for (const qu of e.questions) if (qu.correct < 0 || qu.correct >= qu.options.length) problems.push(`${e.id}: bad exam index`);
const byLevel = (arr) => Object.fromEntries(["A1", "A2", "B1", "B2", "C1"].map((l) => [l, arr.filter((x) => x.level === l).length]));
console.log("poems", byLevel(poems));
console.log("literature", byLevel(literature));
console.log("translate", byLevel(translationBank));
console.log("grammar", byLevel(grammar));
console.log(problems.length ? `PROBLEMS:\n${problems.join("\n")}` : "content OK");
process.exit(problems.length ? 1 : 0);
