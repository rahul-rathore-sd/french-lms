"use client";

import { useState } from "react";
import Link from "next/link";
import { CheckCircle2, Loader2, Clock } from "lucide-react";
import AuthShell from "@/components/AuthShell";

const LEVELS = [
  ["A1", "Beginner"],
  ["A2", "Elementary"],
  ["B1", "Intermediate"],
  ["B2", "Upper intermediate"],
  ["C1", "Advanced"],
  ["C2", "Mastery"],
] as const;

const field =
  "w-full rounded-xl border border-line bg-bg px-4 py-3 text-sm outline-none transition placeholder:text-muted focus:border-accent";

export default function ApplyPage() {
  const [form, setForm] = useState({
    full_name: "",
    email: "",
    target_cefr: "B1",
    academic_background: "",
    motivation: "",
  });
  const [busy, setBusy] = useState(false);
  const [done, setDone] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const set = (k: keyof typeof form) => (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) =>
    setForm((f) => ({ ...f, [k]: e.target.value }));

  const submit = async (e: React.FormEvent) => {
    e.preventDefault();
    setBusy(true);
    setError(null);
    try {
      const res = await fetch("/api/v1/admissions", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(form),
      });
      const data = await res.json();
      if (!res.ok) throw new Error(data.error || "Something went wrong. Please try again.");
      setDone(true);
    } catch (err) {
      setError(err instanceof Error ? err.message : "Network error. Please try again.");
    } finally {
      setBusy(false);
    }
  };

  if (done) {
    return (
      <AuthShell>
        <div className="w-full animate-rise rounded-2xl border border-line bg-surface p-8 text-center shadow-soft">
          <span className="mx-auto grid h-14 w-14 place-items-center rounded-full bg-success/15 text-success">
            <CheckCircle2 className="h-7 w-7" />
          </span>
          <h1 className="mt-5 font-display text-2xl font-semibold">Application received</h1>
          <p className="mt-2 text-sm text-muted">
            Thank you, {form.full_name.split(" ")[0]}. We will review your request and email you at{" "}
            <span className="text-fg">{form.email}</span> once it has been approved.
          </p>
          <p className="mt-5 inline-flex items-center gap-1.5 rounded-full bg-raised px-3 py-1.5 text-xs text-muted">
            <Clock className="h-3.5 w-3.5" /> Usually within 48 hours
          </p>
          <div className="mt-6">
            <Link href="/" className="text-sm font-medium text-accent hover:underline">Back to home</Link>
          </div>
        </div>
      </AuthShell>
    );
  }

  return (
    <AuthShell>
      <form onSubmit={submit} className="w-full animate-rise rounded-2xl border border-line bg-surface p-6 shadow-soft sm:p-8">
        <h1 className="font-display text-3xl font-semibold tracking-tight">Request an invitation</h1>
        <p className="mt-2 text-sm text-muted">Tell us a little about you. Approved applicants receive access to every course.</p>

        <div className="mt-6 space-y-4">
          <label className="block">
            <span className="mb-1.5 block text-sm font-medium">Full name</span>
            <input required maxLength={120} value={form.full_name} onChange={set("full_name")} className={field} placeholder="Marie Curie" autoComplete="name" />
          </label>
          <label className="block">
            <span className="mb-1.5 block text-sm font-medium">Email</span>
            <input required type="email" maxLength={200} value={form.email} onChange={set("email")} className={field} placeholder="you@example.com" autoComplete="email" />
          </label>

          <fieldset>
            <legend className="mb-1.5 text-sm font-medium">Your goal level</legend>
            <div className="grid grid-cols-3 gap-2">
              {LEVELS.map(([code, label]) => (
                <label
                  key={code}
                  className={`cursor-pointer rounded-xl border px-3 py-2.5 text-center transition ${
                    form.target_cefr === code ? "border-accent bg-accent/10" : "border-line hover:border-accent/40"
                  }`}
                >
                  <input type="radio" name="level" value={code} checked={form.target_cefr === code} onChange={set("target_cefr")} className="sr-only" />
                  <span className="block text-sm font-semibold">{code}</span>
                  <span className="block text-[11px] text-muted">{label}</span>
                </label>
              ))}
            </div>
          </fieldset>

          <label className="block">
            <span className="mb-1.5 block text-sm font-medium">Background <span className="font-normal text-muted">(optional)</span></span>
            <input maxLength={300} value={form.academic_background} onChange={set("academic_background")} className={field} placeholder="Student, engineer, traveller…" />
          </label>
          <label className="block">
            <span className="mb-1.5 block text-sm font-medium">Why do you want to learn French?</span>
            <textarea required rows={4} maxLength={2000} value={form.motivation} onChange={set("motivation")} className={`${field} resize-none`} placeholder="Study abroad, work, culture, family…" />
          </label>
        </div>

        {error && (
          <p role="alert" className="mt-4 rounded-xl border border-danger/30 bg-danger/10 px-4 py-3 text-sm text-danger">{error}</p>
        )}

        <button
          type="submit"
          disabled={busy}
          className="mt-6 flex h-12 w-full items-center justify-center gap-2 rounded-full bg-accent text-sm font-medium text-accent-fg shadow-glow transition hover:brightness-110 disabled:opacity-60"
        >
          {busy && <Loader2 className="h-4 w-4 animate-spin" />}
          {busy ? "Sending…" : "Send application"}
        </button>
        <p className="mt-4 text-center text-sm text-muted">
          Already approved? <Link href="/sign-up" className="font-medium text-accent hover:underline">Create your account</Link>
        </p>
      </form>
    </AuthShell>
  );
}
