"use client";

import { useCallback, useEffect, useState } from "react";
import { ExternalLink, Pencil, Plus, Trash2 } from "lucide-react";
import { api, fmtDate, type Post } from "@/lib/api";
import { Badge, btn, Card, Chips, Empty, ErrorNote, inputCls, Spinner } from "./ui";

type Draft = { id?: number; title: string; slug: string; excerpt: string; tags: string; content_md: string; status: Post["status"]; publish_at: string; cover_image: string };
const blank: Draft = { title: "", slug: "", excerpt: "", tags: "", content_md: "", status: "draft", publish_at: "", cover_image: "" };

const toLocalInput = (iso: string | null) => (iso ? new Date(iso).toISOString().slice(0, 16) : "");

export default function BlogTab({ onChanged }: { onChanged: () => void }) {
  const [filter, setFilter] = useState<"all" | "draft" | "published" | "scheduled">("all");
  const [items, setItems] = useState<Post[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [edit, setEdit] = useState<Draft | null>(null);
  const [saving, setSaving] = useState(false);

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const j = await api<{ data: Post[] }>(`blog?status=${filter}&limit=200`);
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

  const open = async (p: Post) => {
    try {
      const j = await api<{ data: Post }>(`blog/${p.id}`);
      const d = j.data;
      setEdit({ id: d.id, title: d.title, slug: d.slug, excerpt: d.excerpt, tags: d.tags.join(", "), content_md: d.content_md ?? "", status: d.status, publish_at: toLocalInput(d.publish_at), cover_image: d.cover_image });
    } catch (e) {
      setError((e as Error).message);
    }
  };

  const save = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!edit) return;
    setSaving(true);
    setError(null);
    const body = {
      title: edit.title, excerpt: edit.excerpt, tags: edit.tags, content_md: edit.content_md, status: edit.status,
      cover_image: edit.cover_image, ...(edit.slug ? { slug: edit.slug } : {}),
      ...(edit.status === "scheduled" ? { publish_at: new Date(edit.publish_at + "Z").toISOString().replace(/\.\d+Z$/, "Z") } : {}),
    };
    try {
      if (edit.id) await api(`blog/${edit.id}`, { method: "PATCH", body });
      else await api("blog", { method: "POST", body });
      setEdit(null);
      await load();
      onChanged();
    } catch (err) {
      setError((err as Error).message);
    } finally {
      setSaving(false);
    }
  };

  const remove = async (p: Post) => {
    if (!window.confirm(`Delete "${p.title}"? This cannot be undone.`)) return;
    try {
      await api(`blog/${p.id}`, { method: "DELETE" });
      setItems((prev) => prev.filter((x) => x.id !== p.id));
      onChanged();
    } catch (e) {
      setError((e as Error).message);
    }
  };

  const quickPublish = async (p: Post) => {
    try {
      await api(`blog/${p.id}`, { method: "PATCH", body: { status: "published" } });
      await load();
      onChanged();
    } catch (e) {
      setError((e as Error).message);
    }
  };

  if (edit) {
    return (
      <form onSubmit={save} className="mt-6 space-y-3 panel rounded-2xl p-5">
        <input required value={edit.title} onChange={(e) => setEdit({ ...edit, title: e.target.value })} placeholder="Title" className={`${inputCls} text-base font-medium`} />
        <div className="grid gap-3 sm:grid-cols-2">
          <input value={edit.slug} onChange={(e) => setEdit({ ...edit, slug: e.target.value })} placeholder="slug (auto from title)" className={inputCls} />
          <input value={edit.tags} onChange={(e) => setEdit({ ...edit, tags: e.target.value })} placeholder="tags, comma separated" className={inputCls} />
        </div>
        <input maxLength={500} value={edit.excerpt} onChange={(e) => setEdit({ ...edit, excerpt: e.target.value })} placeholder="Short excerpt (shown in lists and Google)" className={inputCls} />
        <input value={edit.cover_image} onChange={(e) => setEdit({ ...edit, cover_image: e.target.value })} placeholder="Cover image URL (optional)" className={inputCls} />
        <textarea required rows={16} value={edit.content_md} onChange={(e) => setEdit({ ...edit, content_md: e.target.value })} placeholder="Write in Markdown: ## Heading, **bold**, - list, [link](https://…)" className={`${inputCls} resize-y font-mono`} />
        <div className="flex flex-wrap items-center gap-3">
          <select value={edit.status} onChange={(e) => setEdit({ ...edit, status: e.target.value as Post["status"] })} className="h-9 rounded-full border border-line bg-bg px-3 text-sm" aria-label="Status">
            <option value="draft">draft</option><option value="published">published</option><option value="scheduled">scheduled</option>
          </select>
          {edit.status === "scheduled" && <input type="datetime-local" required value={edit.publish_at} onChange={(e) => setEdit({ ...edit, publish_at: e.target.value })} className="h-9 rounded-full border border-line bg-bg px-3 text-sm" aria-label="Publish at (UTC)" />}
          <span className="text-xs text-muted">{edit.status === "scheduled" ? "Time is UTC." : ""}</span>
        </div>
        <ErrorNote error={error} />
        <div className="flex gap-2">
          <button disabled={saving} className={btn.primary}>{saving ? "Saving…" : "Save post"}</button>
          <button type="button" className={btn.ghost} onClick={() => setEdit(null)}>Cancel</button>
        </div>
      </form>
    );
  }

  return (
    <section className="mt-6">
      <div className="flex flex-wrap items-center justify-between gap-3">
        <Chips value={filter} options={["all", "draft", "published", "scheduled"] as const} onChange={setFilter} />
        <button className={btn.primary} onClick={() => setEdit({ ...blank })}><Plus className="h-4 w-4" /> New post</button>
      </div>
      <ErrorNote error={error} />
      {loading ? <Spinner /> : items.length === 0 ? <Empty>No posts yet. Write one, or let Hermes draft some (<code className="text-xs">hermes blog draft</code>).</Empty> : (
        <ul className="mt-5 space-y-3">
          {items.map((p) => (
            <Card key={p.id}>
              <div className="flex flex-wrap items-start justify-between gap-3">
                <div className="min-w-0">
                  <p className="font-semibold">{p.title}</p>
                  <p className="text-sm text-muted">/{p.slug}/ · {fmtDate(p.publish_at ?? p.created_at)} · by {p.created_by}</p>
                </div>
                <Badge>{p.status}</Badge>
              </div>
              {p.excerpt && <p className="mt-2 text-sm text-muted">{p.excerpt}</p>}
              <div className="mt-4 flex flex-wrap gap-2">
                <button className={btn.ghost} onClick={() => open(p)}><Pencil className="h-4 w-4" /> Edit</button>
                {p.status !== "published" && <button className={btn.primary} onClick={() => quickPublish(p)}>Publish now</button>}
                {p.status === "published" && <a className={btn.ghost} href={`/blog/${p.slug}/`} target="_blank" rel="noopener noreferrer"><ExternalLink className="h-4 w-4" /> View</a>}
                <button className={btn.danger} onClick={() => remove(p)} aria-label={`Delete ${p.title}`}><Trash2 className="h-4 w-4" /></button>
              </div>
            </Card>
          ))}
        </ul>
      )}
    </section>
  );
}
