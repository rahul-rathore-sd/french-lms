"use client";

import { useState } from "react";
import { BookOpen, ExternalLink } from "lucide-react";
import { useProgress } from "@/lib/progress";
import { bookShelf, siteCategories, sites, type BookItem, type SiteCategory } from "@/lib/library/resources";

/** Book cover, or a coloured title card when the PDF has no usable cover. */
function Cover({ book }: { book: BookItem }) {
  if (book.cover) {
    // eslint-disable-next-line @next/next/no-img-element
    return <img src={book.cover} alt={`Cover of ${book.title}`} loading="lazy" className="aspect-[3/4] w-full rounded-lg object-cover shadow-soft" />;
  }
  return (
    <div className="flex aspect-[3/4] w-full flex-col justify-between rounded-lg bg-gradient-to-br from-accent to-fg p-4 text-accent-fg shadow-soft" role="img" aria-label={`Cover of ${book.title}`}>
      <BookOpen className="h-6 w-6 opacity-80" />
      <p className="font-display text-lg font-semibold leading-tight">{book.title}</p>
    </div>
  );
}

export default function ResourcesView() {
  const { showHindi } = useProgress();
  const [cat, setCat] = useState<SiteCategory | "All">("All");
  const list = sites.filter((s) => cat === "All" || s.category === cat);

  return (
    <div className="space-y-10">
      <section>
        <h2 className="font-display text-2xl font-semibold">My books · मेरी किताबें</h2>
        <p className="mt-1 text-sm text-muted">Books from your library folder. Their topics shaped our Grammar section.</p>
        <ul className="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
          {bookShelf.map((b) => (
            <li key={b.slug} className="flex gap-4 rounded-2xl border border-line bg-surface p-4">
              <div className="w-28 shrink-0 sm:w-32"><Cover book={b} /></div>
              <div className="min-w-0">
                <span className="rounded-full bg-fg px-2.5 py-1 text-xs font-semibold text-bg">{b.level || "—"}</span>
                <h3 className="mt-2 font-semibold leading-snug">{b.title}</h3>
                {b.author && <p className="text-xs text-muted">{b.author}</p>}
                <p className="mt-2 text-sm text-muted">{b.blurb}</p>
                {showHindi && b.blurb_hi && <p className="mt-1 text-sm text-accent">{b.blurb_hi}</p>}
                <p className="mt-2 text-xs text-muted">{b.pages} pages</p>
              </div>
            </li>
          ))}
        </ul>
      </section>

      <section>
        <h2 className="font-display text-2xl font-semibold">Best websites · बेहतरीन वेबसाइटें</h2>
        <div className="-mx-4 mt-3 overflow-x-auto px-4 sm:mx-0 sm:px-0" role="tablist" aria-label="Website category">
          <div className="flex min-w-max gap-1.5">
            {(["All", ...siteCategories] as const).map((c) => (
              <button key={c} role="tab" aria-selected={cat === c} onClick={() => setCat(c)}
                className={`h-9 rounded-full px-4 text-xs font-medium transition ${cat === c ? "bg-accent text-accent-fg" : "border border-line text-muted hover:text-fg"}`}>{c}</button>
            ))}
          </div>
        </div>
        <ul className="mt-4 grid gap-3 sm:grid-cols-2">
          {list.map((s) => (
            <li key={s.url}>
              <a href={s.url} target="_blank" rel="noopener noreferrer" className="group block h-full rounded-2xl border border-line bg-surface p-4 transition hover:-translate-y-0.5 hover:border-accent/40 hover:shadow-soft">
                <div className="flex items-start justify-between gap-3">
                  <h3 className="font-semibold leading-snug">{s.name}</h3>
                  <ExternalLink className="mt-1 h-4 w-4 shrink-0 text-muted group-hover:text-accent" />
                </div>
                <p className="mt-1 text-xs text-muted">{s.category} · {s.level} · {s.free}</p>
                <p className="mt-2 text-sm text-muted">{s.desc}</p>
                {showHindi && <p className="mt-1 text-sm text-accent">{s.desc_hi}</p>}
              </a>
            </li>
          ))}
        </ul>
      </section>
    </div>
  );
}
