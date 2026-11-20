"use client";

import { Loader2 } from "lucide-react";
import type { ReactNode } from "react";

export const inputCls =
  "w-full rounded-xl border border-line bg-bg px-3.5 py-2.5 text-sm outline-none transition placeholder:text-muted focus:border-accent";

export const btn = {
  primary: "inline-flex h-9 items-center justify-center gap-1.5 rounded-full bg-accent px-4 text-sm font-medium text-accent-fg transition hover:brightness-110 disabled:opacity-50",
  ghost: "inline-flex h-9 items-center justify-center gap-1.5 rounded-full border border-line px-4 text-sm text-muted transition hover:text-fg disabled:opacity-50",
  danger: "inline-flex h-9 items-center justify-center gap-1.5 rounded-full border border-line px-4 text-sm text-muted transition hover:border-danger hover:text-danger disabled:opacity-50",
};

const tones: Record<string, string> = {
  new: "bg-accent/15 text-accent", unread: "bg-accent/15 text-accent", draft: "bg-gold/15 text-gold",
  contacted: "bg-gold/15 text-gold", approved: "bg-gold/15 text-gold", scheduled: "bg-gold/15 text-gold", qualified: "bg-gold/15 text-gold",
  converted: "bg-success/15 text-success", active: "bg-success/15 text-success", published: "bg-success/15 text-success",
  posted: "bg-success/15 text-success", sent: "bg-success/15 text-success", replied: "bg-success/15 text-success",
  lost: "bg-danger/15 text-danger", failed: "bg-danger/15 text-danger",
};

export function Badge({ children }: { children: string }) {
  return <span className={`rounded-full px-2.5 py-1 text-xs font-medium capitalize ${tones[children] ?? "bg-raised text-muted"}`}>{children}</span>;
}

export function Spinner() {
  return <p className="mt-12 flex items-center justify-center gap-2 text-sm text-muted"><Loader2 className="h-4 w-4 animate-spin" /> Loading…</p>;
}

export function ErrorNote({ error }: { error: string | null }) {
  return error ? <p role="alert" className="mt-4 rounded-xl border border-danger/30 bg-danger/10 px-4 py-3 text-sm text-danger">{error}</p> : null;
}

export function Empty({ children }: { children: ReactNode }) {
  return <div className="mt-6 rounded-2xl border border-dashed border-line p-12 text-center text-sm text-muted">{children}</div>;
}

export function Chips<T extends string>({ value, options, onChange, counts }: { value: T; options: readonly T[]; onChange: (v: T) => void; counts?: Record<string, number> }) {
  return (
    <div className="flex flex-wrap gap-1.5">
      {options.map((o) => (
        <button key={o} onClick={() => onChange(o)} className={`rounded-full px-3.5 py-2 text-xs font-medium capitalize transition ${value === o ? "bg-fg text-bg" : "border border-line text-muted hover:text-fg"}`}>
          {o}
          {counts && counts[o] !== undefined && <span className="ml-1.5 opacity-60">{counts[o]}</span>}
        </button>
      ))}
    </div>
  );
}

export function Card({ children }: { children: ReactNode }) {
  return <li className="rounded-2xl border border-line bg-surface p-5">{children}</li>;
}
