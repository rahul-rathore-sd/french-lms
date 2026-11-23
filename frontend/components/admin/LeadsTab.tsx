"use client";

import { useCallback, useEffect, useState } from "react";
import { Mail as MailIcon, MessageCircle, UserCheck } from "lucide-react";
import { api, fmtDate, type Lead } from "@/lib/api";
import { Badge, btn, Card, Chips, Empty, ErrorNote, inputCls, Spinner } from "./ui";

const FILTERS = ["new", "contacted", "qualified", "converted", "lost", "all"] as const;
type Filter = (typeof FILTERS)[number];

export default function LeadsTab({ onChanged }: { onChanged: () => void }) {
  const [filter, setFilter] = useState<Filter>("new");
  const [items, setItems] = useState<Lead[]>([]);
  const [counts, setCounts] = useState<Record<string, number>>({});
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [busy, setBusy] = useState<number | null>(null);

  const load = useCallback(async () => {
    setLoading(true);
    setError(null);
    try {
      const j = await api<{ data: Lead[]; counts: Record<string, number> }>(`leads${filter === "all" ? "" : `?status=${filter}`}`);
      setItems(j.data);
      setCounts({ ...j.counts, all: Object.values(j.counts).reduce((a, b) => a + b, 0) });
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
    setBusy(id);
    try {
      const j = await api<{ data: Lead }>(`leads/${id}`, { method: "PATCH", body });
      setItems((prev) => prev.map((l) => (l.id === id ? j.data : l)));
      onChanged();
    } catch (e) {
      setError((e as Error).message);
    } finally {
      setBusy(null);
    }
  };

  const convert = async (id: number) => {
    setBusy(id);
    try {
      await api(`leads/${id}/convert`, { method: "POST", body: {} });
      await load();
      onChanged();
    } catch (e) {
      setError((e as Error).message);
    } finally {
      setBusy(null);
    }
  };

  return (
    <section className="mt-6">
      <Chips value={filter} options={FILTERS} onChange={setFilter} counts={counts} />
      <ErrorNote error={error} />
      {loading ? <Spinner /> : items.length === 0 ? <Empty>No {filter === "all" ? "" : filter} leads.</Empty> : (
        <ul className="mt-5 space-y-3">
          {items.map((l) => (
            <Card key={l.id}>
              <div className="flex flex-wrap items-start justify-between gap-3">
                <div>
                  <p className="font-semibold">{l.full_name}</p>
                  <p className="text-sm text-muted">{l.email}{l.phone && ` · ${l.phone}`} · goal {l.target_cefr} · via {l.source}</p>
                </div>
                <Badge>{l.status}</Badge>
              </div>
              <p className="mt-3 whitespace-pre-wrap text-sm leading-relaxed text-muted">{l.message}</p>

              {l.hermes_reply && (
                <div className="mt-4 rounded-xl border border-accent/30 bg-accent/5 p-4">
                  <p className="text-xs font-medium text-accent">Hermes suggested reply</p>
                  <p className="mt-1 whitespace-pre-wrap text-sm">{l.hermes_reply}</p>
                  <div className="mt-3 flex flex-wrap gap-2">
                    <a className={btn.ghost} href={`mailto:${l.email}?subject=${encodeURIComponent("Your French learning enquiry")}&body=${encodeURIComponent(l.hermes_reply)}`}>
                      <MailIcon className="h-4 w-4" /> Open in mail app
                    </a>
                    {l.phone && (
                      <a className={btn.ghost} target="_blank" rel="noopener noreferrer" href={`https://wa.me/${l.phone.replace(/\D/g, "")}?text=${encodeURIComponent(l.hermes_reply)}`}>
                        <MessageCircle className="h-4 w-4" /> WhatsApp
                      </a>
                    )}
                  </div>
                </div>
              )}

              <textarea
                defaultValue={l.notes ?? ""}
                rows={2}
                maxLength={4000}
                placeholder="Private notes…"
                onBlur={(e) => e.target.value !== (l.notes ?? "") && patch(l.id, { notes: e.target.value })}
                className={`${inputCls} mt-4 resize-none`}
              />

              <div className="mt-4 flex flex-wrap items-center justify-between gap-3">
                <span className="text-xs text-muted">{fmtDate(l.created_at)}</span>
                <div className="flex flex-wrap gap-2">
                  <select
                    value={l.status}
                    disabled={busy === l.id}
                    onChange={(e) => patch(l.id, { status: e.target.value })}
                    className="h-9 rounded-full border border-line bg-bg px-3 text-sm"
                    aria-label="Lead status"
                  >
                    {["new", "contacted", "qualified", "converted", "lost"].map((s) => <option key={s}>{s}</option>)}
                  </select>
                  {l.status !== "converted" && (
                    <button disabled={busy === l.id} onClick={() => convert(l.id)} className={btn.primary}>
                      <UserCheck className="h-4 w-4" /> Make client
                    </button>
                  )}
                </div>
              </div>
            </Card>
          ))}
        </ul>
      )}
    </section>
  );
}
