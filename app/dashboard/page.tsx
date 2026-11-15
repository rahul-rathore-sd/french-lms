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
  ChevronRight,
  Flame,
  Layers,
  Search,
  Sparkles,
  Volume2,
  XCircle,
} from "lucide-react";
import SiteHeader from "@/components/SiteHeader";
import MobileTabBar from "@/components/MobileTabBar";
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

  const name = user?.firstName ?? null;

  return (
    <div className="min-h-screen">
      <SiteHeader />
      <main className="mx-auto max-w-6xl px-3.5 py-4 sm:px-6 sm:py-8">
        {/* Greeting */}
        <div className="animate-rise">
          <p className="text-xs sm:text-sm text-muted">{greeting()}</p>
          <h1 className="mt-0.5 font-display text-2xl font-medium tracking-tight sm:text-5xl">
            {name ? `Bonjour, ${name}.` : "Bonjour !"}
          </h1>
        </div>

        {/* Phones: compact 3-metric progress summary card + practice button */}
        {tab === "learn" && (
          <section className="mt-3.5 space-y-2 sm:hidden" aria-label="Your progress">
            <div className="grid grid-cols-3 divide-x divide-line rounded-2xl border border-line bg-surface py-2.5">
              <div className="px-2 text-center">
                <Flame className={`mx-auto h-4 w-4 ${streak ? "text-orange-500" : "text-muted"}`} />
                <p className="mt-1 font-display text-lg font-semibold leading-none">{streak}</p>
                <p className="mt-0.5 text-[10px] text-muted">day streak</p>
              </div>
              <div className="px-2 text-center">
                <Sparkles className="mx-auto h-4 w-4 text-gold" />
                <p className="mt-1 font-display text-lg font-semibold leading-none">{progress.xp}</p>
                <p className="mt-0.5 text-[10px] text-muted">XP · Lv {level}</p>
              </div>
              <button
                type="button"
                aria-label={`Daily goal ${todayXp} of ${progress.dailyGoal} XP. Tap to change`}
                onClick={() => {
                  const goals = [15, 30, 50, 80];
                  actions.setDailyGoal(goals[(goals.indexOf(progress.dailyGoal) + 1) % goals.length]);
                }}
                className="flex flex-col items-center px-1.5 active:scale-95 transition-transform"
              >
                <ProgressRing value={goalPct} size={28} stroke={3.5}>
                  {goalPct >= 1 ? <Check className="h-3 w-3 text-success" /> : null}
                </ProgressRing>
                <p className="mt-0.5 text-[10px] leading-tight text-muted">{todayXp}/{progress.dailyGoal} XP</p>
              </button>
            </div>
            <Link
              href="/practice"
              className="flex h-11 items-center justify-between rounded-xl border border-accent/40 bg-accent/10 px-3.5 text-xs sm:text-sm active:bg-accent/20 transition-colors"
            >
              <span className="flex items-center gap-2">
                <Layers className="h-4 w-4 text-accent shrink-0" />
                <span className="truncate"><b>{dueCount + Math.min(newCount, 10)}</b> flashcards to review</span>
              </span>
              <ArrowRight className="h-3.5 w-3.5 text-accent shrink-0" />
            </Link>
          </section>
        )}

        {/* Stat row (tablet and desktop) */}
        <section className="mt-8 hidden gap-3 sm:grid sm:grid-cols-2 lg:grid-cols-4" aria-label="Your progress">
          <Stat label="Day streak" icon={<Flame className={`h-5 w-5 ${streak ? "text-orange-500" : "text-muted"}`} />}>
            <span className="font-display text-2xl font-semibold sm:text-3xl">{streak}</span>
            <span className="ml-1 text-sm text-muted">{streak === 1 ? "day" : "days"}</span>
          </Stat>

          <Stat label={`Level ${level}`} icon={<Sparkles className="h-5 w-5 text-gold" />}>
            <span className="font-display text-2xl font-semibold sm:text-3xl">{progress.xp}</span>
            <span className="ml-1 text-sm text-muted">XP</span>
            <div className="mt-3 h-1.5 overflow-hidden rounded-full bg-raised">
              <div className="h-full rounded-full bg-gold transition-all duration-700" style={{ width: `${(into / needed) * 100}%` }} />
            </div>
          </Stat>

          <div className="flex flex-col items-start gap-2 rounded-2xl border border-line bg-surface p-4 sm:flex-row sm:items-center sm:gap-4 sm:p-5">
            <ProgressRing value={goalPct} size={52}>
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
            className="group flex items-center justify-between gap-2 rounded-2xl border border-accent/40 bg-accent/10 p-4 transition hover:bg-accent/15 sm:p-5"
          >
            <div>
              <p className="flex items-center gap-2 text-sm text-muted"><Layers className="h-4 w-4" /> Flashcards</p>
              <p className="mt-1 font-display text-xl font-semibold sm:text-2xl">
                {dueCount + Math.min(newCount, 10)} <span className="text-sm font-normal text-muted">to review</span>
              </p>
            </div>
            <ArrowRight className="h-5 w-5 text-accent transition group-hover:translate-x-1" />
          </Link>
        </section>

        {/* Tab Header */}
        <div className="mt-4 flex items-center justify-between border-b border-line sm:mt-10">
          <div className="flex gap-1" role="tablist">
            {(["learn", "lexicon"] as const).map((t) => (
              <button
                key={t}
                role="tab"
                aria-selected={tab === t}
                onClick={() => setTab(t)}
                className={`relative px-3.5 py-2.5 text-xs sm:text-sm font-medium capitalize transition ${tab === t ? "text-fg" : "text-muted hover:text-fg"}`}
              >
                {t === "learn" ? "Courses & Syllabi" : "Lexicon Sanctuary"}
                {tab === t && <span className="absolute inset-x-2.5 -bottom-px h-0.5 rounded bg-accent" />}
              </button>
            ))}
          </div>
          <p className="hidden text-xs text-muted sm:block">{done} of {totalLessons} lessons completed</p>
        </div>

        <div className="mt-4 sm:mt-8">{tab === "learn" ? <LearnTab /> : <LexiconTab />}</div>
      </main>
      <MobileTabBar />
    </div>
  );
}

function greeting() {
  const h = new Date().getHours();
  return h < 12 ? "Bonjour · Matinée" : h < 18 ? "Bonjour · Après-midi" : "Bonsoir · Soirée";
}

function Stat({ label, icon, children }: { label: string; icon: React.ReactNode; children: React.ReactNode }) {
  return (
    <div className="rounded-2xl border border-line bg-surface p-4 sm:p-5">
      <div className="mb-1.5 flex items-center gap-2 text-sm text-muted sm:mb-2">{icon}{label}</div>
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
    if (typeof window !== "undefined" && window.innerWidth < 1024) window.scrollTo({ top: 0, behavior: "smooth" });
  };

  const next = (() => {
    const flat = initialCourses.flatMap((c) => c.lessons.map((l) => [c, l] as const));
    const idx = flat.findIndex(([, l]) => l.id === lesson.id);
    return flat[idx + 1];
  })();

  return (
    <div className="grid gap-5 lg:grid-cols-[320px_1fr]">
      {/* Desktop/Tablet Sidebar Accordion */}
      <aside className="order-2 hidden space-y-2 lg:order-1 lg:block" aria-label="Courses">
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
                  <span className="text-[11px] font-semibold">{c.cefr_level}</span>
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

      {/* Main Lesson Reader Container */}
      <div className="order-1 min-w-0 lg:order-2">
        {/* Mobile-First Interactive CEFR & Lesson Selector */}
        <div className="mb-4 space-y-2.5 lg:hidden" aria-label="Mobile Level & Lesson Selector">
          {/* Horizontal Swipeable CEFR Level Pills */}
          <div className="-mx-3.5 px-3.5 flex overflow-x-auto no-scrollbar gap-1.5 py-1">
            {initialCourses.map((c) => {
              const active = c.id === course.id;
              const finished = c.lessons.filter((l) => progress.completedLessons.includes(l.id)).length;
              return (
                <button
                  key={c.id}
                  onClick={() => {
                    const firstL = c.lessons.find((l) => !progress.completedLessons.includes(l.id)) ?? c.lessons[0];
                    openLesson(c, firstL);
                  }}
                  className={`flex shrink-0 items-center gap-1.5 rounded-full px-3.5 py-1.5 text-xs font-semibold transition active:scale-95 ${
                    active
                      ? "bg-accent text-accent-fg shadow-glow"
                      : "border border-line bg-surface text-muted hover:text-fg"
                  }`}
                >
                  <span>{c.cefr_level}</span>
                  {finished === c.lessons.length && <Check className="h-3 w-3 text-success" />}
                </button>
              );
            })}
          </div>

          {/* Horizontal Swipeable Lessons for Current Level */}
          <div className="-mx-3.5 px-3.5 flex overflow-x-auto no-scrollbar gap-2 py-0.5">
            {course.lessons.map((l) => {
              const current = l.id === lesson.id;
              const complete = progress.completedLessons.includes(l.id);
              return (
                <button
                  key={l.id}
                  onClick={() => openLesson(course, l)}
                  className={`flex shrink-0 items-center gap-2 rounded-xl px-3 py-2 text-xs transition active:scale-95 border ${
                    current
                      ? "border-accent bg-accent/10 text-fg font-medium"
                      : "border-line bg-surface text-muted hover:text-fg"
                  }`}
                >
                  <span
                    className={`grid h-4 w-4 shrink-0 place-items-center rounded-full text-[9px] ${
                      complete ? "bg-success text-bg" : "border border-line"
                    }`}
                  >
                    {complete ? <Check className="h-2.5 w-2.5" /> : l.order_num}
                  </span>
                  <span className="truncate max-w-[140px]">{lessonTitle(l)}</span>
                </button>
              );
            })}
          </div>
        </div>

        {/* Lesson Reader Card */}
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
    <article className="animate-rise space-y-4 sm:space-y-5">
      <header>
        <div className="flex items-center gap-2 text-xs text-muted">
          <span className="rounded-full bg-fg px-2 py-0.5 text-[10px] font-bold text-bg">{course.cefr_level}</span>
          <span>Leçon {lesson.order_num}</span>
          <span>·</span>
          <span>{lesson.duration_mins ?? 15} min</span>
          {completed && (
            <span className="ml-auto inline-flex items-center gap-1 text-[11px] font-semibold text-success">
              <CheckCircle2 className="h-3 w-3" /> Terminé
            </span>
          )}
        </div>
        <h2 className="mt-1.5 font-display text-2xl font-medium tracking-tight sm:text-4xl">{lessonTitle(lesson)}</h2>
        {lesson.title_en && <p className="mt-0.5 text-xs sm:text-sm text-muted">{lesson.title}</p>}
      </header>

      {/* French Corpus & Audio Passage */}
      <section className="relative overflow-hidden rounded-2xl border border-line bg-surface p-4 sm:p-8 sm:rounded-3xl shadow-soft">
        <div className="ambient absolute inset-0 opacity-60 pointer-events-none" />
        <div className="relative space-y-4">
          <p className="font-display text-xl leading-relaxed sm:text-3xl text-fg italic font-normal">
            &ldquo;{lesson.content_fr}&rdquo;
          </p>

          {/* Audio Action Bar */}
          <div className="flex flex-wrap items-center gap-2 pt-1 border-t border-line/60">
            <button
              onClick={() => speak(phrase)}
              aria-label="Listen to French speech"
              className={`inline-flex h-10 items-center gap-2 rounded-full bg-accent px-4 text-xs font-semibold text-accent-fg transition hover:scale-[1.02] active:scale-95 ${
                speaking === phrase ? "shadow-glow" : ""
              }`}
            >
              <Volume2 className="h-4 w-4" />
              <span>Écouter (1.0×)</span>
            </button>
            <button
              onClick={() => speak(phrase, 0.65)}
              aria-label="Listen slowly"
              title="Vitesse ralentie"
              className="inline-flex h-10 items-center rounded-full border border-line px-3.5 text-xs font-medium text-muted transition hover:border-accent hover:text-fg active:scale-95"
            >
              <span>Ralenti (0.65×)</span>
            </button>
          </div>

          {/* Translation and Focus Details */}
          <div className="grid gap-3 border-t border-line pt-3.5 sm:grid-cols-2 text-xs sm:text-sm">
            <div>
              <p className="text-[10px] font-bold uppercase tracking-wider text-muted">Traduction (English)</p>
              <p className="mt-1 leading-relaxed text-fg">{lesson.content_en}</p>
            </div>
            <div>
              <p className="text-[10px] font-bold uppercase tracking-wider text-muted">Point Philologique</p>
              <p className="mt-1 leading-relaxed text-muted">{lesson.section_title}</p>
            </div>
          </div>
        </div>
      </section>

      {/* Interactive Acoustic Quiz */}
      <section className="rounded-2xl border border-line bg-surface p-4 sm:p-8 sm:rounded-3xl shadow-soft">
        <p className="flex items-center gap-2 text-xs sm:text-sm font-semibold text-accent">
          <BookOpen className="h-3.5 w-3.5" /> Contrôle Acoustique & Compréhension
        </p>
        <h3 className="mt-2 font-display text-base sm:text-2xl font-medium leading-snug">
          {lesson.quiz.question_en ?? lesson.quiz.question}
        </h3>
        {lesson.quiz.question_en && <p className="mt-1 text-xs text-muted">{lesson.quiz.question}</p>}

        <div className="mt-4 grid gap-2">
          {lesson.quiz.options.map((o, i) => {
            const isRight = i === lesson.quiz.correct_index;
            const isPicked = i === picked;
            const state = !answered
              ? "border-line hover:border-accent/60 hover:bg-raised active:bg-raised/80"
              : isRight
              ? "border-success bg-success/15 font-semibold"
              : isPicked
              ? "border-danger bg-danger/15"
              : "border-line opacity-50";
            return (
              <button
                key={i}
                onClick={() => choose(i)}
                disabled={answered}
                className={`flex min-h-[46px] items-center justify-between gap-3 rounded-xl border px-3.5 py-3 text-left text-xs sm:text-sm transition active:scale-[0.99] ${state}`}
              >
                <span className="leading-snug">{o}</span>
                {answered && isRight && <CheckCircle2 className="h-4 w-4 shrink-0 text-success" />}
                {answered && isPicked && !isRight && <XCircle className="h-4 w-4 shrink-0 text-danger" />}
              </button>
            );
          })}
        </div>

        {answered && (
          <div className="mt-4 animate-rise rounded-xl bg-raised p-3.5 text-xs sm:text-sm">
            <p className={`font-semibold flex items-center justify-between ${correct ? "text-success" : "text-danger"}`}>
              <span>{correct ? "Excellente réponse ! 🍁" : "Réponse incorrecte."}</span>
              {gained ? <span className="text-gold font-bold">+{gained} XP</span> : null}
            </p>
            <p className="mt-1 leading-relaxed text-muted">{lesson.quiz.explanation}</p>
          </div>
        )}
      </section>

      {/* Navigation Footer */}
      <div className="flex flex-wrap items-center justify-between gap-3 pt-1">
        {!answered && !completed ? (
          <button
            onClick={() => setGained(actions.completeLesson(lesson.id, false))}
            className="text-xs text-muted underline-offset-4 hover:text-fg hover:underline"
          >
            Marquer comme terminé sans le quiz
          </button>
        ) : (
          <span />
        )}
        {onNext && (completed || answered) && (
          <button
            onClick={onNext}
            className="inline-flex h-11 items-center gap-2 rounded-full bg-accent px-5 text-xs sm:text-sm font-semibold text-accent-fg shadow-glow transition hover:brightness-110 active:scale-95"
          >
            <span>Leçon suivante</span>
            <ArrowRight className="h-4 w-4" />
          </button>
        )}
      </div>
    </article>
  );
}

/* ------------------------------------------------------------------ */
/* Lexicon Sanctuary Tab                                              */
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
    <div className="space-y-4">
      {/* Mobile-Friendly Search & Horizontal Category Filter */}
      <div className="space-y-2.5">
        <label className="relative block">
          <Search className="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-muted" />
          <input
            value={q}
            onChange={(e) => setQ(e.target.value)}
            placeholder="Rechercher (Français, हिन्दी, English)..."
            className="h-11 w-full rounded-xl border border-line bg-surface pl-10 pr-4 text-xs sm:text-sm outline-none transition placeholder:text-muted focus:border-accent"
          />
        </label>

        {/* Horizontal Scrolling CEFR Level Pills */}
        <div className="-mx-3.5 px-3.5 flex overflow-x-auto no-scrollbar gap-1.5 py-0.5">
          {LEVELS.map((l) => (
            <button
              key={l}
              onClick={() => setLevel(l)}
              className={`shrink-0 rounded-full px-3 py-1 text-xs font-semibold transition active:scale-95 ${
                level === l
                  ? "bg-accent text-accent-fg shadow-glow"
                  : "border border-line bg-surface text-muted hover:text-fg"
              }`}
            >
              {l === "ALL" ? "Tous Niveaux" : l}
            </button>
          ))}
        </div>

        {/* Category & Hindi toggle */}
        <div className="flex items-center justify-between gap-2 pt-1 text-xs text-muted">
          <select
            value={category}
            onChange={(e) => setCategory(e.target.value)}
            aria-label="Category"
            className="h-9 rounded-lg border border-line bg-surface px-2.5 text-xs outline-none focus:border-accent max-w-[180px] truncate"
          >
            {categories.map((c) => (
              <option key={c}>{c}</option>
            ))}
          </select>

          <label className="flex cursor-pointer items-center gap-1.5 shrink-0 select-none">
            <input
              type="checkbox"
              checked={progress.showHindi}
              onChange={(e) => actions.setShowHindi(e.target.checked)}
              className="accent-[rgb(var(--accent))] rounded"
            />
            <span className="font-medium text-fg">Traductions हिन्दी</span>
          </label>
        </div>
      </div>

      <div className="text-xs text-muted">
        <span>{words.length} {words.length === 1 ? "terme trouvé" : "termes trouvés"}</span>
      </div>

      {/* Vocabulary Cards Grid */}
      <ul className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        {words.map((w) => {
          const card = progress.cards[w.id];
          return (
            <li
              key={`${w.id}-${w.french_word}`}
              className="flex flex-col rounded-2xl border border-line bg-surface p-4 sm:p-5 transition hover:border-accent/40 shadow-soft"
            >
              <div className="flex items-start justify-between gap-3">
                <div className="min-w-0">
                  <h3 className="font-display text-lg sm:text-xl font-semibold leading-tight text-fg">
                    {w.french_word}
                  </h3>
                  <p className="mt-0.5 font-mono text-[11px] text-muted truncate">
                    {w.ipa_phonetic} · {w.part_of_speech}
                  </p>
                </div>
                <button
                  onClick={() => speak(w.french_word)}
                  aria-label={`Prononcer ${w.french_word}`}
                  className={`grid h-9 w-9 shrink-0 place-items-center rounded-full border border-line text-muted transition hover:border-accent hover:text-accent active:scale-95 ${
                    speaking === w.french_word ? "border-accent text-accent shadow-glow" : ""
                  }`}
                >
                  <Volume2 className="h-4 w-4" />
                </button>
              </div>

              <div className="mt-2.5 space-y-1.5 text-xs sm:text-sm">
                <p className="font-medium text-fg">{w.meaning_en}</p>
                {progress.showHindi && (
                  <p className="text-amber-600 dark:text-amber-400 font-semibold bg-amber-500/5 dark:bg-amber-500/10 p-2 rounded-lg border border-amber-500/20">
                    हिन्दी : {w.meaning_hi}
                  </p>
                )}
              </div>

              <button
                onClick={() => speak(w.example_sentence)}
                className="mt-2.5 rounded-xl bg-raised px-3 py-2 text-left text-xs italic leading-relaxed text-muted transition hover:text-fg active:bg-raised/70"
              >
                &ldquo;{w.example_sentence}&rdquo;
              </button>

              <div className="mt-auto flex items-center gap-1.5 pt-3 text-[10px] font-semibold text-muted">
                <span className="rounded-full bg-raised px-2.5 py-0.5">{w.cefr_level}</span>
                <span className="truncate rounded-full bg-raised px-2.5 py-0.5">{w.category}</span>
                {card && <span className="ml-auto rounded-full bg-success/15 px-2 py-0.5 text-success">Appris</span>}
              </div>
            </li>
          );
        })}
      </ul>

      {words.length === 0 && (
        <p className="mt-8 rounded-2xl border border-dashed border-line p-8 text-center text-xs text-muted">
          Aucun terme ne correspond à vos filtres.
        </p>
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
