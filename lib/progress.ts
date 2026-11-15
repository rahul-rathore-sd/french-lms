"use client";

import { useSyncExternalStore } from "react";
import { CardState, review, Rating, todayKey, addDays } from "./srs";

export interface Progress {
  version: 1;
  xp: number;
  streak: number;
  lastActive: string | null;
  activeDays: string[]; // YYYY-MM-DD, most recent last (max 120)
  completedLessons: number[];
  quizzes: Record<number, boolean>; // lessonId -> answered correctly
  cards: Record<number, CardState>; // vocabulary id -> SRS state
  dailyXp: Record<string, number>; // YYYY-MM-DD -> xp earned
  dailyGoal: number;
  showHindi: boolean;
}

const KEY = "academie.progress.v1";

const EMPTY: Progress = {
  version: 1,
  xp: 0,
  streak: 0,
  lastActive: null,
  activeDays: [],
  completedLessons: [],
  quizzes: {},
  cards: {},
  dailyXp: {},
  dailyGoal: 30,
  showHindi: true,
};

let cache: Progress = EMPTY;
let loaded = false;
const listeners = new Set<() => void>();

function load(): Progress {
  try {
    const raw = window.localStorage.getItem(KEY);
    if (raw) return { ...EMPTY, ...JSON.parse(raw) } as Progress;
  } catch {
    /* storage unavailable or corrupt: start fresh */
  }
  return EMPTY;
}

function persist(next: Progress) {
  cache = next;
  try {
    window.localStorage.setItem(KEY, JSON.stringify(next));
  } catch {
    /* ignore quota / privacy mode */
  }
  listeners.forEach((l) => l());
}

function subscribe(listener: () => void) {
  if (!loaded && typeof window !== "undefined") {
    cache = load();
    loaded = true;
  }
  listeners.add(listener);
  return () => listeners.delete(listener);
}

const getSnapshot = () => cache;
const getServerSnapshot = () => EMPTY;

export function useProgress(): Progress {
  return useSyncExternalStore(subscribe, getSnapshot, getServerSnapshot);
}

/** Streak shown to the user: broken when the last activity is older than yesterday. */
export function liveStreak(p: Progress, today = todayKey()): number {
  if (!p.lastActive) return 0;
  return p.lastActive === today || p.lastActive === addDays(today, -1) ? p.streak : 0;
}

export function levelFromXp(xp: number): { level: number; into: number; needed: number } {
  let level = 1;
  let remaining = xp;
  let needed = 100;
  while (remaining >= needed) {
    remaining -= needed;
    level += 1;
    needed = 100 + (level - 1) * 40;
  }
  return { level, into: remaining, needed };
}

function award(p: Progress, xp: number): Progress {
  const today = todayKey();
  let streak = p.streak;
  if (p.lastActive !== today) {
    streak = p.lastActive === addDays(today, -1) ? p.streak + 1 : 1;
  }
  const activeDays = p.activeDays.includes(today) ? p.activeDays : [...p.activeDays, today].slice(-120);
  return {
    ...p,
    xp: p.xp + xp,
    streak,
    lastActive: today,
    activeDays,
    dailyXp: { ...p.dailyXp, [today]: (p.dailyXp[today] ?? 0) + xp },
  };
}

export const actions = {
  completeLesson(lessonId: number, quizCorrect: boolean) {
    const p = cache;
    const first = !p.completedLessons.includes(lessonId);
    const gained = first ? 20 + (quizCorrect ? 10 : 0) : quizCorrect ? 2 : 0;
    const next = award(p, gained);
    next.completedLessons = first ? [...p.completedLessons, lessonId] : p.completedLessons;
    next.quizzes = { ...p.quizzes, [lessonId]: p.quizzes[lessonId] || quizCorrect };
    persist(next);
    return gained;
  },
  reviewCard(wordId: number, rating: Rating) {
    const p = cache;
    const gained = rating === "again" ? 1 : rating === "hard" ? 2 : rating === "good" ? 3 : 4;
    const next = award(p, gained);
    next.cards = { ...p.cards, [wordId]: review(p.cards[wordId], rating) };
    persist(next);
  },
  setDailyGoal(goal: number) {
    persist({ ...cache, dailyGoal: goal });
  },
  setShowHindi(show: boolean) {
    persist({ ...cache, showHindi: show });
  },
  reset() {
    persist({ ...EMPTY });
  },
};
