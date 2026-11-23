"use client";

import { useCallback, useEffect, useState } from "react";
import { Archive, Check, Send, Trash2 } from "lucide-react";
import { api, fmtDate, type Mail } from "@/lib/api";
import { Badge, btn, Card, Chips, Empty, ErrorNote, inputCls, Spinner } from "./ui";

const VIEWS = ["inbox", "outbox", "compose"] as const;
type View = (typeof VIEWS)[number];

function Inbox({ onChanged }: { onChanged: () => void }) {
  const [items, setItems] = useState<Mail[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [open, setOpen] = useState<number | null>(null);
  const [drafts, setDrafts] = useState<Record<number, string>>({});
  const [showArchived, setShowArchived] = useState(false);

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const j = await api<{ data: Mail[] }>("emails?direction=in");
      setItems(j.data);
    } catch (e) {
      setError((e as Error).message);
    } finally {
      setLoading(false);
    }
  }, []);
  useEffect(() => {
    load();
  }, [load]);

  const setStatus = async (m: Mail, status: string) => {
    try {
      const j = await api<{ data: Mail }>(`emails/${m.id}`, { method: "PATCH", body: { status } });
      setItems((p) => p.map((x) => (x.id === m.id ? j.data : x)));
      onChanged();
    } catch (e) {
      setError((e as Error).message);
    }
  };

  const reply = async (m: Mail) => {
    const text = (drafts[m.id] ?? m.hermes_draft ?? "").trim();
    if (!text) return setError("Write a reply first.");
    try {
      await api("emails", {
        method: "POST",
        body: { direction: "out", to_addr: m.from_addr, subject: m.subject.toLowerCase().startsWith("re:") ? m.subject : `Re: ${m.subject}`, body: text, status: "approved", reply_to_id: m.id },
      });
      await setStatus(m, "read");
      setOpen(null);
      setError(null);
      alert("Reply queued. Hermes sends approved mail the next time it runs (hermes mail send).");
    } catch (e) {
      setError((e as Error).message);
    }
  };

  const shown = items.filter((m) => showArchived || m.status !== "archived");
  if (loading) return <Spinner />;
  return (
    <>
      <label className="mt-4 flex items-center gap-2 text-xs text-muted">
        <input type="checkbox" checked={showArchived} onChange={(e) => setShowArchived(e.target.checked)} /> Show archived
      </label>
      <ErrorNote error={error} />
      {shown.length === 0 ? <Empty>Inbox is empty. Run <code className="rounded bg-raised px-1.5 py-0.5 text-xs">hermes mail sync</code> to pull new mail.</Empty> : (
        <ul className="mt-4 space-y-3">
          {shown.map((m) => (
            <Card key={m.id}>
              <button className="flex w-full items-start justify-between gap-3 text-left" onClick={() => { setOpen(open === m.id ? null : m.id); if (m.status === "unread") setStatus(m, "read"); }}>
                <div className="min-w-0">
                  <p className={`truncate ${m.status === "unread" ? "font-semibold" : ""}`}>{m.subject || "(no subject)"}</p>
                  <p className="truncate text-sm text-muted">{m.from_addr} · {fmtDate(m.received_at ?? m.created_at)}</p>
                </div>
                <div className="flex shrink-0 items-center gap-2">
                  {m.hermes_draft && m.status !== "replied" && <span className="rounded-full bg-accent/15 px-2 py-0.5 text-[11px] font-medium text-accent">Hermes draft</span>}
                  <Badge>{m.status}</Badge>
                </div>
              </button>
              {open === m.id && (
                <div className="mt-4 space-y-3">
                  <pre className="max-h-72 overflow-auto whitespace-pre-wrap rounded-xl bg-raised p-4 font-sans text-sm leading-relaxed">{m.body}</pre>
                  <textarea
                    rows={6}
                    placeholder="Write your reply…"
                    value={drafts[m.id] ?? m.hermes_draft ?? ""}
                    onChange={(e) => setDrafts((d) => ({ ...d, [m.id]: e.target.value }))}
                    className={`${inputCls} resize-y`}
                  />
                  <div className="flex flex-wrap gap-2">
                    <button className={btn.primary} onClick={() => reply(m)}><Send className="h-4 w-4" /> Approve &amp; queue reply</button>
                    <button className={btn.ghost} onClick={() => setStatus(m, "archived")}><Archive className="h-4 w-4" /> Archive</button>
                  </div>
                </div>
              )}
            </Card>
          ))}
        </ul>
      )}
    </>
  );
}

function Outbox({ onChanged }: { onChanged: () => void }) {
  const [filter, setFilter] = useState<"draft" | "approved" | "sent" | "failed" | "all">("approved");
  const [items, setItems] = useState<Mail[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const j = await api<{ data: Mail[] }>(`emails?direction=out${filter === "all" ? "" : `&status=${filter}`}`);
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
      const j = await api<{ data: Mail }>(`emails/${id}`, { method: "PATCH", body });
      setItems((p) => p.map((x) => (x.id === id ? j.data : x)));
      onChanged();
    } catch (e) {
      setError((e as Error).message);
    }
  };
  const remove = async (id: number) => {
    if (!window.confirm("Delete this email?")) return;
    try {
      await api(`emails/${id}`, { method: "DELETE" });
      setItems((p) => p.filter((x) => x.id !== id));
      onChanged();
    } catch (e) {
      setError((e as Error).message);
    }
  };

  return (
    <>
      <div className="mt-4"><Chips value={filter} options={["approved", "draft", "sent", "failed", "all"] as const} onChange={setFilter} /></div>
      <ErrorNote error={error} />
      {loading ? <Spinner /> : items.length === 0 ? <Empty>Nothing here.</Empty> : (
        <ul className="mt-4 space-y-3">
          {items.map((m) => {
            const editable = m.status === "draft" || m.status === "approved" || m.status === "failed";
            return (
              <Card key={m.id}>
                <div className="flex items-start justify-between gap-3">
                  <div className="min-w-0">
                    <p className="truncate font-medium">{m.subject}</p>
                    <p className="truncate text-sm text-muted">To {m.to_addr} · {fmtDate(m.sent_at ?? m.created_at)} · by {m.created_by}</p>
                  </div>
                  <Badge>{m.status}</Badge>
                </div>
                {editable ? (
                  <textarea defaultValue={m.body} rows={5} onBlur={(e) => e.target.value !== m.body && patch(m.id, { body: e.target.value })} className={`${inputCls} mt-3 resize-y`} />
                ) : (
                  <pre className="mt-3 max-h-48 overflow-auto whitespace-pre-wrap rounded-xl bg-raised p-3 font-sans text-sm">{m.body}</pre>
                )}
                <div className="mt-3 flex flex-wrap gap-2">
                  {(m.status === "draft" || m.status === "failed") && <button className={btn.primary} onClick={() => patch(m.id, { status: "approved" })}><Check className="h-4 w-4" /> Approve to send</button>}
                  {m.status === "approved" && <button className={btn.ghost} onClick={() => patch(m.id, { status: "draft" })}>Back to draft</button>}
                  {m.status !== "sent" && <button className={btn.danger} onClick={() => remove(m.id)} aria-label="Delete"><Trash2 className="h-4 w-4" /></button>}
                </div>
              </Card>
            );
          })}
        </ul>
      )}
    </>
  );
}

function Compose({ onChanged, onDone }: { onChanged: () => void; onDone: () => void }) {
  const [f, setF] = useState({ to_addr: "", subject: "", body: "" });
  const [error, setError] = useState<string | null>(null);
  const submit = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      await api("emails", { method: "POST", body: { ...f, direction: "out", status: "approved" } });
      onChanged();
      onDone();
    } catch (err) {
      setError((err as Error).message);
    }
  };
  return (
    <form onSubmit={submit} className="mt-4 space-y-3 panel rounded-2xl p-5">
      <input required type="email" placeholder="To" value={f.to_addr} onChange={(e) => setF({ ...f, to_addr: e.target.value })} className={inputCls} />
      <input required placeholder="Subject" value={f.subject} onChange={(e) => setF({ ...f, subject: e.target.value })} className={inputCls} />
      <textarea required rows={8} placeholder="Message" value={f.body} onChange={(e) => setF({ ...f, body: e.target.value })} className={`${inputCls} resize-y`} />
      <ErrorNote error={error} />
      <button className={btn.primary}><Send className="h-4 w-4" /> Approve &amp; queue</button>
      <p className="text-xs text-muted">Mail is sent from your own mailbox by Hermes (<code>hermes mail send</code>), so it comes from your real address.</p>
    </form>
  );
}

export default function EmailTab({ onChanged }: { onChanged: () => void }) {
  const [view, setView] = useState<View>("inbox");
  return (
    <section className="mt-6">
      <Chips value={view} options={VIEWS} onChange={setView} />
      {view === "inbox" && <Inbox onChanged={onChanged} />}
      {view === "outbox" && <Outbox onChanged={onChanged} />}
      {view === "compose" && <Compose onChanged={onChanged} onDone={() => setView("outbox")} />}
    </section>
  );
}
