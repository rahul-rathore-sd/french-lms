"use client";

import { useCallback, useEffect, useState } from "react";
import { Check, Inbox, Loader2, X } from "lucide-react";
import SiteHeader from "@/components/SiteHeader";
import { useLexicon } from "@/components/useLexicon";
import { initialCourses, AdmissionRequest } from "@/lib/data";

type Filter = "all" | "pending" | "approved" | "rejected";

const badge: Record<AdmissionRequest["status"], string> = {
  pending: "bg-gold/15 text-gold",
  approved: "bg-success/15 text-success",
  rejected: "bg-danger/15 text-danger",
};

export default function AdminPage() {
  const lexicon = useLexicon();
  const [tab, setTab] = useState<"applications" | "overview">("applications");
  const [filter, setFilter] = useState<Filter>("pending");
  const [items, setItems] = useState<AdmissionRequest[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [busyId, setBusyId] = useState<number | null>(null);

  const load = useCallback(async () => {
    setLoading(true);
    setError(null);
    try {
      const res = await fetch("/api/v1/admissions", { cache: "no-store" });
      const json = await res.json();
      if (!res.ok) throw new Error(json.error || "Could not load applications.");
      setItems(json.data);
    } catch (e) {
      setError(e instanceof Error ? e.message : "Could not load applications.");
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    load();
  }, [load]);

  const decide = async (id: number, status: "approved" | "rejected") => {
    let rejection_reason: string | undefined;
    if (status === "rejected") {
      const reason = window.prompt("Reason for declining (optional):", "");
      if (reason === null) return;
      rejection_reason = reason;
    }
    setBusyId(id);
    try {
      const res = await fetch("/api/v1/admissions", {
        method: "PATCH",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id, status, rejection_reason }),
      });
      const json = await res.json();
      if (!res.ok) throw new Error(json.error || "Update failed.");
      setItems((prev) => prev.map((r) => (r.id === id ? json.data : r)));
    } catch (e) {
      setError(e instanceof Error ? e.message : "Update failed.");
    } finally {
      setBusyId(null);
    }
  };

  const shown = items.filter((r) => filter === "all" || r.status === filter);
  const counts = {
    pending: items.filter((r) => r.status === "pending").length,
    approved: items.filter((r) => r.status === "approved").length,
    rejected: items.filter((r) => r.status === "rejected").length,
  };

  return (
    <div className="min-h-screen">
      <SiteHeader />
      <main className="mx-auto max-w-5xl px-4 py-8 sm:px-6">
        <h1 className="font-display text-4xl font-medium tracking-tight">Admin console</h1>
        <p className="mt-1 text-sm text-muted">Review applications and keep an eye on the content library.</p>

        <div className="mt-8 flex gap-1 border-b border-line" role="tablist">
          {(["applications", "overview"] as const).map((t) => (
            <button
              key={t}
              role="tab"
              aria-selected={tab === t}
              onClick={() => setTab(t)}
              className={`relative px-4 py-3 text-sm font-medium capitalize transition ${tab === t ? "text-fg" : "text-muted hover:text-fg"}`}
            >
              {t}
              {t === "applications" && counts.pending > 0 && (
                <span className="ml-2 rounded-full bg-accent px-1.5 py-0.5 text-[10px] font-semibold text-accent-fg">{counts.pending}</span>
              )}
              {tab === t && <span className="absolute inset-x-3 -bottom-px h-0.5 rounded bg-accent" />}
            </button>
          ))}
        </div>

        {tab === "applications" ? (
          <section className="mt-6">
            <div className="flex flex-wrap gap-1.5">
              {(["pending", "approved", "rejected", "all"] as Filter[]).map((f) => (
                <button
                  key={f}
                  onClick={() => setFilter(f)}
                  className={`rounded-full px-3.5 py-2 text-xs font-medium capitalize transition ${filter === f ? "bg-fg text-bg" : "border border-line text-muted hover:text-fg"}`}
                >
                  {f}
                  {f !== "all" && <span className="ml-1.5 opacity-60">{counts[f]}</span>}
                </button>
              ))}
            </div>

            {error && <p role="alert" className="mt-4 rounded-xl border border-danger/30 bg-danger/10 px-4 py-3 text-sm text-danger">{error}</p>}

            {loading ? (
              <p className="mt-12 flex items-center justify-center gap-2 text-sm text-muted"><Loader2 className="h-4 w-4 animate-spin" /> Loading…</p>
            ) : shown.length === 0 ? (
              <div className="mt-6 rounded-2xl border border-dashed border-line p-14 text-center text-sm text-muted">
                <Inbox className="mx-auto mb-3 h-8 w-8 opacity-60" />
                No {filter === "all" ? "" : filter} applications.
              </div>
            ) : (
              <ul className="mt-5 space-y-3">
                {shown.map((r) => (
                  <li key={r.id} className="rounded-2xl border border-line bg-surface p-5">
                    <div className="flex flex-wrap items-start justify-between gap-3">
                      <div>
                        <p className="font-semibold">{r.full_name}</p>
                        <p className="text-sm text-muted">{r.email} · goal {r.target_cefr} · {r.academic_background}</p>
                      </div>
                      <span className={`rounded-full px-2.5 py-1 text-xs font-medium capitalize ${badge[r.status]}`}>{r.status}</span>
                    </div>
                    <p className="mt-3 text-sm leading-relaxed text-muted">{r.motivation}</p>
                    {r.rejection_reason && <p className="mt-2 text-xs text-danger">Reason: {r.rejection_reason}</p>}
                    <div className="mt-4 flex items-center justify-between">
                      <span className="text-xs text-muted">{new Date(r.submitted_at).toLocaleDateString(undefined, { dateStyle: "medium" })}</span>
                      {r.status === "pending" && (
                        <div className="flex gap-2">
                          <button
                            disabled={busyId === r.id}
                            onClick={() => decide(r.id, "rejected")}
                            className="inline-flex h-9 items-center gap-1.5 rounded-full border border-line px-4 text-sm text-muted transition hover:border-danger hover:text-danger disabled:opacity-50"
                          >
                            <X className="h-4 w-4" /> Decline
                          </button>
                          <button
                            disabled={busyId === r.id}
                            onClick={() => decide(r.id, "approved")}
                            className="inline-flex h-9 items-center gap-1.5 rounded-full bg-accent px-4 text-sm font-medium text-accent-fg transition hover:brightness-110 disabled:opacity-50"
                          >
                            <Check className="h-4 w-4" /> Approve
                          </button>
                        </div>
                      )}
                    </div>
                  </li>
                ))}
              </ul>
            )}
          </section>
        ) : (
          <section className="mt-6 space-y-6">
            <div className="grid gap-3 sm:grid-cols-3">
              {[
                [initialCourses.length, "Courses"],
                [initialCourses.reduce((n, c) => n + c.lessons.length, 0), "Lessons"],
                [lexicon.length, "Lexicon words"],
              ].map(([v, l]) => (
                <div key={String(l)} className="rounded-2xl border border-line bg-surface p-5">
                  <p className="font-display text-3xl font-semibold">{v}</p>
                  <p className="text-sm text-muted">{l}</p>
                </div>
              ))}
            </div>

            <div className="rounded-2xl border border-line bg-surface p-6">
              <h2 className="font-semibold">Hermes ingestion API</h2>
              <p className="mt-1 text-sm text-muted">
                An agent can add or update words with a Bearer token. The token lives in the <code className="rounded bg-raised px-1.5 py-0.5 text-xs">HERMES_API_KEY</code> environment variable and is never shown here.
              </p>
              <pre className="mt-4 overflow-x-auto rounded-xl bg-raised p-4 text-xs leading-relaxed">
{`POST /api/v1/vocabulary
Authorization: Bearer <HERMES_API_KEY>
Content-Type: application/json

{ "french_word": "…", "meaning_en": "…", "meaning_hi": "…",
  "use_case": "…", "cefr_level": "B1" }`}
              </pre>
            </div>
          </section>
        )}
      </main>
    </div>
  );
}
