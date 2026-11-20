/** Tiny client for the PHP API (same origin, session cookie for admins). */

export async function api<T = any>(path: string, opts: { method?: string; body?: unknown } = {}): Promise<T> {
  const res = await fetch(`/api/v1/${path}`, {
    method: opts.method ?? "GET",
    credentials: "same-origin",
    cache: "no-store",
    headers: {
      "X-Requested-With": "fetch",
      ...(opts.body !== undefined ? { "Content-Type": "application/json" } : {}),
    },
    body: opts.body !== undefined ? JSON.stringify(opts.body) : undefined,
  });
  let json: any = null;
  try {
    json = await res.json();
  } catch {
    /* non-JSON response */
  }
  if (!res.ok) {
    const err = new Error(json?.error || `Request failed (${res.status})`) as Error & { status?: number };
    err.status = res.status;
    throw err;
  }
  return json as T;
}

export type Lead = {
  id: number; full_name: string; email: string; phone: string; target_cefr: string; background: string;
  message: string; source: string; status: "new" | "contacted" | "qualified" | "converted" | "lost";
  notes: string | null; hermes_reply: string | null; created_at: string; updated_at: string;
};
export type Client = {
  id: number; lead_id: number | null; full_name: string; email: string; phone: string; level: string;
  plan: string; status: "active" | "paused" | "completed"; notes: string | null; started_at: string;
};
export type Mail = {
  id: number; direction: "in" | "out"; message_id: string | null; from_addr: string; to_addr: string; subject: string;
  body: string; status: string; hermes_draft: string | null; lead_id: number | null; reply_to_id: number | null;
  received_at: string | null; sent_at: string | null; created_by: string; created_at: string;
};
export type Post = {
  id: number; slug: string; title: string; excerpt: string; content_md?: string; tags: string[]; cover_image: string;
  status: "draft" | "published" | "scheduled"; publish_at: string | null; created_by: string; created_at: string; url: string;
};
export type Social = {
  id: number; platform: string; content: string; link: string; media_url: string; status: string;
  scheduled_at: string | null; posted_at: string | null; post_url: string; blog_id: number | null; created_by: string; created_at: string;
};
export type Book = {
  id: number; slug: string; title: string; author: string; level: string; pages: number;
  cover: string | null; file_url: string; file_name?: string; category: string;
  blurb: string; blurb_hi: string; created_at: string; updated_at: string;
};
export type Activity = { id: number; actor: string; action: string; entity: string; entity_id: number | null; detail: string; created_at: string };

export const fmtDate = (iso?: string | null) =>
  iso ? new Date(iso).toLocaleString(undefined, { dateStyle: "medium", timeStyle: "short" }) : "";
