"use client";

import { Suspense } from "react";
import { useRouter, useSearchParams } from "next/navigation";
import { BookOpenText, CheckCircle2, Feather, GraduationCap, Languages, Library as LibraryIcon, ScrollText } from "lucide-react";
import SiteHeader from "@/components/SiteHeader";
import MobileTabBar from "@/components/MobileTabBar";
import TextReader from "@/components/library/TextReader";
import TranslateDrill from "@/components/library/TranslateDrill";
import GrammarView from "@/components/library/GrammarView";
import ResourcesView from "@/components/library/ResourcesView";
import ExamsView from "@/components/library/ExamsView";
import { useProgress } from "@/lib/progress";
import { LEVELS, Level, SECTIONS, Section, countByLevel, grammar, textsFor } from "@/lib/library";

const ICONS: Record<Section, React.ComponentType<{ className?: string }>> = {
  poetry: Feather,
  literature: BookOpenText,
  translate: Languages,
  grammar: ScrollText,
  exams: GraduationCap,
  resources: LibraryIcon,
};

const isSection = (v: string | null): v is Section => SECTIONS.some((s) => s.id === v);
const isLevel = (v: string | null): v is Level => LEVELS.includes(v as Level);

function Library() {
  const router = useRouter();
  const params = useSearchParams();
  const progress = useProgress();

  const section: Section = isSection(params.get("s")) ? (params.get("s") as Section) : "poetry";
  const level: Level | "ALL" = isLevel(params.get("l")) ? (params.get("l") as Level) : "ALL";
  const slug = params.get("i");

  const go = (next: { s?: Section; l?: Level | "ALL"; i?: string | null }) => {
    const q = new URLSearchParams();
    q.set("s", next.s ?? section);
    const l = next.l ?? level;
    if (l !== "ALL") q.set("l", l);
    if (next.i) q.set("i", next.i);
    router.push(`/library?${q.toString()}`, { scroll: next.i === undefined ? false : true });
  };

  const texts = textsFor(section).filter((t) => level === "ALL" || t.level === level);
  const current = slug ? textsFor(section).find((t) => t.slug === slug) : undefined;
  const topics = grammar.filter((t) => level === "ALL" || t.level === level);
  const drillLevel: Level = level === "ALL" ? "A1" : level;

  const meta = SECTIONS.find((s) => s.id === section)!;

  return (
    <div className="min-h-screen">
      <SiteHeader />
      <main className="mx-auto max-w-6xl px-4 py-6 sm:px-6 sm:py-10">
        {current ? (
          <TextReader item={current} onBack={() => go({ i: null })} />
        ) : (
          <>
            <div className="animate-rise">
              <p className="text-sm font-medium text-accent">Library</p>
              <h1 className="mt-1 font-display text-3xl font-medium tracking-tight sm:text-5xl">Read, translate, understand.</h1>
              <p className="mt-3 max-w-2xl text-sm text-muted sm:text-base">{meta.blurb}</p>
            </div>

            {/* Sections */}
            <div className="-mx-4 mt-6 overflow-x-auto px-4 sm:mx-0 sm:px-0" role="tablist" aria-label="Sections">
              <div className="flex min-w-max gap-2 sm:min-w-0 sm:flex-wrap">
                {SECTIONS.map((s) => {
                  const Icon = ICONS[s.id];
                  const active = s.id === section;
                  return (
                    <button
                      key={s.id}
                      role="tab"
                      aria-selected={active}
                      onClick={() => go({ s: s.id, i: null })}
                      className={`inline-flex h-11 items-center gap-2 rounded-full px-5 text-sm font-medium transition ${active ? "bg-fg text-bg" : "border border-line text-muted hover:text-fg"}`}
                    >
                      <Icon className="h-4 w-4" /> {s.label}
                    </button>
                  );
                })}
              </div>
            </div>

            {/* Levels */}
            {section !== "resources" && <div className="-mx-4 mt-3 overflow-x-auto px-4 sm:mx-0 sm:px-0" role="tablist" aria-label="Level">
              <div className="flex min-w-max gap-1.5 sm:min-w-0 sm:flex-wrap">
                {(["ALL", ...LEVELS] as const).map((l) => {
                  const active = l === level;
                  const n = l === "ALL" ? null : countByLevel(section, l);
                  return (
                    <button
                      key={l}
                      role="tab"
                      aria-selected={active}
                      onClick={() => go({ l, i: null })}
                      className={`inline-flex h-9 items-center gap-1.5 rounded-full px-4 text-xs font-medium transition ${active ? "bg-accent text-accent-fg" : "border border-line text-muted hover:text-fg"}`}
                    >
                      {l === "ALL" ? "All levels" : l}
                      {n !== null && <span className="opacity-60">{n}</span>}
                    </button>
                  );
                })}
              </div>
            </div>}

            <div className="mt-8">
              {section === "translate" ? (
                <>
                  {level === "ALL" && <p className="mb-4 text-center text-sm text-muted">Showing A1. Pick a level above to change difficulty.</p>}
                  <TranslateDrill key={drillLevel} level={drillLevel} />
                </>
              ) : section === "resources" ? (
                <ResourcesView />
              ) : section === "exams" ? (
                <ExamsView level={level} />
              ) : section === "grammar" ? (
                <GrammarView key={level} topics={topics} />
              ) : texts.length === 0 ? (
                <p className="rounded-2xl border border-dashed border-line p-12 text-center text-sm text-muted">Nothing here for this level yet.</p>
              ) : (
                <ul className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                  {texts.map((t) => {
                    const done = progress.completedItems.includes(`text:${t.slug}`);
                    return (
                      <li key={t.slug}>
                        <button
                          onClick={() => go({ i: t.slug })}
                          className="group flex h-full w-full flex-col rounded-2xl border border-line bg-surface p-5 text-left transition hover:-translate-y-0.5 hover:border-accent/40 hover:shadow-soft sm:p-6"
                        >
                          <div className="flex items-center justify-between">
                            <span className="rounded-full bg-fg px-2.5 py-1 text-xs font-semibold text-bg">{t.level}</span>
                            <span className="flex items-center gap-1.5 text-xs text-muted">
                              {done && <CheckCircle2 className="h-4 w-4 text-success" />} {t.minutes} min
                            </span>
                          </div>
                          <h2 className="mt-4 font-display text-xl font-semibold leading-snug">{t.title}</h2>
                          <p className="text-sm text-muted">{t.title_en}</p>
                          <p className="mt-3 line-clamp-3 flex-1 text-sm leading-relaxed text-muted">{t.summary_en}</p>
                          <p className="mt-4 truncate text-xs text-muted">{t.author}{t.year ? ` · ${t.year}` : ""}</p>
                        </button>
                      </li>
                    );
                  })}
                </ul>
              )}
            </div>
          </>
        )}
      </main>
      <MobileTabBar />
    </div>
  );
}

export default function LibraryPage() {
  return (
    <Suspense fallback={<div className="min-h-screen" />}>
      <Library />
    </Suspense>
  );
}
