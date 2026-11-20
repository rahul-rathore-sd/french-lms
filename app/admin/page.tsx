"use client";

import { useCallback, useEffect, useState } from "react";
import { Loader2, LogOut } from "lucide-react";
import SiteHeader from "@/components/SiteHeader";
import { api } from "@/lib/api";
import { btn, inputCls } from "@/components/admin/ui";
import OverviewTab, { type Stats } from "@/components/admin/OverviewTab";
import LeadsTab from "@/components/admin/LeadsTab";
import ClientsTab from "@/components/admin/ClientsTab";
import EmailTab from "@/components/admin/EmailTab";
import BlogTab from "@/components/admin/BlogTab";
import SocialTab from "@/components/admin/SocialTab";
import BooksTab from "@/components/admin/BooksTab";

const TABS = [
  ["overview", "Overview", null],
  ["books", "Books", null],
  ["leads", "Leads", "leads_new"],
  ["clients", "Clients", null],
  ["email", "Email", "emails_unread"],
  ["blog", "Blog", null],
  ["social", "Social", "social_queue"],
] as const;
type Tab = (typeof TABS)[number][0];

function Login({ onDone }: { onDone: () => void }) {
  const [password, setPassword] = useState("");
  const [error, setError] = useState<string | null>(null);
  const [busy, setBusy] = useState(false);
  const submit = async (e: React.FormEvent) => {
    e.preventDefault();
    setBusy(true);
    setError(null);
    try {
      await api("admin/login", { method: "POST", body: { password } });
      onDone();
    } catch (err) {
      setError((err as Error).message);
    } finally {
      setBusy(false);
    }
  };
  return (
    <form onSubmit={submit} className="mx-auto mt-24 w-full max-w-sm rounded-2xl border border-line bg-surface p-7 shadow-soft">
      <h1 className="font-display text-2xl font-semibold">Admin sign in</h1>
      <p className="mt-1 text-sm text-muted">Manage leads, clients, email, blog and social media.</p>
      <input type="password" required autoFocus autoComplete="current-password" value={password} onChange={(e) => setPassword(e.target.value)} placeholder="Password" className={`${inputCls} mt-5`} />
      {error && <p role="alert" className="mt-3 text-sm text-danger">{error}</p>}
      <button disabled={busy} className={`${btn.primary} mt-4 w-full`}>{busy && <Loader2 className="h-4 w-4 animate-spin" />} Sign in</button>
    </form>
  );
}

export default function AdminPage() {
  const [auth, setAuth] = useState<"loading" | "out" | "in">("loading");
  const [tab, setTab] = useState<Tab>("overview");
  const [stats, setStats] = useState<Stats | null>(null);
  const [tick, setTick] = useState(0);

  const refresh = useCallback(() => {
    api<{ data: Stats }>("stats").then((j) => setStats(j.data)).catch(() => {});
    setTick((t) => t + 1);
  }, []);

  useEffect(() => {
    document.title = "Admin · Académie de Français";
    api<{ authenticated: boolean }>("admin/me")
      .then((j) => setAuth(j.authenticated ? "in" : "out"))
      .catch(() => setAuth("out"));
  }, []);
  useEffect(() => {
    if (auth === "in") refresh();
  }, [auth, refresh]);

  const logout = async () => {
    await api("admin/logout", { method: "POST", body: {} }).catch(() => {});
    setAuth("out");
  };

  return (
    <div className="min-h-screen">
      <meta name="robots" content="noindex,nofollow" />
      <SiteHeader />
      <main className="mx-auto max-w-5xl px-4 py-8 sm:px-6">
        {auth === "loading" ? (
          <p className="mt-24 flex items-center justify-center gap-2 text-sm text-muted"><Loader2 className="h-4 w-4 animate-spin" /> Loading…</p>
        ) : auth === "out" ? (
          <Login onDone={() => setAuth("in")} />
        ) : (
          <>
            <div className="flex flex-wrap items-center justify-between gap-3">
              <div>
                <h1 className="font-display text-4xl font-medium tracking-tight">Dashboard</h1>
                <p className="mt-1 text-sm text-muted">Everything Hermes and you manage for the website.</p>
              </div>
              <button className={btn.ghost} onClick={logout}><LogOut className="h-4 w-4" /> Sign out</button>
            </div>

            <div className="mt-8 flex gap-1 overflow-x-auto border-b border-line" role="tablist">
              {TABS.map(([id, label, badgeKey]) => (
                <button key={id} role="tab" aria-selected={tab === id} onClick={() => setTab(id)} className={`relative shrink-0 px-4 py-3 text-sm font-medium transition ${tab === id ? "text-fg" : "text-muted hover:text-fg"}`}>
                  {label}
                  {badgeKey && stats && stats[badgeKey] > 0 && <span className="ml-2 rounded-full bg-accent px-1.5 py-0.5 text-[10px] font-semibold text-accent-fg">{stats[badgeKey]}</span>}
                  {tab === id && <span className="absolute inset-x-3 -bottom-px h-0.5 rounded bg-accent" />}
                </button>
              ))}
            </div>

            {tab === "overview" && <OverviewTab stats={stats} refreshKey={tick} />}
            {tab === "books" && <BooksTab onChanged={refresh} />}
            {tab === "leads" && <LeadsTab onChanged={refresh} />}
            {tab === "clients" && <ClientsTab onChanged={refresh} />}
            {tab === "email" && <EmailTab onChanged={refresh} />}
            {tab === "blog" && <BlogTab onChanged={refresh} />}
            {tab === "social" && <SocialTab onChanged={refresh} />}
          </>
        )}
      </main>
    </div>
  );
}
