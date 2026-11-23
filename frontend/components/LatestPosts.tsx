"use client";

import { useEffect, useState } from "react";
import { ArrowRight } from "lucide-react";

type Post = { slug: string; title: string; excerpt: string; publish_at: string | null; created_at: string };

/** Latest blog articles from the PHP API. Renders nothing until there is at least one. */
export default function LatestPosts() {
  const [posts, setPosts] = useState<Post[]>([]);

  useEffect(() => {
    let cancelled = false;
    fetch("/api/v1/blog?limit=3")
      .then((r) => (r.ok ? r.json() : null))
      .then((j) => {
        if (!cancelled && Array.isArray(j?.data)) setPosts(j.data);
      })
      .catch(() => {});
    return () => {
      cancelled = true;
    };
  }, []);

  if (posts.length === 0) return null;

  return (
    <section className="mx-auto max-w-6xl px-4 py-16 sm:px-6 sm:py-20">
      <div className="flex items-end justify-between gap-4">
        <div>
          <p className="text-sm font-medium text-accent">From the blog</p>
          <h2 className="mt-3 font-display text-3xl font-medium tracking-tight sm:text-4xl">Fresh reading for French learners.</h2>
        </div>
        <a href="/blog/" className="hidden items-center gap-1 text-sm font-medium text-accent sm:inline-flex">
          All articles <ArrowRight className="h-4 w-4" />
        </a>
      </div>
      <ul className="mt-8 grid gap-4 md:grid-cols-3">
        {posts.map((p) => (
          <li key={p.slug}>
            <a
              href={`/blog/${p.slug}/`}
              className="flex h-full flex-col panel rounded-2xl p-6 transition hover:-translate-y-1 hover:border-accent/40 hover:shadow-soft"
            >
              <span className="text-xs text-muted">{(p.publish_at ?? p.created_at).slice(0, 10)}</span>
              <h3 className="mt-2 font-display text-xl font-semibold leading-snug">{p.title}</h3>
              <p className="mt-2 flex-1 text-sm leading-relaxed text-muted">{p.excerpt}</p>
            </a>
          </li>
        ))}
      </ul>
    </section>
  );
}
