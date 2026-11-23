"use client";

import { useState } from "react";
import { ArrowLeft, CheckCircle2, Eye, EyeOff, Languages, Volume2, XCircle } from "lucide-react";
import { useSpeech } from "@/components/useSpeech";
import { actions, useProgress } from "@/lib/progress";
import type { TextItem } from "@/lib/library";

export default function TextReader({ item, onBack }: { item: TextItem; onBack: () => void }) {
  const { speak, speaking } = useSpeech();
  const progress = useProgress();
  const [showEn, setShowEn] = useState(true);
  const [showHi, setShowHi] = useState(false);
  const [answers, setAnswers] = useState<Record<number, number>>({});
  const [gained, setGained] = useState<number | null>(null);

  const doneId = `text:${item.slug}`;
  const completed = progress.completedItems.includes(doneId);
  const allAnswered = item.questions.every((_, i) => answers[i] !== undefined);
  const score = item.questions.filter((q, i) => answers[i] === q.correct).length;

  const answer = (qi: number, oi: number) => {
    if (answers[qi] !== undefined) return;
    const next = { ...answers, [qi]: oi };
    setAnswers(next);
    if (item.questions.every((_, i) => next[i] !== undefined)) {
      setGained(actions.completeItem(doneId, 15 + 5 * item.questions.filter((q, i) => next[i] === q.correct).length));
    }
  };

  const isPoem = item.kind === "poem";

  return (
    <article className="animate-rise">
      <button onClick={onBack} className="inline-flex items-center gap-1.5 text-sm text-muted transition hover:text-fg">
        <ArrowLeft className="h-4 w-4" /> Back to library
      </button>

      <header className="mt-4">
        <div className="flex flex-wrap items-center gap-2 text-xs text-muted">
          <span className="rounded-full bg-fg px-2.5 py-1 font-semibold text-bg">{item.level}</span>
          <span>{item.minutes} min</span>
          <span>·</span>
          <span>{item.author}{item.year ? `, ${item.year}` : ""}</span>
          {completed && <span className="inline-flex items-center gap-1 text-success"><CheckCircle2 className="h-3.5 w-3.5" /> Completed</span>}
        </div>
        <h2 className="mt-2 font-display text-3xl font-medium leading-tight tracking-tight sm:text-4xl">{item.title}</h2>
        <p className="mt-1 text-muted">{item.title_en}</p>
        <p className="mt-4 max-w-2xl text-sm leading-relaxed text-muted sm:text-base">{item.summary_en}</p>
        <p className="mt-1 max-w-2xl text-sm leading-relaxed text-muted/80">{item.summary_hi}</p>
      </header>

      {/* Controls */}
      <div className="mt-6 flex flex-wrap gap-2">
        <button
          onClick={() => setShowEn((v) => !v)}
          aria-pressed={showEn}
          className={`inline-flex h-10 items-center gap-2 rounded-full border px-4 text-sm transition ${showEn ? "border-accent bg-accent/10 text-fg" : "border-line text-muted hover:text-fg"}`}
        >
          {showEn ? <Eye className="h-4 w-4" /> : <EyeOff className="h-4 w-4" />} English
        </button>
        <button
          onClick={() => speak(item.passages.map((p) => p.fr).join(" "), 0.85)}
          className="inline-flex h-10 items-center gap-2 rounded-full border border-line px-4 text-sm text-muted transition hover:border-accent hover:text-fg"
        >
          <Volume2 className="h-4 w-4" /> Listen to all
        </button>
        <button
          onClick={() => setShowHi((v) => !v)}
          aria-pressed={showHi}
          className={`inline-flex h-10 items-center gap-2 rounded-full border px-4 text-sm transition ${showHi ? "border-accent bg-accent/10 text-fg" : "border-line text-muted hover:text-fg"}`}
        >
          <Languages className="h-4 w-4" /> हिन्दी glossary
        </button>
      </div>

      {/* Text */}
      <div className="mt-6 space-y-4">
        {item.passages.map((p, i) => (
          <section key={i} className="rounded-3xl border border-line bg-surface p-5 sm:p-7">
            <div className="flex items-start justify-between gap-3">
              <p className={`whitespace-pre-line font-display leading-relaxed ${isPoem ? "text-[1.05rem] sm:text-2xl" : "text-lg sm:text-xl"}`}>{p.fr}</p>
              <button
                onClick={() => speak(p.fr, 0.8)}
                aria-label="Listen to this passage"
                className={`grid h-10 w-10 shrink-0 place-items-center rounded-full border border-line text-muted transition hover:border-accent hover:text-accent ${speaking === p.fr ? "border-accent text-accent" : ""}`}
              >
                <Volume2 className="h-4 w-4" />
              </button>
            </div>
            {showEn && <p className="mt-4 whitespace-pre-line border-t border-line pt-4 text-sm leading-relaxed text-muted sm:text-base">{p.en}</p>}
          </section>
        ))}
      </div>

      {/* Glossary */}
      <section className="mt-8">
        <h3 className="font-semibold">Key vocabulary</h3>
        <ul className="mt-3 grid gap-2 sm:grid-cols-2">
          {item.glossary.map((g) => (
            <li key={g.fr}>
              <button
                onClick={() => speak(g.fr)}
                className="flex w-full items-start justify-between gap-3 rounded-2xl border border-line bg-surface px-4 py-3 text-left transition hover:border-accent/40"
              >
                <span className="min-w-0">
                  <span className="block font-medium">{g.fr}</span>
                  <span className="block text-sm text-muted">{g.en}</span>
                  {showHi && g.hi && <span className="mt-0.5 block text-sm text-muted/80">{g.hi}</span>}
                </span>
                <Volume2 className="mt-1 h-4 w-4 shrink-0 text-muted" />
              </button>
            </li>
          ))}
        </ul>
      </section>

      {/* Questions */}
      <section className="mt-8 rounded-3xl border border-line bg-surface p-5 sm:p-7">
        <h3 className="font-semibold">Check your understanding</h3>
        <div className="mt-4 space-y-6">
          {item.questions.map((q, qi) => (
            <div key={qi}>
              <p className="font-medium leading-snug">{q.q}</p>
              <div className="mt-3 grid gap-2">
                {q.options.map((o, oi) => {
                  const picked = answers[qi];
                  const answered = picked !== undefined;
                  const state = !answered
                    ? "border-line hover:border-accent/60 hover:bg-raised"
                    : oi === q.correct
                    ? "border-success bg-success/10"
                    : oi === picked
                    ? "border-danger bg-danger/10"
                    : "border-line opacity-60";
                  return (
                    <button
                      key={oi}
                      disabled={answered}
                      onClick={() => answer(qi, oi)}
                      className={`flex items-center justify-between gap-3 rounded-xl border px-4 py-3 text-left text-sm transition ${state}`}
                    >
                      <span>{o}</span>
                      {answered && oi === q.correct && <CheckCircle2 className="h-5 w-5 shrink-0 text-success" />}
                      {answered && oi === picked && oi !== q.correct && <XCircle className="h-5 w-5 shrink-0 text-danger" />}
                    </button>
                  );
                })}
              </div>
              {answers[qi] !== undefined && <p className="mt-3 rounded-xl bg-raised p-3 text-sm leading-relaxed text-muted">{q.explain}</p>}
            </div>
          ))}
        </div>
        {allAnswered && (
          <p className="mt-5 animate-rise rounded-2xl bg-accent/10 px-4 py-3 text-sm">
            <span className="font-semibold">{score} / {item.questions.length} correct.</span>{" "}
            {gained ? <span className="text-gold">+{gained} XP</span> : <span className="text-muted">Already completed — no extra XP.</span>}
          </p>
        )}
      </section>
    </article>
  );
}
