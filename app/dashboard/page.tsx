"use client";

import { Suspense, useMemo, useState } from "react";
import Link from "next/link";
import { useRouter, useSearchParams } from "next/navigation";
import { useUser } from "@clerk/nextjs";
import {
  ArrowRight,
  BookOpen,
  Check,
  CheckCircle2,
  ChevronDown,
  Flame,
  Layers,
  Search,
  Sparkles,
  Volume2,
  XCircle,
} from "lucide-react";
import SiteHeader from "@/components/SiteHeader";
import ProgressRing from "@/components/ProgressRing";
import { useSpeech } from "@/components/useSpeech";
import { useLexicon } from "@/components/useLexicon";
import { initialCourses, Course, Lesson } from "@/lib/data";
import { actions, levelFromXp, liveStreak, useProgress } from "@/lib/progress";
import { isDue, todayKey } from "@/lib/srs";

const LEVELS = ["ALL", "A1", "A2", "B1", "B2", "C1", "C2"];

function lessonTitle(l: Lesson) {
  return l.title_en ?? l.title;
}

function Dashboard() {
  const router = useRouter();
  const params = useSearchParams();
  const tab = params.get("tab") === "lexicon" ? "lexicon" : "learn";
  const { user } = useUser();
  const progress = useProgress();
  const lexicon = useLexicon();

  const streak = liveStreak(progress);
  const { level, into, needed } = levelFromXp(progress.xp);
  const today = todayKey();
  const todayXp = progress.dailyXp[today] ?? 0;
  const goalPct = Math.min(1, todayXp / progress.dailyGoal);
  const dueCount = lexicon.filter((w) => isDue(progress.cards[w.id], today) && progress.cards[w.id]).length;
  const newCount = lexicon.filter((w) => !progress.cards[w.id]).length;

  const totalLessons = initialCourses.reduce((n, c) => n + c.lessons.length, 0);
  const done = progress.completedLessons.length;

  const setTab = (t: "learn" | "lexicon") => router.replace(t === "learn" ? "/dashboard" : "/dashboard?tab=lexicon", { scroll: false });

  const name = user?.firstName ?? "there";

  return (
    <div className="min-h-screen">
      <SiteHeader />
      <main className="mx-auto max-w-6xl px-4 py-8 sm:px-6">
        {/* Greeting */}
        <div className="animate-rise">
          <p className="text-sm text-muted">{greeting()}</p>
          <h1 className="mt-1 font-display text-4xl font-medium tracking-tight sm:text-5xl">
            Bonjour, {name}.
          </h1>
        </div>

        {/* Stat row */}
        <section className="mt-8 grid gap-3 sm:grid-cols-2 lg:grid-cols-4" aria-label="Your progress">
          <Stat label="Day streak" icon={<Flame className={`h-5 w-5 ${streak ? "text-orange-500" : "text-muted"}`} />}>
            <span className="font-display text-3xl font-semibold">{streak}</span>
            <span className="ml-1 text-sm text-muted">{streak === 1 ? "day" : "days"}</span>
          </Stat>

          <Stat label={`Level ${level}`} icon={<Sparkles className="h-5 w-5 text-gold" />}>
            <span className="font-display text-3xl font-semibold">{progress.xp}</span>
            <span className="ml-1 text-sm text-muted">XP</span>
            <div className="mt-3 h-1.5 overflow-hidden rounded-full bg-raised">
              <div className="h-full rounded-full bg-gold transition-all duration-700" style={{ width: `${(into / needed) * 100}%` }} />
            </div>
          </Stat>

          <div className="flex items-center gap-4 rounded-2xl border border-line bg-surface p-5">
            <ProgressRing value={goalPct} size={64}>
              {goalPct >= 1 ? <Check className="h-5 w-5 text-success" /> : `${Math.round(goalPct * 100)}%`}
            </ProgressRing>
            <div>
              <p className="text-sm text-muted">Daily goal</p>
              <p className="font-medium">{todayXp} / {progress.dailyGoal} XP</p>
              <select
                aria-label="Daily goal"
                value={progress.dailyGoal}
                onChange={(e) => actions.setDailyGoal(Number(e.target.value))}
                className="mt-1 -ml-1 cursor-pointer rounded bg-transparent text-xs text-muted outline-none hover:text-fg"
              >
                {[15, 30, 50, 80].map((g) => (
                  <option key={g} value={g} className="bg-surface text-fg">
                    {g} XP · {g <= 15 ? "casual" : g <= 30 ? "regular" : g <= 50 ? "serious" : "intense"}
                  </option>
                ))}
              </select>
            </div>
          </div>

          <Link
            href="/practice"
            className="group flex items-center justify-between rounded-2xl border border-accent/40 bg-accent/10 p-5 transition hover:bg-accent/15"
          >
            <div>
              <p className="flex items-center gap-2 text-sm text-muted"><Layers className="h-4 w-4" /> Flashcards</p>
              <p className="mt-1 font-display text-2xl font-semibold">
                {dueCount + Math.min(newCount, 10)} <span className="text-sm font-normal text-muted">to review</span>
              </p>
            </div>
            <ArrowRight className="h-5 w-5 text-accent transition group-hover:translate-x-1" />
          </Link>
        </section>

        {/* Tabs */}
        <div className="mt-10 flex items-center justify-between border-b border-line">
          <div className="flex gap-1" role="tablist">
            {(["learn", "lexicon"] as const).map((t) => (
              <button
                key={t}
                role="tab"
                aria-selected={tab === t}
                onClick={() => setTab(t)}
                className={`relative px-4 py-3 text-sm font-medium capitalize transition ${tab === t ? "text-fg" : "text-muted hover:text-fg"}`}
              >
                {t === "learn" ? "Courses" : "Lexicon"}
                {tab === t && <span className="absolute inset-x-3 -bottom-px h-0.5 rounded bg-accent" />}
              </button>
            ))}
          </div>
          <p className="hidden text-sm text-muted sm:block">{done} of {totalLessons} lessons completed</p>
        </div>

        <div className="mt-8">{tab === "learn" ? <LearnTab /> : <LexiconTab />}</div>
      </main>
    </div>
  );
}

function greeting() {
  const h = new Date().getHours();
  return h < 12 ? "Good morning" : h < 18 ? "Good afternoon" : "Good evening";
}

function Stat({ label, icon, children }: { label: string; icon: React.ReactNode; children: React.ReactNode }) {
  return (
    <div className="rounded-2xl border border-line bg-surface p-5">
      <div className="mb-2 flex items-center gap-2 text-sm text-muted">{icon}{label}</div>
      {children}
    </div>
  );
}

/* ------------------------------------------------------------------ */
/* Courses + lesson reader                                            */
/* ------------------------------------------------------------------ */

function LearnTab() {
  const progress = useProgress();
  const firstOpen = initialCourses.find((c) => c.lessons.some((l) => !progress.completedLessons.includes(l.id))) ?? initialCourses[0];
  const [courseId, setCourseId] = useState<number>(firstOpen.id);
  const course = initialCourses.find((c) => c.id === courseId) as Course;
  const firstTodo = course.lessons.find((l) => !progress.completedLessons.includes(l.id)) ?? course.lessons[0];
  const [lessonId, setLessonId] = useState<number>(firstTodo.id);
  const lesson = course.lessons.find((l) => l.id === lessonId) ?? course.lessons[0];

  const openLesson = (c: Course, l: Lesson) => {
    setCourseId(c.id);
    setLessonId(l.id);
    // On phones the reader sits above the course list, so bring it into view.
    if (typeof window !== "undefined" && window.innerWidth < 1024) window.scrollTo({ top: 0, behavior: "smooth" });
  };

  const next = (() => {
    const flat = initialCourses.flatMap((c) => c.lessons.map((l) => [c, l] as const));
    const idx = flat.findIndex(([, l]) => l.id === lesson.id);
    return flat[idx + 1];
  })();

  return (
    <div className="grid gap-6 lg:grid-cols-[320px_1fr]">
      <aside className="order-2 space-y-2 lg:order-1" aria-label="Courses">
        {initialCourses.map((c) => {
          const finished = c.lessons.filter((l) => progress.completedLessons.includes(l.id)).length;
          const open = c.id === courseId;
          return (
            <div key={c.id} className="overflow-hidden rounded-2xl border border-line bg-surface">
              <button
                onClick={() => setCourseId(open ? -1 : c.id)}
                aria-expanded={open}
                className="flex w-full items-center gap-3 p-4 text-left transition hover:bg-raised/60"
              >
                <ProgressRing value={finished / c.lessons.length} size={44} stroke={4}>
                  <span className="text-[11px]">{c.cefr_level}</span>
                </ProgressRing>
                <div className="min-w-0 flex-1">
                  <p className="truncate text-sm font-semibold">{c.title_en ?? c.title}</p>
                  <p className="text-xs text-muted">{finished}/{c.lessons.length} lessons</p>
                </div>
                <ChevronDown className={`h-4 w-4 text-muted transition ${open ? "rotate-180" : ""}`} />
              </button>
              {open && (
                <ul className="border-t border-line p-2">
                  {c.lessons.map((l) => {
                    const complete = progress.completedLessons.includes(l.id);
                    const current = l.id === lesson.id;
                    return (
                      <li key={l.id}>
                        <button
                          onClick={() => openLesson(c, l)}
                          className={`flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-sm transition ${current ? "bg-accent/10 text-fg" : "text-muted hover:bg-raised hover:text-fg"}`}
                        >
                          <span className={`grid h-5 w-5 shrink-0 place-items-center rounded-full border text-[10px] ${complete ? "border-success bg-success text-bg" : "border-line"}`}>
                            {complete ? <Check className="h-3 w-3" /> : l.order_num}
                          </span>
                          <span className="line-clamp-2">{lessonTitle(l)}</span>
                        </button>
                      </li>
                    );
                  })}
                </ul>
              )}
            </div>
          );
        })}
      </aside>

      <div className="order-1 min-w-0 lg:order-2">
        <LessonReader
          key={lesson.id}
          course={course}
          lesson={lesson}
          onNext={next ? () => openLesson(next[0], next[1]) : undefined}
        />
      </div>
    </div>
  );
}

function LessonReader({ course, lesson, onNext }: { course: Course; lesson: Lesson; onNext?: () => void }) {
  const progress = useProgress();
  const { speak, speaking } = useSpeech();
  const [picked, setPicked] = useState<number | null>(null);
  const [gained, setGained] = useState<number | null>(null);
  const completed = progress.completedLessons.includes(lesson.id);
  const answered = picked !== null;
  const correct = picked === lesson.quiz.correct_index;

  const choose = (i: number) => {
    if (answered) return;
    setPicked(i);
    setGained(actions.completeLesson(lesson.id, i === lesson.quiz.correct_index));
  };

  const phrase = lesson.audio_phrase ?? lesson.content_fr;

  return (
    <article className="animate-rise space-y-5">
      <header>
        <p className="text-sm text-muted">
          {course.cefr_level} · Lesson {lesson.order_num} · {lesson.duration_mins ?? 15} min
          {completed && <span className="ml-2 inline-flex items-center gap-1 text-success"><CheckCircle2 className="h-3.5 w-3.5" /> Completed</span>}
        </p>
        <h2 className="mt-1 font-display text-3xl font-medium tracking-tight sm:text-4xl">{lessonTitle(lesson)}</h2>
        {lesson.title_en && <p className="mt-1 text-muted">{lesson.title}</p>}
      </header>

      {/* Passage */}
      <section className="relative overflow-hidden rounded-3xl border border-line bg-surface p-6 sm:p-8">
        <div className="ambient absolute inset-0 opacity-60" />
        <div className="relative">
          <div className="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <p className="font-display text-2xl leading-relaxed sm:text-3xl">{lesson.content_fr}</p>
            <div className="flex shrink-0 gap-2">
              <button
                onClick={() => speak(phrase)}
                aria-label="Listen"
                className={`grid h-11 w-11 place-items-center rounded-full bg-accent text-accent-fg transition hover:scale-105 ${speaking === phrase ? "shadow-glow" : ""}`}
              >
                <Volume2 className="h-5 w-5" />
              </button>
              <button
                onClick={() => speak(phrase, 0.6)}
                aria-label="Listen slowly"
                title="Slow"
                className="grid h-11 items-center rounded-full border border-line px-3.5 text-xs font-medium text-muted transition hover:border-accent hover:text-fg"
              >
                0.6×
              </button>
            </div>
          </div>
          <div className="mt-6 grid gap-4 border-t border-line pt-5 sm:grid-cols-2">
            <div>
              <p className="text-xs font-medium uppercase tracking-wider text-muted">English</p>
              <p className="mt-1.5 leading-relaxed">{lesson.content_en}</p>
            </div>
            <div>
              <p className="text-xs font-medium uppercase tracking-wider text-muted">Focus</p>
              <p className="mt-1.5 leading-relaxed text-muted">{lesson.section_title}</p>
            </div>
          </div>
        </div>
      </section>

      {/* Quiz */}
      <section className="rounded-3xl border border-line bg-surface p-6 sm:p-8">
        <p className="flex items-center gap-2 text-sm font-medium text-accent"><BookOpen className="h-4 w-4" /> Check your understanding</p>
        <h3 className="mt-3 font-display text-xl font-medium leading-snug sm:text-2xl">{lesson.quiz.question_en ?? lesson.quiz.question}</h3>
        {lesson.quiz.question_en && <p className="mt-1 text-sm text-muted">{lesson.quiz.question}</p>}

        <div className="mt-5 grid gap-2.5">
          {lesson.quiz.options.map((o, i) => {
            const isRight = i === lesson.quiz.correct_index;
            const isPicked = i === picked;
            const state = !answered
              ? "border-line hover:border-accent/60 hover:bg-raised"
              : isRight
              ? "border-success bg-success/10"
              : isPicked
              ? "border-danger bg-danger/10"
              : "border-line opacity-60";
            return (
              <button
                key={i}
                onClick={() => choose(i)}
                disabled={answered}
                className={`flex items-center justify-between gap-3 rounded-xl border px-4 py-3.5 text-left text-sm transition ${state}`}
              >
                <span>{o}</span>
                {answered && isRight && <CheckCircle2 className="h-5 w-5 shrink-0 text-success" />}
                {answered && isPicked && !isRight && <XCircle className="h-5 w-5 shrink-0 text-danger" />}
              </button>
            );
          })}
        </div>

        {answered && (
          <div className="mt-5 animate-rise rounded-2xl bg-raised p-4 text-sm">
            <p className={`font-medium ${correct ? "text-success" : "text-danger"}`}>
              {correct ? "Correct!" : "Not quite."}
              {gained ? <span className="ml-2 text-gold">+{gained} XP</span> : null}
            </p>
            <p className="mt-1.5 leading-relaxed text-muted">{lesson.quiz.explanation}</p>
          </div>
        )}
      </section>

      <div className="flex flex-wrap items-center justify-between gap-3">
        {!answered && !completed ? (
          <button
            onClick={() => setGained(actions.completeLesson(lesson.id, false))}
            className="text-sm text-muted underline-offset-4 hover:text-fg hover:underline"
          >
            Mark as complete without the quiz
          </button>
        ) : (
          <span />
        )}
        {onNext && (completed || answered) && (
          <button
            onClick={onNext}
            className="inline-flex h-11 items-center gap-2 rounded-full bg-accent px-6 text-sm font-medium text-accent-fg shadow-glow transition hover:brightness-110"
          >
            Next lesson <ArrowRight className="h-4 w-4" />
          </button>
        )}
      </div>
    </article>
  );
}

/* ------------------------------------------------------------------ */
/* Lexicon                                                            */
/* ------------------------------------------------------------------ */

function LexiconTab() {
  const lexicon = useLexicon();
  const progress = useProgress();
  const { speak, speaking } = useSpeech();
  const [q, setQ] = useState("");
  const [level, setLevel] = useState("ALL");
  const [category, setCategory] = useState("All");

  const categories = useMemo(() => ["All", ...Array.from(new Set(lexicon.map((w) => w.category)))], [lexicon]);

  const words = useMemo(() => {
    const s = q.trim().toLowerCase();
    return lexicon.filter(
      (w) =>
        (level === "ALL" || w.cefr_level === level) &&
        (category === "All" || w.category === category) &&
        (!s ||
          w.french_word.toLowerCase().includes(s) ||
          w.meaning_en.toLowerCase().includes(s) ||
          w.meaning_hi.includes(s) ||
          w.use_case.toLowerCase().includes(s))
    );
  }, [lexicon, q, level, category]);

  return (
    <div>
      <div className="flex flex-col gap-3 lg:flex-row lg:items-center">
        <label className="relative flex-1">
          <Search className="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-muted" />
          <input
            value={q}
            onChange={(e) => setQ(e.target.value)}
            placeholder="Search French, English or Hindi…"
            className="h-12 w-full rounded-full border border-line bg-surface pl-11 pr-4 text-sm outline-none transition placeholder:text-muted focus:border-accent"
          />
        </label>
        <div className="flex flex-wrap gap-1.5">
          {LEVELS.map((l) => (
            <button
              key={l}
              onClick={() => setLevel(l)}
              className={`rounded-full px-3.5 py-2 text-xs font-medium transition ${level === l ? "bg-fg text-bg" : "border border-line text-muted hover:text-fg"}`}
            >
              {l === "ALL" ? "All" : l}
            </button>
          ))}
        </div>
        <select
          value={category}
          onChange={(e) => setCategory(e.target.value)}
          aria-label="Category"
          className="h-12 rounded-full border border-line bg-surface px-4 text-sm outline-none focus:border-accent"
        >
          {categories.map((c) => <option key={c}>{c}</option>)}
        </select>
      </div>

      <div className="mt-4 flex items-center justify-between text-sm text-muted">
        <p>{words.length} {words.length === 1 ? "word" : "words"}</p>
        <label className="flex cursor-pointer items-center gap-2">
          <input type="checkbox" checked={progress.showHindi} onChange={(e) => actions.setShowHindi(e.target.checked)} className="accent-[rgb(var(--accent))]" />
          Show Hindi
        </label>
      </div>

      <ul className="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        {words.map((w) => {
          const card = progress.cards[w.id];
          return (
            <li key={`${w.id}-${w.french_word}`} className="flex flex-col rounded-2xl border border-line bg-surface p-5 transition hover:border-accent/40 hover:shadow-soft">
              <div className="flex items-start justify-between gap-3">
                <div className="min-w-0">
                  <h3 className="font-display text-xl font-semibold">{w.french_word}</h3>
                  <p className="mt-0.5 font-mono text-xs text-muted">{w.ipa_phonetic} · {w.part_of_speech}</p>
                </div>
                <button
                  onClick={() => speak(w.french_word)}
                  aria-label={`Pronounce ${w.french_word}`}
                  className={`grid h-9 w-9 shrink-0 place-items-center rounded-full border border-line text-muted transition hover:border-accent hover:text-accent ${speaking === w.french_word ? "border-accent text-accent" : ""}`}
                >
                  <Volume2 className="h-4 w-4" />
                </button>
              </div>
              <p className="mt-3 text-sm">{w.meaning_en}</p>
              {progress.showHindi && <p className="mt-1 text-sm text-muted">{w.meaning_hi}</p>}
              <button
                onClick={() => speak(w.example_sentence)}
                className="mt-3 rounded-xl bg-raised px-3 py-2.5 text-left text-sm italic leading-relaxed text-muted transition hover:text-fg"
              >
                “{w.example_sentence}”
              </button>
              <div className="mt-auto flex items-center gap-2 pt-4 text-[11px] font-medium text-muted">
                <span className="rounded-full bg-raised px-2.5 py-1">{w.cefr_level}</span>
                <span className="truncate rounded-full bg-raised px-2.5 py-1">{w.category}</span>
                {card && <span className="ml-auto rounded-full bg-success/15 px-2.5 py-1 text-success">learning</span>}
              </div>
            </li>
          );
        })}
      </ul>
      {words.length === 0 && (
        <p className="mt-10 rounded-2xl border border-dashed border-line p-12 text-center text-sm text-muted">No words match your filters.</p>
      )}
    </div>
  );
}

export default function DashboardPage() {
  return (
    <Suspense fallback={<div className="min-h-screen" />}>
      <Dashboard />
    </Suspense>
  );
}
