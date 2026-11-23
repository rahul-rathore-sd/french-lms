/** Lightweight SM-2 style spaced repetition. Dates are local YYYY-MM-DD strings. */

export type Rating = "again" | "hard" | "good" | "easy";

export interface CardState {
  ease: number; // 1.3 – 3.0
  interval: number; // days
  reps: number;
  lapses: number;
  due: string; // YYYY-MM-DD
}

export function todayKey(d: Date = new Date()): string {
  const y = d.getFullYear();
  const m = String(d.getMonth() + 1).padStart(2, "0");
  const day = String(d.getDate()).padStart(2, "0");
  return `${y}-${m}-${day}`;
}

export function addDays(key: string, days: number): string {
  const [y, m, d] = key.split("-").map(Number);
  const date = new Date(y, m - 1, d + days);
  return todayKey(date);
}

export function newCard(): CardState {
  return { ease: 2.5, interval: 0, reps: 0, lapses: 0, due: todayKey() };
}

export function review(card: CardState | undefined, rating: Rating, today = todayKey()): CardState {
  const c = card ?? newCard();
  let { ease, interval, reps, lapses } = c;

  if (rating === "again") {
    lapses += 1;
    reps = 0;
    interval = 0;
    ease = Math.max(1.3, ease - 0.2);
    return { ease, interval, reps, lapses, due: today };
  }

  reps += 1;
  if (rating === "hard") {
    ease = Math.max(1.3, ease - 0.15);
    interval = reps === 1 ? 1 : Math.max(1, Math.round(interval * 1.2));
  } else if (rating === "good") {
    interval = reps === 1 ? 1 : reps === 2 ? 3 : Math.round(interval * ease);
  } else {
    ease = Math.min(3.0, ease + 0.15);
    interval = reps === 1 ? 3 : Math.round(Math.max(interval, 1) * ease * 1.3);
  }
  return { ease, interval, reps, lapses, due: addDays(today, interval) };
}

export function isDue(card: CardState | undefined, today = todayKey()): boolean {
  return !card || card.due <= today;
}

/** Human readable hint for the next interval shown on rating buttons. */
export function previewInterval(card: CardState | undefined, rating: Rating): string {
  const next = review(card, rating);
  if (rating === "again") return "<1d";
  return next.interval === 1 ? "1d" : `${next.interval}d`;
}
