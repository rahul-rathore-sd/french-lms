"use client";

import { useEffect, useState } from "react";
import { api, fmtDate, type Activity } from "@/lib/api";

export type Stats = Record<string, number>;

const tiles: [keyof Stats & string, string][] = [
  ["leads_new", "New leads"],
  ["clients_active", "Active clients"],
  ["emails_unread", "Unread emails"],
  ["emails_to_send", "Emails waiting to send"],
  ["blog_published", "Published posts"],
  ["blog_drafts", "Blog drafts"],
  ["social_queue", "Social queue"],
  ["social_posted", "Posted"],
];

export default function OverviewTab({ stats, refreshKey }: { stats: Stats | null; refreshKey: number }) {
  const [activity, setActivity] = useState<Activity[]>([]);
  useEffect(() => {
    api<{ data: Activity[] }>("activity?limit=25").then((j) => setActivity(j.data)).catch(() => {});
  }, [refreshKey]);

  return (
    <section className="mt-6 space-y-6">
      <div className="grid grid-cols-2 gap-3 md:grid-cols-4">
        {tiles.map(([k, label]) => (
          <div key={k} className="panel rounded-2xl p-5">
            <p className="font-display text-3xl font-semibold">{stats ? stats[k] ?? 0 : "–"}</p>
            <p className="text-sm text-muted">{label}</p>
          </div>
        ))}
      </div>

      <div className="panel rounded-2xl p-6">
        <h2 className="font-semibold">Recent activity</h2>
        {activity.length === 0 ? <p className="mt-2 text-sm text-muted">Nothing yet.</p> : (
          <ul className="mt-3 divide-y divide-line text-sm">
            {activity.map((a) => (
              <li key={a.id} className="flex flex-wrap items-baseline justify-between gap-2 py-2.5">
                <span><span className={`mr-2 rounded-full px-2 py-0.5 text-[11px] font-medium ${a.actor === "hermes" ? "bg-accent/15 text-accent" : "bg-raised text-muted"}`}>{a.actor}</span>{a.action} {a.entity}{a.detail && <span className="text-muted"> · {a.detail}</span>}</span>
                <span className="text-xs text-muted">{fmtDate(a.created_at)}</span>
              </li>
            ))}
          </ul>
        )}
      </div>

      <div className="panel rounded-2xl p-6">
        <h2 className="font-semibold">Connect Hermes</h2>
        <p className="mt-1 text-sm text-muted">Hermes runs on your PC and talks to this site with a secret key (never shown here). Mailbox passwords also stay on your PC.</p>
        <pre className="mt-4 overflow-x-auto rounded-xl bg-raised p-4 text-xs leading-relaxed">{`python hermes.py config --url ${typeof window !== "undefined" ? window.location.origin : "https://yourdomain.com"} --key <HERMES_API_KEY>
python hermes.py status
python hermes.py autopilot      # leads, mail, blog, social: drafts only
python hermes.py mail send      # sends mail you approved here`}</pre>
      </div>
    </section>
  );
}
