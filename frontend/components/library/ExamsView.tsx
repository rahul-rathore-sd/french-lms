"use client";

import { useState } from "react";
import { CheckCircle2, ChevronDown, Headphones, Mic, PenLine, Target, Volume2 } from "lucide-react";
import { useSpeech } from "@/components/useSpeech";
import { actions, useProgress } from "@/lib/progress";
import GrammarQuiz from "./GrammarQuiz";
import { SKILL_LABEL, examGuides, nclcTable, practice, roadmap, speakingTasks, writingTasks } from "@/lib/library/exams";
import type { Level } from "@/lib/library";

type Tab = "guide" | "nclc" | "roadmap" | "practice" | "writing" | "speaking";
const TABS: { id: Tab; label: string }[] = [
  { id: "guide", label: "Exams" },
  { id: "nclc", label: "NCLC scores" },
  { id: "roadmap", label: "0 → C1 roadmap" },
  { id: "practice", label: "Read & Listen" },
  { id: "writing", label: "Writing" },
  { id: "speaking", label: "Speaking" },
];

const Chip = ({ children }: { children: React.ReactNode }) => (
  <span className="rounded-full bg-fg px-2.5 py-1 text-xs font-semibold text-bg">{children}</span>
);

export default function ExamsView({ level }: { level: Level | "ALL" }) {
  const [tab, setTab] = useState<Tab>("guide");
  const [open, setOpen] = useState<string | null>(null);
  const [reveal, setReveal] = useState<Record<string, boolean>>({});
  const { speak } = useSpeech();
  const progress = useProgress();
  const hi = progress.showHindi;
  const ok = (l: Level) => level === "ALL" || l === level;
  const toggle = (id: string) => setOpen(open === id ? null : id);
  const flip = (id: string) => setReveal((r) => ({ ...r, [id]: !r[id] }));

  return (
    <div>
      <div className="-mx-4 overflow-x-auto px-4 sm:mx-0 sm:px-0" role="tablist" aria-label="Exam tools">
        <div className="flex min-w-max gap-1.5">
          {TABS.map((t) => (
            <button key={t.id} role="tab" aria-selected={tab === t.id} onClick={() => setTab(t.id)}
              className={`h-9 rounded-full px-4 text-xs font-medium transition ${tab === t.id ? "bg-fg text-bg" : "border border-line text-muted hover:text-fg"}`}>
              {t.label}
            </button>
          ))}
        </div>
      </div>

      <div className="mt-6 space-y-4">
        {tab === "guide" && examGuides.map((g) => (
          <section key={g.id} className="rounded-2xl border border-line bg-surface p-5 sm:p-6">
            <h3 className="font-display text-2xl font-semibold">{g.name}</h3>
            <p className="mt-1 text-sm text-muted">{g.tagline}</p>
            {hi && <p className="text-sm text-accent">{g.tagline_hi}</p>}
            <p className="mt-3 text-xs text-muted">Levels: {g.levels} · Used for: {g.usedFor.join(", ")}</p>
            <ul className="mt-4 grid gap-3 sm:grid-cols-2">
              {g.parts.map((p) => (
                <li key={p.skill} className="rounded-xl bg-raised p-4">
                  <p className="text-sm font-semibold">{SKILL_LABEL[p.skill].fr} <span className="font-normal text-muted">· {SKILL_LABEL[p.skill].en} · {p.duration}</span></p>
                  <p className="mt-1 text-sm text-muted">{p.detail}</p>
                  {hi && <p className="mt-1 text-sm text-accent">{p.detail_hi}</p>}
                </li>
              ))}
            </ul>
            <p className="mt-4 text-sm"><span className="font-semibold">Scoring: </span>{g.scoring}</p>
            <ul className="mt-3 list-disc space-y-1.5 pl-5 text-sm text-muted">
              {g.tips.map((t, i) => <li key={t}>{t}{hi && <span className="block text-accent">{g.tips_hi[i]}</span>}</li>)}
            </ul>
          </section>
        ))}

        {tab === "nclc" && (
          <section className="rounded-2xl border border-line bg-surface p-4 sm:p-6">
            <p className="text-sm text-muted">NCLC (Niveaux de compétence linguistique canadiens) = CLB. The target for Express Entry French bonus points is NCLC 7 in all four skills. Higher NCLC means more points, so keep going to 9-10.</p>
            {hi && <p className="mt-1 text-sm text-accent">NCLC कनाडा का भाषा-स्तर है। Express Entry में चारों कौशलों में NCLC 7 ज़रूरी लक्ष्य है; 9-10 पर और अधिक अंक मिलते हैं।</p>}
            <div className="mt-4 overflow-x-auto rounded-xl border border-line">
              <table className="w-full min-w-[40rem] text-left text-xs sm:text-sm">
                <thead className="bg-raised text-xs uppercase tracking-wide text-muted">
                  <tr><th className="px-3 py-2">NCLC</th><th className="px-3 py-2">CEFR</th><th className="px-3 py-2">TEF CO</th><th className="px-3 py-2">TEF CE</th><th className="px-3 py-2">TEF EE/EO</th><th className="px-3 py-2">TCF CO</th><th className="px-3 py-2">TCF CE</th><th className="px-3 py-2">TCF EE/EO</th></tr>
                </thead>
                <tbody>
                  {nclcTable.map((r) => (
                    <tr key={r.nclc} className={`border-t border-line ${r.nclc === 7 ? "bg-accent/10 font-semibold" : ""}`}>
                      <td className="px-3 py-2">{r.nclc}</td><td className="px-3 py-2">{r.cefr}</td><td className="px-3 py-2">{r.tefCO}</td><td className="px-3 py-2">{r.tefCE}</td><td className="px-3 py-2">{r.tefEE}</td><td className="px-3 py-2">{r.tcfCO}</td><td className="px-3 py-2">{r.tcfCE}</td><td className="px-3 py-2">{r.tcfEE}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
            <ul className="mt-4 space-y-1 text-sm text-muted">
              {nclcTable.map((r) => <li key={r.nclc}><span className="font-semibold text-fg">NCLC {r.nclc}:</span> {r.note}{hi && <span className="text-accent"> · {r.note_hi}</span>}</li>)}
            </ul>
            <p className="mt-4 text-xs text-muted">Bands are summarised for study planning. Always confirm current values on the official IRCC and test-centre websites before you book.</p>
          </section>
        )}

        {tab === "roadmap" && roadmap.map((r) => (
          <section key={r.stage} className={`rounded-2xl border bg-surface p-5 sm:p-6 ${ok(r.level) ? "border-line" : "border-line opacity-50"}`}>
            <div className="flex flex-wrap items-center gap-2"><Chip>Stage {r.stage}</Chip><Chip>{r.level}</Chip><span className="text-xs text-muted">{r.nclc} · {r.hours}</span></div>
            <h3 className="mt-3 flex items-center gap-2 font-display text-xl font-semibold"><Target className="h-5 w-5 text-accent" /> {r.title}</h3>
            {hi && <p className="text-sm text-accent">{r.title_hi}</p>}
            <ul className="mt-3 list-disc space-y-1 pl-5 text-sm text-muted">{r.goals.map((g, i) => <li key={g}>{g}{hi && <span className="block text-accent">{r.goals_hi[i]}</span>}</li>)}</ul>
            <p className="mt-3 text-sm font-semibold">Do now</p>
            <ul className="mt-1 list-disc space-y-1 pl-5 text-sm text-muted">{r.doNow.map((d) => <li key={d}>{d}</li>)}</ul>
          </section>
        ))}

        {tab === "practice" && practice.filter((p) => ok(p.level)).map((p) => {
          const doneId = `exam:${p.id}`;
          const done = progress.completedItems.includes(doneId);
          const isCO = p.skill === "CO";
          return (
            <section key={p.id} className="overflow-hidden rounded-2xl border border-line bg-surface">
              <button onClick={() => toggle(p.id)} aria-expanded={open === p.id} className="flex w-full items-center gap-3 p-4 text-left hover:bg-raised/60 sm:p-5">
                <Chip>{p.level}</Chip>
                <span className="min-w-0 flex-1"><span className="block font-semibold">{p.title}</span><span className="block text-sm text-muted">{isCO ? "Listening" : "Reading"} · {p.title_en} · {p.nclc}</span></span>
                {done && <CheckCircle2 className="h-5 w-5 text-success" />}
                {isCO ? <Headphones className="h-4 w-4 text-muted" /> : null}
                <ChevronDown className={`h-4 w-4 text-muted transition ${open === p.id ? "rotate-180" : ""}`} />
              </button>
              {open === p.id && (
                <div className="space-y-4 border-t border-line p-4 sm:p-6">
                  {isCO ? (
                    <div className="flex flex-wrap gap-2">
                      <button onClick={() => speak(p.fr, 0.9)} className="inline-flex h-10 items-center gap-2 rounded-full bg-accent px-5 text-sm font-medium text-accent-fg"><Volume2 className="h-4 w-4" /> Play audio</button>
                      <button onClick={() => speak(p.fr, 0.65)} className="inline-flex h-10 items-center gap-2 rounded-full border border-line px-5 text-sm">Slow</button>
                      <button onClick={() => flip(p.id)} className="h-10 rounded-full border border-line px-5 text-sm">{reveal[p.id] ? "Hide script" : "Show script"}</button>
                    </div>
                  ) : null}
                  {(!isCO || reveal[p.id]) && (
                    <div className="rounded-xl bg-raised p-4">
                      <p className="whitespace-pre-line font-display text-lg leading-relaxed">{p.fr}</p>
                      <p className="mt-3 whitespace-pre-line text-sm text-muted">{p.en}</p>
                      {hi && <p className="mt-2 whitespace-pre-line text-sm text-accent">{p.hi}</p>}
                    </div>
                  )}
                  <GrammarQuiz questions={p.questions} />
                  <button onClick={() => actions.completeItem(doneId, 20)} disabled={done} className="inline-flex h-10 items-center gap-2 rounded-full border border-line px-5 text-sm font-medium disabled:text-success">
                    <CheckCircle2 className="h-4 w-4" /> {done ? "Done" : "Mark done (+20 XP)"}
                  </button>
                </div>
              )}
            </section>
          );
        })}

        {tab === "writing" && writingTasks.filter((w) => ok(w.level)).map((w) => (
          <section key={w.id} className="overflow-hidden rounded-2xl border border-line bg-surface">
            <button onClick={() => toggle(w.id)} aria-expanded={open === w.id} className="flex w-full items-center gap-3 p-4 text-left hover:bg-raised/60 sm:p-5">
              <Chip>{w.level}</Chip>
              <span className="min-w-0 flex-1"><span className="block font-semibold">{w.title}</span><span className="block text-sm text-muted">{w.exam} · {w.nclc} · min. {w.minWords} words</span></span>
              <PenLine className="h-4 w-4 text-muted" />
              <ChevronDown className={`h-4 w-4 text-muted transition ${open === w.id ? "rotate-180" : ""}`} />
            </button>
            {open === w.id && (
              <div className="space-y-4 border-t border-line p-4 sm:p-6">
                <p className="font-medium leading-relaxed">{w.prompt}</p>
                {hi && <p className="text-sm text-accent">{w.prompt_hi}</p>}
                <textarea aria-label="Your answer" placeholder="Écrivez votre réponse ici…" rows={7} className="w-full rounded-xl border border-line bg-bg p-3 text-sm outline-none focus:border-accent" />
                <ul className="list-disc space-y-1 pl-5 text-sm text-muted">{w.checklist.map((c) => <li key={c}>{c}</li>)}</ul>
                <button onClick={() => flip(w.id)} className="h-10 rounded-full border border-line px-5 text-sm">{reveal[w.id] ? "Hide model answer" : "Show model answer"}</button>
                {reveal[w.id] && (
                  <div className="rounded-xl bg-raised p-4">
                    <p className="whitespace-pre-line font-display leading-relaxed">{w.model}</p>
                    <p className="mt-3 text-sm text-muted">{w.model_en}</p>
                  </div>
                )}
              </div>
            )}
          </section>
        ))}

        {tab === "speaking" && speakingTasks.filter((s) => ok(s.level)).map((s) => (
          <section key={s.id} className="overflow-hidden rounded-2xl border border-line bg-surface">
            <button onClick={() => toggle(s.id)} aria-expanded={open === s.id} className="flex w-full items-center gap-3 p-4 text-left hover:bg-raised/60 sm:p-5">
              <Chip>{s.level}</Chip>
              <span className="min-w-0 flex-1"><span className="block font-semibold">{s.title}</span><span className="block text-sm text-muted">{s.exam} · {s.nclc} · ~{s.minutes} min</span></span>
              <Mic className="h-4 w-4 text-muted" />
              <ChevronDown className={`h-4 w-4 text-muted transition ${open === s.id ? "rotate-180" : ""}`} />
            </button>
            {open === s.id && (
              <div className="space-y-4 border-t border-line p-4 sm:p-6">
                <p className="font-medium leading-relaxed">{s.prompt}</p>
                {hi && <p className="text-sm text-accent">{s.prompt_hi}</p>}
                <p className="text-sm font-semibold">Useful phrases · काम के वाक्य</p>
                <ul className="space-y-2">
                  {s.phrases.map((p) => (
                    <li key={p.fr}>
                      <button onClick={() => speak(p.fr)} className="flex w-full items-center justify-between gap-3 rounded-xl bg-raised px-4 py-3 text-left">
                        <span><span className="block font-display text-lg">{p.fr}</span>{hi && <span className="block text-sm text-accent">{p.hi}</span>}</span>
                        <Volume2 className="h-4 w-4 shrink-0 text-muted" />
                      </button>
                    </li>
                  ))}
                </ul>
                <p className="text-sm text-muted">Tip: record yourself on your phone, listen back, and repeat until you can speak for the full {s.minutes} minutes without stopping.</p>
              </div>
            )}
          </section>
        ))}
      </div>
    </div>
  );
}
