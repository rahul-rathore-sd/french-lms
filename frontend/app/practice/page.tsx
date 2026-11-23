"use client";

import { useCallback, useEffect, useMemo, useState } from "react";
import Link from "next/link";
import { ArrowLeft, PartyPopper, RotateCcw, Volume2 } from "lucide-react";
import SiteHeader from "@/components/SiteHeader";
import MobileTabBar from "@/components/MobileTabBar";
import { useSpeech } from "@/components/useSpeech";
import { useLexicon } from "@/components/useLexicon";
import { actions, useProgress } from "@/lib/progress";
import { isDue, previewInterval, Rating, todayKey } from "@/lib/srs";
import type { VocabularyWord } from "@/lib/data";

const NEW_PER_SESSION = 10;

const RATINGS: { key: Rating; label: string; hotkey: string; style: string }[] = [
  { key: "again", label: "Again", hotkey: "1", style: "border-danger/40 text-danger hover:bg-danger/10" },
  { key: "hard", label: "Hard", hotkey: "2", style: "border-gold/40 text-gold hover:bg-gold/10" },
  { key: "good", label: "Good", hotkey: "3", style: "border-success/40 text-success hover:bg-success/10" },
  { key: "easy", label: "Easy", hotkey: "4", style: "border-accent/40 text-accent hover:bg-accent/10" },
];

export default function PracticePage() {
  const lexicon = useLexicon();
  const progress = useProgress();
  const { speak } = useSpeech();

  // Build the queue once per session so reviewing a card does not reshuffle it.
  const [queue, setQueue] = useState<VocabularyWord[] | null>(null);
  const [index, setIndex] = useState(0);
  const [flipped, setFlipped] = useState(false);
  const [stats, setStats] = useState({ reviewed: 0, again: 0 });

  const buildQueue = useCallback(() => {
    const today = todayKey();
    const due = lexicon.filter((w) => progress.cards[w.id] && isDue(progress.cards[w.id], today));
    const fresh = lexicon.filter((w) => !progress.cards[w.id]).slice(0, NEW_PER_SESSION);
    const items = [...due, ...fresh];
    // Fisher–Yates shuffle
    for (let i = items.length - 1; i > 0; i--) {
      const j = Math.floor(Math.random() * (i + 1));
      [items[i], items[j]] = [items[j], items[i]];
    }
    setQueue(items);
    setIndex(0);
    setFlipped(false);
    setStats({ reviewed: 0, again: 0 });
  }, [lexicon, progress.cards]);

  // Build on first load of the lexicon (not on every progress change).
  useEffect(() => {
    if (queue === null && lexicon.length > 0) buildQueue();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [lexicon.length]);

  const card = queue?.[index];
  const total = queue?.length ?? 0;
  const finished = queue !== null && index >= total;

  const rate = useCallback(
    (r: Rating) => {
      if (!card) return;
      actions.reviewCard(card.id, r);
      setStats((s) => ({ reviewed: s.reviewed + 1, again: s.again + (r === "again" ? 1 : 0) }));
      // "Again" re-queues the card at the end of this session.
      if (r === "again") setQueue((q) => (q ? [...q, card] : q));
      setFlipped(false);
      setIndex((i) => i + 1);
    },
    [card]
  );

  useEffect(() => {
    const onKey = (e: KeyboardEvent) => {
      if (e.target instanceof HTMLElement && /INPUT|TEXTAREA|SELECT/.test(e.target.tagName)) return;
      if (e.code === "Space") {
        e.preventDefault();
        setFlipped((f) => !f);
      } else if (flipped && ["1", "2", "3", "4"].includes(e.key)) {
        rate(RATINGS[Number(e.key) - 1].key);
      } else if (e.key.toLowerCase() === "p" && card) {
        speak(card.french_word);
      }
    };
    window.addEventListener("keydown", onKey);
    return () => window.removeEventListener("keydown", onKey);
  }, [flipped, rate, card, speak]);

  const mastered = useMemo(
    () => Object.values(progress.cards).filter((c) => c.interval >= 21).length,
    [progress.cards]
  );

  return (
    <div className="min-h-screen">
      <SiteHeader />
      <main className="mx-auto max-w-2xl px-4 py-8 sm:px-6">
        <Link href="/dashboard" className="inline-flex items-center gap-1.5 text-sm text-muted transition hover:text-fg">
          <ArrowLeft className="h-4 w-4" /> Dashboard
        </Link>

        {queue === null ? (
          <p className="mt-16 text-center text-muted">Preparing your cards…</p>
        ) : total === 0 ? (
          <Empty mastered={mastered} />
        ) : finished ? (
          <Done reviewed={stats.reviewed} again={stats.again} onMore={buildQueue} />
        ) : (
          <>
            <div className="mt-6 flex items-center gap-3">
              <div className="h-1.5 flex-1 overflow-hidden rounded-full bg-raised">
                <div className="h-full rounded-full bg-accent transition-all duration-500" style={{ width: `${(index / total) * 100}%` }} />
              </div>
              <span className="text-xs tabular-nums text-muted">{index + 1} / {total}</span>
            </div>

            <div className="flip-scene mt-6">
              <div
                className="flip-card relative h-[26rem] w-full cursor-pointer sm:h-[28rem]"
                data-flipped={flipped}
                onClick={() => setFlipped((f) => !f)}
                role="button"
                tabIndex={0}
                aria-label={flipped ? "Show French side" : "Reveal answer"}
                onKeyDown={(e) => {
                  if (e.key === "Enter") setFlipped((f) => !f);
                }}
              >
                {/* Front */}
                <div className="flip-face absolute inset-0 flex flex-col items-center justify-center panel rounded-3xl p-8 text-center shadow-soft">
                  <span className="rounded-full bg-raised px-3 py-1 text-xs font-medium text-muted">{card!.cefr_level} · {card!.part_of_speech}</span>
                  <h2 className="mt-8 font-display text-5xl font-medium tracking-tight sm:text-6xl">{card!.french_word}</h2>
                  <p className="mt-4 font-mono text-sm text-muted">{card!.ipa_phonetic}</p>
                  <button
                    onClick={(e) => {
                      e.stopPropagation();
                      speak(card!.french_word);
                    }}
                    aria-label="Pronounce"
                    className="mt-8 grid h-12 w-12 place-items-center rounded-full bg-accent text-accent-fg transition hover:scale-105"
                  >
                    <Volume2 className="h-5 w-5" />
                  </button>
                  <p className="absolute bottom-5 text-xs text-muted">Tap to reveal</p>
                </div>

                {/* Back */}
                <div className="flip-face flip-back absolute inset-0 flex flex-col justify-center overflow-auto rounded-3xl border border-accent/40 bg-surface p-8 shadow-glow">
                  <p className="font-display text-3xl font-medium">{card!.french_word}</p>
                  <p className="mt-3 text-lg">{card!.meaning_en}</p>
                  {progress.showHindi && <p className="mt-1 text-muted">{card!.meaning_hi}</p>}
                  <div
                    className="mt-6 rounded-2xl bg-raised p-4 text-sm"
                    onClick={(e) => {
                      e.stopPropagation();
                      speak(card!.example_sentence);
                    }}
                  >
                    <p className="italic leading-relaxed">“{card!.example_sentence}”</p>
                    {card!.example_en && <p className="mt-2 text-muted">{card!.example_en}</p>}
                  </div>
                  <p className="mt-4 text-xs text-muted">{card!.use_case}</p>
                </div>
              </div>
            </div>

            <div className={`mt-6 grid grid-cols-4 gap-2 transition ${flipped ? "opacity-100" : "pointer-events-none opacity-30"}`}>
              {RATINGS.map((r) => (
                <button
                  key={r.key}
                  onClick={() => rate(r.key)}
                  disabled={!flipped}
                  className={`rounded-2xl border px-2 py-3 text-center transition ${r.style}`}
                >
                  <span className="block text-sm font-semibold">{r.label}</span>
                  <span className="mt-0.5 block text-[11px] opacity-70">{previewInterval(progress.cards[card!.id], r.key)}</span>
                  <kbd className="mt-1 hidden text-[10px] opacity-50 sm:inline">{r.hotkey}</kbd>
                </button>
              ))}
            </div>
            <p className="mt-4 hidden text-center text-xs text-muted md:block">Shortcuts: Space flip · 1–4 rate · P pronounce</p>
          </>
        )}
      </main>
      <MobileTabBar />
    </div>
  );
}

function Empty({ mastered }: { mastered: number }) {
  return (
    <div className="mt-16 panel rounded-3xl p-10 text-center">
      <PartyPopper className="mx-auto h-10 w-10 text-gold" />
      <h1 className="mt-4 font-display text-3xl font-medium">You are all caught up.</h1>
      <p className="mt-2 text-muted">No cards are due right now. {mastered > 0 && `${mastered} words are already well memorised.`}</p>
      <Link href="/dashboard" className="mt-6 inline-flex h-11 items-center rounded-full bg-accent px-6 text-sm font-medium text-accent-fg">Back to lessons</Link>
    </div>
  );
}

function Done({ reviewed, again, onMore }: { reviewed: number; again: number; onMore: () => void }) {
  const accuracy = reviewed ? Math.round(((reviewed - again) / reviewed) * 100) : 100;
  return (
    <div className="mt-16 animate-rise panel rounded-3xl p-10 text-center shadow-soft">
      <PartyPopper className="mx-auto h-10 w-10 text-gold" />
      <h1 className="mt-4 font-display text-3xl font-medium">Session complete</h1>
      <p className="mt-2 text-muted">{reviewed} reviews · {accuracy}% recalled on the first try</p>
      <div className="mt-8 flex flex-wrap justify-center gap-3">
        <button onClick={onMore} className="inline-flex h-11 items-center gap-2 rounded-full border border-line px-5 text-sm font-medium transition hover:border-accent/50">
          <RotateCcw className="h-4 w-4" /> Study more
        </button>
        <Link href="/dashboard" className="inline-flex h-11 items-center rounded-full bg-accent px-6 text-sm font-medium text-accent-fg">Back to lessons</Link>
      </div>
    </div>
  );
}
