"use client";

import { useCallback, useMemo, useState } from "react";
import { ArrowRight, CheckCircle2, RotateCcw, Volume2, XCircle } from "lucide-react";
import { useSpeech } from "@/components/useSpeech";
import { actions } from "@/lib/progress";
import { translationBank, Level, TranslationPair } from "@/lib/library";

const SESSION_SIZE = 8;

const shuffle = <T,>(arr: T[]): T[] => {
  const a = [...arr];
  for (let i = a.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [a[i], a[j]] = [a[j], a[i]];
  }
  return a;
};

const norm = (s: string) =>
  s
    .replace(/[’‘]/g, "'")
    .replace(/\s+([?!:;,.])/g, "$1")
    .replace(/\s+/g, " ")
    .trim()
    .toLowerCase();

type Question =
  | { mode: "build"; pair: TranslationPair; tiles: string[] }
  | { mode: "choose"; pair: TranslationPair; options: string[] };

function buildSession(level: Level): Question[] {
  const pool = translationBank.filter((t) => t.level === level);
  const chosen = shuffle(pool).slice(0, SESSION_SIZE);
  return chosen.map((pair, i) => {
    if (i % 2 === 0) {
      const words = pair.fr.split(" ");
      // add up to two distractor words from other sentences of the same level
      const extras = shuffle(pool.filter((p) => p !== pair).flatMap((p) => p.fr.split(" ")))
        .filter((w) => !words.includes(w))
        .slice(0, 2);
      return { mode: "build", pair, tiles: shuffle([...words, ...extras]) };
    }
    const wrong = shuffle(pool.filter((p) => p !== pair).map((p) => p.en)).slice(0, 3);
    return { mode: "choose", pair, options: shuffle([pair.en, ...wrong]) };
  });
}

export default function TranslateDrill({ level }: { level: Level }) {
  const [session, setSession] = useState<Question[]>(() => buildSession(level));
  const [index, setIndex] = useState(0);
  const [picked, setPicked] = useState<string[]>([]); // build mode: tile indexes as strings
  const [choice, setChoice] = useState<string | null>(null);
  const [result, setResult] = useState<"right" | "wrong" | null>(null);
  const [correct, setCorrect] = useState(0);
  const [xp, setXp] = useState<number | null>(null);
  const { speak } = useSpeech();

  const q = session[index];
  const finished = index >= session.length;

  const restart = useCallback(() => {
    setSession(buildSession(level));
    setIndex(0);
    setPicked([]);
    setChoice(null);
    setResult(null);
    setCorrect(0);
    setXp(null);
  }, [level]);

  const answerText = useMemo(() => {
    if (!q || q.mode !== "build") return "";
    return picked.map((i) => q.tiles[Number(i)]).join(" ");
  }, [q, picked]);

  const check = () => {
    if (!q || result) return;
    let ok = false;
    if (q.mode === "build") ok = norm(answerText) === norm(q.pair.fr);
    else ok = choice === q.pair.en;
    setResult(ok ? "right" : "wrong");
    if (ok) setCorrect((c) => c + 1);
    if (q.mode === "build") speak(q.pair.fr);
  };

  const next = () => {
    const last = index + 1 >= session.length;
    if (last) {
      const earned = 5 + correct * 3;
      actions.addXp(earned);
      setXp(earned);
    }
    setIndex((i) => i + 1);
    setPicked([]);
    setChoice(null);
    setResult(null);
  };

  if (session.length === 0) {
    return <p className="rounded-2xl border border-dashed border-line p-10 text-center text-sm text-muted">No exercises for this level yet.</p>;
  }

  if (finished) {
    const pct = Math.round((correct / session.length) * 100);
    return (
      <div className="animate-rise rounded-3xl border border-line bg-surface p-8 text-center sm:p-12">
        <p className="font-display text-5xl font-semibold">{pct}%</p>
        <p className="mt-2 text-muted">{correct} of {session.length} correct · {level}</p>
        {xp !== null && <p className="mt-3 text-gold">+{xp} XP</p>}
        <button onClick={restart} className="mt-8 inline-flex h-11 items-center gap-2 rounded-full bg-accent px-6 text-sm font-medium text-accent-fg shadow-glow transition hover:brightness-110">
          <RotateCcw className="h-4 w-4" /> New session
        </button>
      </div>
    );
  }

  const canCheck = q.mode === "build" ? picked.length > 0 : choice !== null;

  return (
    <div className="mx-auto max-w-2xl">
      <div className="flex items-center gap-3">
        <div className="h-1.5 flex-1 overflow-hidden rounded-full bg-raised">
          <div className="h-full rounded-full bg-accent transition-all duration-500" style={{ width: `${(index / session.length) * 100}%` }} />
        </div>
        <span className="text-xs tabular-nums text-muted">{index + 1} / {session.length}</span>
      </div>

      <div className="mt-6 rounded-3xl border border-line bg-surface p-5 sm:p-8">
        <p className="text-xs font-medium uppercase tracking-wider text-muted">
          {q.mode === "build" ? "Translate into French" : "What does this mean in English?"} · {q.pair.topic}
        </p>

        {q.mode === "build" ? (
          <>
            <p className="mt-3 font-display text-2xl leading-snug sm:text-3xl">{q.pair.en}</p>

            {/* Answer line */}
            <div className="mt-6 flex min-h-[3.5rem] flex-wrap items-center gap-2 border-b border-line pb-4" aria-label="Your answer">
              {picked.length === 0 && <span className="text-sm text-muted">Tap the words in order…</span>}
              {picked.map((i, pos) => (
                <button
                  key={`${i}-${pos}`}
                  disabled={!!result}
                  onClick={() => setPicked((p) => p.filter((x) => x !== i))}
                  className="rounded-xl border border-accent/50 bg-accent/10 px-3 py-2 text-sm font-medium transition hover:bg-accent/20 disabled:cursor-default"
                >
                  {q.tiles[Number(i)]}
                </button>
              ))}
            </div>

            {/* Word bank */}
            <div className="mt-4 flex flex-wrap gap-2">
              {q.tiles.map((w, i) => {
                const used = picked.includes(String(i));
                return (
                  <button
                    key={i}
                    disabled={used || !!result}
                    onClick={() => setPicked((p) => [...p, String(i)])}
                    className={`rounded-xl border px-3 py-2 text-sm font-medium transition ${used ? "border-line bg-raised text-transparent" : "border-line hover:border-accent/60 hover:bg-raised"}`}
                  >
                    {w}
                  </button>
                );
              })}
            </div>
          </>
        ) : (
          <>
            <div className="mt-3 flex items-start justify-between gap-3">
              <p className="font-display text-2xl leading-snug sm:text-3xl">{q.pair.fr}</p>
              <button onClick={() => speak(q.pair.fr)} aria-label="Listen" className="grid h-10 w-10 shrink-0 place-items-center rounded-full border border-line text-muted transition hover:border-accent hover:text-accent">
                <Volume2 className="h-4 w-4" />
              </button>
            </div>
            <div className="mt-5 grid gap-2">
              {q.options.map((o) => {
                const state = !result
                  ? choice === o
                    ? "border-accent bg-accent/10"
                    : "border-line hover:border-accent/60 hover:bg-raised"
                  : o === q.pair.en
                  ? "border-success bg-success/10"
                  : o === choice
                  ? "border-danger bg-danger/10"
                  : "border-line opacity-60";
                return (
                  <button key={o} disabled={!!result} onClick={() => setChoice(o)} className={`rounded-xl border px-4 py-3 text-left text-sm transition ${state}`}>
                    {o}
                  </button>
                );
              })}
            </div>
          </>
        )}

        {result && (
          <div className={`mt-5 animate-rise rounded-2xl p-4 text-sm ${result === "right" ? "bg-success/10" : "bg-danger/10"}`}>
            <p className={`flex items-center gap-2 font-medium ${result === "right" ? "text-success" : "text-danger"}`}>
              {result === "right" ? <CheckCircle2 className="h-4 w-4" /> : <XCircle className="h-4 w-4" />}
              {result === "right" ? "Correct!" : "Not quite."}
            </p>
            {result === "wrong" && (
              <p className="mt-1.5 text-muted">
                Answer: <span className="font-medium text-fg">{q.mode === "build" ? q.pair.fr : q.pair.en}</span>
              </p>
            )}
          </div>
        )}
      </div>

      <div className="mt-5 flex justify-end">
        {!result ? (
          <button
            onClick={check}
            disabled={!canCheck}
            className="inline-flex h-11 w-full items-center justify-center rounded-full bg-accent px-7 text-sm font-medium text-accent-fg shadow-glow transition hover:brightness-110 disabled:opacity-40 disabled:shadow-none sm:w-auto"
          >
            Check
          </button>
        ) : (
          <button onClick={next} className="inline-flex h-11 w-full items-center justify-center gap-2 rounded-full bg-fg px-7 text-sm font-medium text-bg transition hover:opacity-90 sm:w-auto">
            {index + 1 >= session.length ? "Finish" : "Continue"} <ArrowRight className="h-4 w-4" />
          </button>
        )}
      </div>
    </div>
  );
}
