"use client";

import { useCallback, useEffect, useState } from "react";
import { Plus, Trash2 } from "lucide-react";
import { api, fmtDate, type Client } from "@/lib/api";
import { Badge, btn, Card, Chips, Empty, ErrorNote, inputCls, Spinner } from "./ui";

const FILTERS = ["active", "paused", "completed", "all"] as const;
const LEVELS = ["A1", "A2", "B1", "B2", "C1", "C2"];

export default function ClientsTab({ onChanged }: { onChanged: () => void }) {
  const [filter, setFilter] = useState<(typeof FILTERS)[number]>("active");
  const [items, setItems] = useState<Client[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [adding, setAdding] = useState(false);
  const [form, setForm] = useState({ full_name: "", email: "", phone: "", level: "A1", plan: "" });

  const load = useCallback(async () => {
    setLoading(true);
    setError(null);
    try {
      const j = await api<{ data: Client[] }>(`clients${filter === "all" ? "" : `?status=${filter}`}`);
      setItems(j.data);
    } catch (e) {
      setError((e as Error).message);
    } finally {
      setLoading(false);
    }
  }, [filter]);

  useEffect(() => {
    load();
  }, [load]);

  const patch = async (id: number, body: Record<string, unknown>) => {
    try {
      const j = await api<{ data: Client }>(`clients/${id}`, { method: "PATCH", body });
      setItems((prev) => prev.map((c) => (c.id === id ? j.data : c)));
      onChanged();
    } catch (e) {
      setError((e as Error).message);
    }
  };

  const remove = async (c: Client) => {
    if (!window.confirm(`Delete client ${c.full_name}? This cannot be undone.`)) return;
    try {
      await api(`clients/${c.id}`, { method: "DELETE" });
      setItems((prev) => prev.filter((x) => x.id !== c.id));
      onChanged();
    } catch (e) {
      setError((e as Error).message);
    }
  };

  const add = async (e: React.FormEvent) => {
    e.preventDefault();
    setError(null);
    try {
      await api("clients", { method: "POST", body: form });
      setForm({ full_name: "", email: "", phone: "", level: "A1", plan: "" });
      setAdding(false);
      await load();
      onChanged();
    } catch (err) {
      setError((err as Error).message);
    }
  };

  return (
    <section className="mt-6">
      <div className="flex flex-wrap items-center justify-between gap-3">
        <Chips value={filter} options={FILTERS} onChange={setFilter} />
        <button className={btn.primary} onClick={() => setAdding((v) => !v)}><Plus className="h-4 w-4" /> Add client</button>
      </div>

      {adding && (
        <form onSubmit={add} className="mt-4 grid gap-3 rounded-2xl border border-line bg-surface p-5 sm:grid-cols-2">
          <input required placeholder="Full name" value={form.full_name} onChange={(e) => setForm({ ...form, full_name: e.target.value })} className={inputCls} />
          <input required type="email" placeholder="Email" value={form.email} onChange={(e) => setForm({ ...form, email: e.target.value })} className={inputCls} />
          <input placeholder="Phone" value={form.phone} onChange={(e) => setForm({ ...form, phone: e.target.value })} className={inputCls} />
          <div className="flex gap-3">
            <select value={form.level} onChange={(e) => setForm({ ...form, level: e.target.value })} className={`${inputCls} w-24`}>{LEVELS.map((l) => <option key={l}>{l}</option>)}</select>
            <input placeholder="Plan (e.g. TEF batch)" value={form.plan} onChange={(e) => setForm({ ...form, plan: e.target.value })} className={inputCls} />
          </div>
          <button className={`${btn.primary} sm:col-span-2`}>Save client</button>
        </form>
      )}

      <ErrorNote error={error} />
      {loading ? <Spinner /> : items.length === 0 ? <Empty>No {filter === "all" ? "" : filter} clients yet. Convert a lead or add one.</Empty> : (
        <ul className="mt-5 space-y-3">
          {items.map((c) => (
            <Card key={c.id}>
              <div className="flex flex-wrap items-start justify-between gap-3">
                <div>
                  <p className="font-semibold">{c.full_name}</p>
                  <p className="text-sm text-muted">{c.email}{c.phone && ` · ${c.phone}`} · since {fmtDate(c.started_at).split(",")[0]}</p>
                </div>
                <Badge>{c.status}</Badge>
              </div>
              <div className="mt-4 flex flex-wrap gap-2">
                <select value={c.level} onChange={(e) => patch(c.id, { level: e.target.value })} className="h-9 rounded-full border border-line bg-bg px-3 text-sm" aria-label="Level">{LEVELS.map((l) => <option key={l}>{l}</option>)}</select>
                <select value={c.status} onChange={(e) => patch(c.id, { status: e.target.value })} className="h-9 rounded-full border border-line bg-bg px-3 text-sm" aria-label="Status">{["active", "paused", "completed"].map((s) => <option key={s}>{s}</option>)}</select>
                <input defaultValue={c.plan} placeholder="Plan" onBlur={(e) => e.target.value !== c.plan && patch(c.id, { plan: e.target.value })} className="h-9 min-w-40 flex-1 rounded-full border border-line bg-bg px-4 text-sm" />
                <a className={btn.ghost} href={`mailto:${c.email}`}>Email</a>
                <button className={btn.danger} onClick={() => remove(c)} aria-label={`Delete ${c.full_name}`}><Trash2 className="h-4 w-4" /></button>
              </div>
              <textarea defaultValue={c.notes ?? ""} rows={2} placeholder="Notes…" onBlur={(e) => e.target.value !== (c.notes ?? "") && patch(c.id, { notes: e.target.value })} className={`${inputCls} mt-3 resize-none`} />
            </Card>
          ))}
        </ul>
      )}
    </section>
  );
}
