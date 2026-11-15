"use client";

import { useState } from "react";
import GrammarQuiz from "./GrammarQuiz";
import { CheckCircle2, ChevronDown, Lightbulb, Volume2 } from "lucide-react";
import { useSpeech } from "@/components/useSpeech";
import { actions, useProgress } from "@/lib/progress";
import type { GrammarTopic } from "@/lib/library";

export default function GrammarView({ topics }: { topics: GrammarTopic[] }) {
  const [open, setOpen] = useState<string | null>(topics[0]?.slug ?? null);
  const { speak } = useSpeech();
  const progress = useProgress();

  if (topics.length === 0) {
    return <p className="rounded-2xl border border-dashed border-line p-10 text-center text-sm text-muted">No guides for this level yet.</p>;
  }

  return (
    <ul className="space-y-3">
      {topics.map((t) => {
        const isOpen = open === t.slug;
        const doneId = `grammar:${t.slug}`;
        const done = progress.completedItems.includes(doneId);
        return (
          <li key={t.slug} className="overflow-hidden rounded-2xl border border-line bg-surface">
            <button
              onClick={() => setOpen(isOpen ? null : t.slug)}
              aria-expanded={isOpen}
              className="flex w-full items-center gap-3 p-4 text-left transition hover:bg-raised/60 sm:p-5"
            >
              <span className="rounded-full bg-fg px-2.5 py-1 text-xs font-semibold text-bg">{t.level}</span>
              <span className="min-w-0 flex-1">
                <span className="block font-semibold leading-snug">{t.title}</span>
                {progress.showHindi && t.title_hi && <span className="block text-sm text-accent">{t.title_hi}</span>}
                <span className="mt-0.5 line-clamp-2 block text-sm text-muted">{t.summary}</span>
              </span>
              {done && <CheckCircle2 className="h-5 w-5 shrink-0 text-success" />}
              <ChevronDown className={`h-4 w-4 shrink-0 text-muted transition ${isOpen ? "rotate-180" : ""}`} />
            </button>

            {isOpen && (
              <div className="space-y-6 border-t border-line p-4 sm:p-6">
                {t.rules.map((r, i) => (
                  <div key={i}>
                    <p className="font-medium leading-relaxed">{r.rule}</p>
                    {progress.showHindi && r.rule_hi && <p className="mt-0.5 text-sm leading-relaxed text-accent">{r.rule_hi}</p>}
                    <ul className="mt-3 space-y-2">
                      {r.examples.map((e) => (
                        <li key={e.fr}>
                          <button
                            onClick={() => speak(e.fr)}
                            className="flex w-full items-center justify-between gap-3 rounded-xl bg-raised px-4 py-3 text-left transition hover:bg-raised/70"
                          >
                            <span className="min-w-0">
                              <span className="block font-display text-lg">{e.fr}</span>
                              <span className="block text-sm text-muted">{e.en}</span>
                              {progress.showHindi && e.hi && <span className="block text-sm text-accent">{e.hi}</span>}
                            </span>
                            <Volume2 className="h-4 w-4 shrink-0 text-muted" />
                          </button>
                        </li>
                      ))}
                    </ul>
                  </div>
                ))}

                {t.tables?.map((tb) => (
                  <div key={tb.caption}>
                    <p className="mb-2 text-sm font-semibold">{tb.caption}</p>
                    <div className="overflow-x-auto rounded-xl border border-line">
                      <table className="w-full min-w-[20rem] text-left text-sm">
                        <thead className="bg-raised text-xs uppercase tracking-wide text-muted">
                          <tr>{tb.headers.map((h) => <th key={h} className="px-4 py-2.5 font-medium">{h}</th>)}</tr>
                        </thead>
                        <tbody>
                          {tb.rows.map((row, ri) => (
                            <tr key={ri} className="border-t border-line">
                              {row.map((cell, ci) => (
                                <td key={ci} className={`px-4 py-2.5 ${ci === 0 ? "text-muted" : "font-medium"}`}>{cell}</td>
                              ))}
                            </tr>
                          ))}
                        </tbody>
                      </table>
                    </div>
                  </div>
                ))}

                <div className="rounded-2xl border border-gold/30 bg-gold/10 p-4">
                  <p className="flex items-center gap-2 text-sm font-semibold text-gold"><Lightbulb className="h-4 w-4" /> Tips</p>
                  <ul className="mt-2 list-disc space-y-1.5 pl-5 text-sm leading-relaxed text-muted">
                    {t.tips.map((tip, i) => (
                      <li key={tip}>
                        {tip}
                        {progress.showHindi && t.tips_hi?.[i] && <span className="block text-accent">{t.tips_hi[i]}</span>}
                      </li>
                    ))}
                  </ul>
                </div>

                {t.quiz && t.quiz.length > 0 && <GrammarQuiz questions={t.quiz} />}

                <button
                  onClick={() => actions.completeItem(doneId, 10)}
                  disabled={done}
                  className="inline-flex h-10 items-center gap-2 rounded-full border border-line px-5 text-sm font-medium transition hover:border-accent/60 disabled:text-success"
                >
                  <CheckCircle2 className="h-4 w-4" /> {done ? "Studied" : "Mark as studied (+10 XP)"}
                </button>
              </div>
            )}
          </li>
        );
      })}
    </ul>
  );
}
