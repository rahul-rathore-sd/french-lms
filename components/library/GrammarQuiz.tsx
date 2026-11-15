"use client";

import { useState } from "react";
import { CheckCircle2, XCircle } from "lucide-react";
import type { Question } from "@/lib/library";

/** Small self-check: tap an option, see right/wrong and the reason immediately. */
export default function GrammarQuiz({ questions }: { questions: Question[] }) {
  const [picked, setPicked] = useState<Record<number, number>>({});
  return (
    <div className="rounded-2xl border border-line p-4">
      <p className="text-sm font-semibold">Quick check · जाँचें</p>
      <ul className="mt-3 space-y-4">
        {questions.map((qu, qi) => {
          const sel = picked[qi];
          const answered = sel !== undefined;
          return (
            <li key={qu.q}>
              <p className="text-sm font-medium">{qi + 1}. {qu.q}</p>
              <div className="mt-2 flex flex-wrap gap-2">
                {qu.options.map((o, oi) => {
                  const right = oi === qu.correct;
                  const cls = !answered
                    ? "border-line hover:border-accent/60"
                    : right
                      ? "border-success bg-success/10 text-success"
                      : oi === sel
                        ? "border-danger bg-danger/10 text-danger"
                        : "border-line text-muted";
                  return (
                    <button key={o} disabled={answered} onClick={() => setPicked((p) => ({ ...p, [qi]: oi }))} className={`rounded-full border px-4 py-2 text-sm transition ${cls}`}>
                      {o}
                    </button>
                  );
                })}
              </div>
              {answered && (
                <p className="mt-2 flex items-start gap-1.5 text-sm text-muted">
                  {sel === qu.correct ? <CheckCircle2 className="mt-0.5 h-4 w-4 shrink-0 text-success" /> : <XCircle className="mt-0.5 h-4 w-4 shrink-0 text-danger" />}
                  {qu.explain}
                </p>
              )}
            </li>
          );
        })}
      </ul>
    </div>
  );
}
