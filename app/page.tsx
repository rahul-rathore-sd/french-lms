import Link from "next/link";
import { ArrowRight, Headphones, BookOpenText, Repeat2, TrendingUp, Check } from "lucide-react";
import SiteHeader from "@/components/SiteHeader";
import HeroDemo from "@/components/HeroDemo";
import LexiconPreview from "@/components/LexiconPreview";
import { initialCourses, initialVocabulary } from "@/lib/data";

const method = [
  { icon: Headphones, title: "Listen first", body: "Every phrase has native-quality audio and IPA, so your ear is trained before your eyes." },
  { icon: BookOpenText, title: "Read in context", body: "Short, literary and everyday passages with English and Hindi translations side by side." },
  { icon: Repeat2, title: "Remember for good", body: "Spaced-repetition flashcards bring each word back right before you would forget it." },
  { icon: TrendingUp, title: "See real progress", body: "Streaks, XP and a daily goal keep the habit alive, and every level maps to CEFR A1–C2." },
];

export default function HomePage() {
  const lessons = initialCourses.reduce((n, c) => n + c.lessons.length, 0);

  return (
    <div className="min-h-screen">
      <SiteHeader variant="marketing" />

      {/* Hero */}
      <section className="ambient relative overflow-hidden">
        <div className="mx-auto grid max-w-6xl gap-12 px-4 pb-20 pt-16 sm:px-6 lg:grid-cols-[1.1fr_1fr] lg:items-center lg:pb-28 lg:pt-24">
          <div className="animate-rise">
            <span className="inline-flex items-center gap-2 rounded-full border border-line bg-surface/70 px-3.5 py-1.5 text-xs font-medium text-muted backdrop-blur">
              <span className="h-1.5 w-1.5 rounded-full bg-success" /> CEFR A1 → C2 · Audio · Flashcards
            </span>
            <h1 className="mt-6 font-display text-5xl font-medium leading-[1.05] tracking-tight sm:text-6xl lg:text-7xl">
              Learn French the way it is <span className="text-accent">actually spoken.</span>
            </h1>
            <p className="mt-6 max-w-xl text-lg leading-relaxed text-muted">
              A calm, beautifully designed place to go from first greetings to literary fluency. Structured courses, real pronunciation, and a lexicon that remembers what you forget.
            </p>
            <div className="mt-8 flex flex-wrap items-center gap-3">
              <Link
                href="/dashboard"
                className="group inline-flex h-12 items-center gap-2 rounded-full bg-accent px-6 text-sm font-medium text-accent-fg shadow-glow transition hover:brightness-110"
              >
                Start learning <ArrowRight className="h-4 w-4 transition group-hover:translate-x-0.5" />
              </Link>
              <Link href="/apply" className="inline-flex h-12 items-center rounded-full border border-line px-6 text-sm font-medium transition hover:border-accent/50">
                Request an invitation
              </Link>
            </div>
            <ul className="mt-8 flex flex-wrap gap-x-6 gap-y-2 text-sm text-muted">
              {["Free to explore", "English & Hindi translations", "Works on any device"].map((t) => (
                <li key={t} className="flex items-center gap-1.5"><Check className="h-4 w-4 text-success" />{t}</li>
              ))}
            </ul>
          </div>
          <HeroDemo />
        </div>
      </section>

      {/* Stats */}
      <section className="border-y border-line/70 bg-surface/50">
        <dl className="mx-auto grid max-w-6xl grid-cols-2 gap-6 px-4 py-8 text-center sm:px-6 md:grid-cols-4">
          {[
            [String(initialCourses.length), "Structured courses"],
            [String(lessons), "Guided lessons"],
            [String(initialVocabulary.length), "Lexicon words"],
            ["A1–C2", "CEFR coverage"],
          ].map(([v, l]) => (
            <div key={l}>
              <dt className="font-display text-3xl font-semibold">{v}</dt>
              <dd className="mt-1 text-sm text-muted">{l}</dd>
            </div>
          ))}
        </dl>
      </section>

      {/* Method */}
      <section id="method" className="mx-auto max-w-6xl scroll-mt-20 px-4 py-24 sm:px-6">
        <p className="text-sm font-medium text-accent">The method</p>
        <h2 className="mt-3 max-w-2xl font-display text-4xl font-medium tracking-tight sm:text-5xl">Four habits that make French stick.</h2>
        <div className="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
          {method.map(({ icon: Icon, title, body }, i) => (
            <div
              key={title}
              className="animate-rise rounded-2xl border border-line bg-surface p-6 transition hover:-translate-y-1 hover:border-accent/40 hover:shadow-soft"
              style={{ animationDelay: `${i * 70}ms` }}
            >
              <span className="grid h-11 w-11 place-items-center rounded-xl bg-accent/10 text-accent"><Icon className="h-5 w-5" /></span>
              <h3 className="mt-5 text-lg font-semibold">{title}</h3>
              <p className="mt-2 text-sm leading-relaxed text-muted">{body}</p>
            </div>
          ))}
        </div>
      </section>

      {/* Levels */}
      <section id="levels" className="scroll-mt-20 border-y border-line/70 bg-surface/40">
        <div className="mx-auto max-w-6xl px-4 py-24 sm:px-6">
          <p className="text-sm font-medium text-accent">Your path</p>
          <h2 className="mt-3 max-w-2xl font-display text-4xl font-medium tracking-tight sm:text-5xl">From bonjour to Balzac.</h2>
          <ol className="mt-12 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            {initialCourses.map((c) => (
              <li key={c.id}>
                <Link
                  href="/dashboard"
                  className="group flex h-full flex-col rounded-2xl border border-line bg-surface p-6 transition hover:-translate-y-1 hover:border-accent/40 hover:shadow-soft"
                >
                  <div className="flex items-center justify-between">
                    <span className="rounded-full bg-fg px-3 py-1 text-xs font-semibold text-bg">{c.cefr_level}</span>
                    <span className="text-xs text-muted">{c.lessons.length} lessons</span>
                  </div>
                  <h3 className="mt-5 font-display text-xl font-semibold leading-snug">{c.title}</h3>
                  <p className="mt-2 flex-1 text-sm leading-relaxed text-muted">{c.description}</p>
                  <span className="mt-5 inline-flex items-center gap-1 text-sm font-medium text-accent">
                    Explore <ArrowRight className="h-4 w-4 transition group-hover:translate-x-1" />
                  </span>
                </Link>
              </li>
            ))}
          </ol>
        </div>
      </section>

      {/* Lexicon */}
      <section id="lexicon" className="mx-auto max-w-6xl scroll-mt-20 px-4 py-24 sm:px-6">
        <p className="text-sm font-medium text-accent">The lexicon</p>
        <h2 className="mt-3 max-w-2xl font-display text-4xl font-medium tracking-tight sm:text-5xl">A living dictionary, with sound.</h2>
        <p className="mt-4 max-w-xl text-muted">Search by French, English or Hindi. Tap the speaker to hear the word.</p>
        <div className="mt-10"><LexiconPreview /></div>
      </section>

      {/* CTA */}
      <section className="mx-auto max-w-6xl px-4 pb-24 sm:px-6">
        <div className="relative overflow-hidden rounded-3xl border border-line bg-surface px-6 py-16 text-center sm:px-12">
          <div className="ambient absolute inset-0" />
          <h2 className="relative font-display text-4xl font-medium tracking-tight sm:text-5xl">Ten minutes a day is enough.</h2>
          <p className="relative mx-auto mt-4 max-w-lg text-muted">Pick up where you left off on any device. Your streak, XP and flashcards are always waiting.</p>
          <Link
            href="/dashboard"
            className="relative mt-8 inline-flex h-12 items-center gap-2 rounded-full bg-accent px-7 text-sm font-medium text-accent-fg shadow-glow transition hover:brightness-110"
          >
            Begin your first lesson <ArrowRight className="h-4 w-4" />
          </Link>
        </div>
      </section>

      <footer className="border-t border-line/70">
        <div className="mx-auto flex max-w-6xl flex-col items-center justify-between gap-3 px-4 py-8 text-sm text-muted sm:flex-row sm:px-6">
          <p>© {new Date().getFullYear()} Académie de Français</p>
          <p>Bonne continuation.</p>
        </div>
      </footer>
    </div>
  );
}
