"use client";

import { useCallback, useEffect, useState } from "react";
import { Check, Copy, ExternalLink, Trash2 } from "lucide-react";
import { api, fmtDate, type Social } from "@/lib/api";
import { Badge, btn, Card, Chips, Empty, ErrorNote, inputCls, Spinner } from "./ui";

const PLATFORMS = ["instagram", "facebook", "linkedin", "x", "youtube", "whatsapp", "other"];
const LIMITS: Record<string, number> = { x: 280, instagram: 2200, facebook: 5000, linkedin: 3000 };

/** One-click share links (no API keys needed). Instagram has no web share, so it is copy-only. */
function shareUrl(p: Social): string | null {
  const text = encodeURIComponent(p.content);
  const link = encodeURIComponent(p.link || "");
  switch (p.platform) {
    case "x": return `https://twitter.com/intent/tweet?text=${text}${link ? `&url=${link}` : ""}`;
    case "facebook": return p.link ? `https://www.facebook.com/sharer/sharer.php?u=${link}&quote=${text}` : null;
    case "linkedin": return p.link ? `https://www.linkedin.com/sharing/share-offsite/?url=${link}` : null;
    case "whatsapp": return `https://wa.me/?text=${text}${p.link ? encodeURIComponent(" " + p.link) : ""}`;
    default: return null;
  }
}

export default function SocialTab({ onChanged }: { onChanged: () => void }) {
  const [filter, setFilter] = useState<"queue" | "posted" | "all">("queue");
  const [items, setItems] = useState<Social[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [f, setF] = useState({ platform: "linkedin", content: "", link: "" });
  const [copied, setCopied] = useState<number | null>(null);

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const j = await api<{ data: Social[] }>("social");
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

  const patch = async (id: number, body: Record<string, unknown>) => {
    try {
      const j = await api<{ data: Social }>(`social/${id}`, { method: "PATCH", body });
      setItems((p) => p.map((x) => (x.id === id ? j.data : x)));
      onChanged();
    } catch (e) {
      setError((e as Error).message);
    }
  };

  const create = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      await api("social", { method: "POST", body: { ...f, status: "draft" } });
      setF({ ...f, content: "", link: "" });
      await load();
      onChanged();
    } catch (err) {
      setError((err as Error).message);
    }
  };

  const remove = async (id: number) => {
    if (!window.confirm("Delete this post?")) return;
    try {
      await api(`social/${id}`, { method: "DELETE" });
      setItems((p) => p.filter((x) => x.id !== id));
      onChanged();
    } catch (e) {
      setError((e as Error).message);
    }
  };

  const copy = async (p: Social) => {
    try {
      await navigator.clipboard.writeText(p.link ? `${p.content}\n\n${p.link}` : p.content);
      setCopied(p.id);
      setTimeout(() => setCopied(null), 1500);
    } catch {
      setError("Could not copy. Select the text manually.");
    }
  };

  const markPosted = (p: Social) => {
    const url = window.prompt("Link to the live post (optional):", "");
    if (url === null) return;
    patch(p.id, { status: "posted", post_url: url });
  };

  const shown = items.filter((p) => (filter === "all" ? true : filter === "posted" ? p.status === "posted" : p.status !== "posted"));
  const limit = LIMITS[f.platform];

  return (
    <section className="mt-6">
      <form onSubmit={create} className="space-y-3 panel rounded-2xl p-5">
        <div className="flex flex-wrap gap-3">
          <select value={f.platform} onChange={(e) => setF({ ...f, platform: e.target.value })} className="h-10 rounded-xl border border-line bg-bg px-3 text-sm" aria-label="Platform">{PLATFORMS.map((p) => <option key={p}>{p}</option>)}</select>
          <input value={f.link} onChange={(e) => setF({ ...f, link: e.target.value })} placeholder="Link to share (optional)" className={`${inputCls} flex-1`} />
        </div>
        <textarea required rows={4} value={f.content} onChange={(e) => setF({ ...f, content: e.target.value })} placeholder="Write a post, or let Hermes draft them (hermes social draft)…" className={`${inputCls} resize-y`} />
        <div className="flex items-center justify-between">
          <span className={`text-xs ${limit && f.content.length > limit ? "text-danger" : "text-muted"}`}>{f.content.length}{limit ? ` / ${limit}` : ""}</span>
          <button className={btn.primary}>Add to queue</button>
        </div>
      </form>

      <div className="mt-6"><Chips value={filter} options={["queue", "posted", "all"] as const} onChange={setFilter} /></div>
      <ErrorNote error={error} />
      {loading ? <Spinner /> : shown.length === 0 ? <Empty>Nothing here yet.</Empty> : (
        <ul className="mt-5 space-y-3">
          {shown.map((p) => {
            const share = shareUrl(p);
            return (
              <Card key={p.id}>
                <div className="flex items-center justify-between gap-3">
                  <p className="text-sm font-semibold capitalize">{p.platform} <span className="font-normal text-muted">· {fmtDate(p.created_at)} · by {p.created_by}</span></p>
                  <Badge>{p.status}</Badge>
                </div>
                <textarea
                  defaultValue={p.content}
                  rows={Math.min(8, Math.max(3, Math.ceil(p.content.length / 80)))}
                  disabled={p.status === "posted"}
                  onBlur={(e) => e.target.value !== p.content && patch(p.id, { content: e.target.value })}
                  className={`${inputCls} mt-3 resize-y`}
                />
                {p.link && <p className="mt-2 truncate text-xs text-muted">{p.link}</p>}
                <div className="mt-3 flex flex-wrap gap-2">
                  <button className={btn.ghost} onClick={() => copy(p)}><Copy className="h-4 w-4" /> {copied === p.id ? "Copied" : "Copy"}</button>
                  {share && p.status !== "posted" && <a className={btn.ghost} href={share} target="_blank" rel="noopener noreferrer"><ExternalLink className="h-4 w-4" /> Open {p.platform}</a>}
                  {p.status === "draft" && <button className={btn.ghost} onClick={() => patch(p.id, { status: "approved" })}><Check className="h-4 w-4" /> Approve</button>}
                  {p.status !== "posted" && <button className={btn.primary} onClick={() => markPosted(p)}>Mark posted</button>}
                  {p.post_url && <a className={btn.ghost} href={p.post_url} target="_blank" rel="noopener noreferrer">View live</a>}
                  <button className={btn.danger} onClick={() => remove(p.id)} aria-label="Delete"><Trash2 className="h-4 w-4" /></button>
                </div>
              </Card>
            );
          })}
        </ul>
      )}
    </section>
  );
}
