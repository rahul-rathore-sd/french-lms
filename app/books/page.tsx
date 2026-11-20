"use client";

import { useMemo, useState } from "react";
import Link from "next/link";
import {
  ArrowLeft,
  BookOpen,
  CheckCircle2,
  Download,
  ExternalLink,
  Eye,
  FileText,
  GraduationCap,
  Languages,
  Library,
  Search,
  Sparkles,
  X,
} from "lucide-react";
import SiteHeader from "@/components/SiteHeader";
import MobileTabBar from "@/components/MobileTabBar";
import { useBooks, type BookItem } from "@/lib/books";
import { useProgress } from "@/lib/progress";

const LEVELS = ["All", "A1", "A2", "B1", "B2", "C1", "C2"] as const;
const CATEGORIES = ["All", "Grammar", "Literature", "Exam Prep", "Vocabulary"] as const;

function BookCover({ book }: { book: BookItem }) {
  if (book.cover) {
    return (
      <div className="relative aspect-[3/4] w-full overflow-hidden rounded-xl bg-raised shadow-soft transition-transform duration-300 group-hover:scale-[1.03]">
        {/* eslint-disable-next-line @next/next/no-img-element */}
        <img
          src={book.cover}
          alt={`Couverture de ${book.title}`}
          loading="lazy"
          className="h-full w-full object-cover"
        />
        <div className="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100" />
      </div>
    );
  }

  return (
    <div
      className="relative flex aspect-[3/4] w-full flex-col justify-between overflow-hidden rounded-xl bg-gradient-to-br from-accent/90 via-slate-800 to-fg p-5 text-accent-fg shadow-soft transition-transform duration-300 group-hover:scale-[1.03]"
      role="img"
      aria-label={`Couverture de ${book.title}`}
    >
      <div className="flex items-center justify-between opacity-80">
        <BookOpen className="h-6 w-6" />
        <span className="text-[11px] font-semibold uppercase tracking-wider">{book.level}</span>
      </div>
      <div>
        <p className="line-clamp-3 font-display text-lg font-semibold leading-snug">{book.title}</p>
        {book.author && <p className="mt-2 text-xs opacity-70 line-clamp-1">{book.author}</p>}
      </div>
      <div className="h-1 w-10 rounded-full bg-gold/80" />
    </div>
  );
}

export default function BooksPage() {
  const { books } = useBooks();
  const { showHindi } = useProgress();
  const [level, setLevel] = useState<string>("All");
  const [category, setCategory] = useState<string>("All");
  const [query, setQuery] = useState("");
  const [selectedBook, setSelectedBook] = useState<BookItem | null>(null);

  const filtered = useMemo(() => {
    return books.filter((b) => {
      const matchLevel =
        level === "All" ||
        b.level.toLowerCase().includes(level.toLowerCase());
      const matchCat =
        category === "All" ||
        (b.category && b.category.toLowerCase() === category.toLowerCase());
      const matchQuery =
        !query ||
        b.title.toLowerCase().includes(query.toLowerCase()) ||
        b.author.toLowerCase().includes(query.toLowerCase()) ||
        b.blurb.toLowerCase().includes(query.toLowerCase()) ||
        (b.blurb_hi && b.blurb_hi.toLowerCase().includes(query.toLowerCase()));
      return matchLevel && matchCat && matchQuery;
    });
  }, [books, level, category, query]);

  return (
    <div className="min-h-screen">
      <SiteHeader />

      <main className="mx-auto max-w-6xl px-4 py-6 sm:px-6 sm:py-10">
        {/* Breadcrumb Navigation */}
        <div className="flex items-center gap-2 text-xs text-muted sm:text-sm">
          <Link href="/library" className="hover:text-fg transition-colors">
            Library
          </Link>
          <span>/</span>
          <span className="text-fg font-medium">Books & Manuals</span>
        </div>

        {/* Hero Banner */}
        <div className="mt-4 animate-rise">
          <div className="inline-flex items-center gap-2 rounded-full border border-line bg-surface/70 px-3.5 py-1.5 text-xs font-medium text-muted backdrop-blur">
            <span className="h-2 w-2 rounded-full bg-accent animate-pulse" />
            <span>Bibliothèque Française · French Books Sanctuary</span>
          </div>

          <h1 className="mt-3 font-display text-3xl font-medium tracking-tight sm:text-5xl">
            Books, Manuals & Readers
          </h1>
          <p className="mt-2 max-w-2xl text-sm leading-relaxed text-muted sm:text-base">
            Curated French textbooks, grammar companions, and literary works with English &amp; Hindi explanations.
            {showHindi && (
              <span className="block mt-1 text-accent">
                फ़्रेंच भाषा की प्रामाणिक पुस्तकें, व्याकरण गाइड और अभ्यास पुस्तकें — अनुवाद सहित।
              </span>
            )}
          </p>
        </div>

        {/* Search & Filters */}
        <div className="mt-8 space-y-3.5">
          {/* Search Input */}
          <div className="relative max-w-md">
            <Search className="absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-muted" />
            <input
              type="text"
              value={query}
              onChange={(e) => setQuery(e.target.value)}
              placeholder="Search by book name, author, or topic..."
              className="w-full rounded-2xl border border-line bg-surface py-2.5 pl-10 pr-9 text-sm outline-none transition placeholder:text-muted focus:border-accent focus:ring-2 focus:ring-accent/15"
            />
            {query && (
              <button
                onClick={() => setQuery("")}
                className="absolute right-3 top-1/2 -translate-y-1/2 text-muted hover:text-fg"
              >
                <X className="h-4 w-4" />
              </button>
            )}
          </div>

          {/* Level Filter Pills */}
          <div className="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div className="-mx-4 flex overflow-x-auto px-4 sm:mx-0 sm:px-0 gap-1.5" role="tablist" aria-label="Level filters">
              {LEVELS.map((lvl) => (
                <button
                  key={lvl}
                  role="tab"
                  aria-selected={level === lvl}
                  onClick={() => setLevel(lvl)}
                  className={`inline-flex shrink-0 items-center rounded-full px-3.5 py-1.5 text-xs font-medium transition active:scale-95 ${
                    level === lvl
                      ? "bg-accent text-accent-fg shadow-glow"
                      : "border border-line bg-surface text-muted hover:text-fg"
                  }`}
                >
                  {lvl === "All" ? "All Levels" : lvl}
                </button>
              ))}
            </div>

            {/* Category Pills */}
            <div className="-mx-4 flex overflow-x-auto px-4 sm:mx-0 sm:px-0 gap-1.5" role="tablist" aria-label="Category filters">
              {CATEGORIES.map((cat) => (
                <button
                  key={cat}
                  role="tab"
                  aria-selected={category === cat}
                  onClick={() => setCategory(cat)}
                  className={`inline-flex shrink-0 items-center rounded-full px-3 py-1 text-xs transition ${
                    category === cat
                      ? "bg-fg text-bg font-semibold"
                      : "border border-line bg-surface text-muted hover:text-fg"
                  }`}
                >
                  {cat}
                </button>
              ))}
            </div>
          </div>
        </div>

        {/* Counter Info */}
        <div className="mt-6 flex items-center justify-between text-xs text-muted">
          <span>
            Showing <b>{filtered.length}</b> {filtered.length === 1 ? "book" : "books"}
          </span>
          <span className="hidden sm:inline">Tap any book card to inspect details or read PDF</span>
        </div>

        {/* Books Grid */}
        {filtered.length === 0 ? (
          <div className="mt-8 rounded-3xl border border-dashed border-line bg-surface/50 p-12 text-center">
            <BookOpen className="mx-auto h-10 w-10 text-muted opacity-50" />
            <h3 className="mt-4 font-display text-lg font-medium">No books found</h3>
            <p className="mt-1 text-sm text-muted">Try adjusting your search query or level filters.</p>
            <button
              onClick={() => {
                setLevel("All");
                setCategory("All");
                setQuery("");
              }}
              className="mt-4 inline-flex items-center gap-1.5 rounded-full border border-line px-4 py-2 text-xs font-medium text-fg hover:border-accent"
            >
              Reset all filters
            </button>
          </div>
        ) : (
          <div className="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            {filtered.map((b) => (
              <article
                key={b.slug}
                onClick={() => setSelectedBook(b)}
                className="group relative flex flex-col justify-between cursor-pointer rounded-2xl border border-line bg-surface p-4 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-accent/40 hover:shadow-soft sm:p-5"
              >
                <div className="flex gap-4">
                  {/* Book Cover */}
                  <div className="w-28 shrink-0 sm:w-32">
                    <BookCover book={b} />
                  </div>

                  {/* Book Metadata */}
                  <div className="flex min-w-0 flex-1 flex-col justify-between">
                    <div>
                      <div className="flex items-center gap-1.5 flex-wrap">
                        <span className="rounded-full bg-fg px-2.5 py-0.5 text-xs font-semibold text-bg">
                          {b.level || "All"}
                        </span>
                        {b.category && (
                          <span className="rounded-full bg-raised px-2 py-0.5 text-[11px] text-muted">
                            {b.category}
                          </span>
                        )}
                      </div>

                      <h2 className="mt-2.5 font-display text-base font-semibold leading-snug tracking-tight text-fg group-hover:text-accent transition-colors sm:text-lg">
                        {b.title}
                      </h2>
                      {b.author && (
                        <p className="mt-1 text-xs text-muted truncate">
                          {b.author}
                        </p>
                      )}

                      <p className="mt-2.5 line-clamp-3 text-xs leading-relaxed text-muted sm:text-sm">
                        {b.blurb}
                      </p>
                      {showHindi && b.blurb_hi && (
                        <p className="mt-1.5 line-clamp-2 text-xs leading-relaxed text-accent">
                          {b.blurb_hi}
                        </p>
                      )}
                    </div>

                    <div className="mt-4 flex items-center justify-between border-t border-line/60 pt-2.5 text-xs text-muted">
                      <span>{b.pages ? `${b.pages} pages` : "Complete guide"}</span>
                      <span className="inline-flex items-center gap-1 font-medium text-accent">
                        Details <Eye className="h-3.5 w-3.5 transition-transform group-hover:scale-110" />
                      </span>
                    </div>
                  </div>
                </div>
              </article>
            ))}
          </div>
        )}

        {/* Selected Book Reader / Synopsis Modal */}
        {selectedBook && (
          <div
            className="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm animate-rise"
            onClick={() => setSelectedBook(null)}
          >
            <div
              className="relative max-h-[90vh] w-full max-w-xl overflow-y-auto rounded-3xl border border-line bg-surface p-6 shadow-2xl sm:p-8"
              onClick={(e) => e.stopPropagation()}
            >
              {/* Close Button */}
              <button
                onClick={() => setSelectedBook(null)}
                aria-label="Fermer"
                className="absolute right-5 top-5 grid h-9 w-9 place-items-center rounded-full border border-line text-muted transition hover:text-fg"
              >
                <X className="h-4 w-4" />
              </button>

              <div className="flex flex-col gap-6 sm:flex-row">
                <div className="w-36 shrink-0 mx-auto sm:mx-0">
                  <BookCover book={selectedBook} />
                </div>

                <div className="flex-1 min-w-0">
                  <div className="flex items-center gap-2 flex-wrap">
                    <span className="rounded-full bg-fg px-3 py-1 text-xs font-semibold text-bg">
                      {selectedBook.level || "All"}
                    </span>
                    {selectedBook.category && (
                      <span className="rounded-full bg-raised px-2.5 py-0.5 text-xs text-muted">
                        {selectedBook.category}
                      </span>
                    )}
                    <span className="text-xs text-muted">{selectedBook.pages} pages</span>
                  </div>

                  <h3 className="mt-3 font-display text-2xl font-semibold leading-tight">
                    {selectedBook.title}
                  </h3>
                  {selectedBook.author && (
                    <p className="mt-1 text-sm text-muted">Par {selectedBook.author}</p>
                  )}

                  <div className="mt-4 space-y-2 rounded-2xl bg-raised/50 p-4 text-xs sm:text-sm">
                    <p className="leading-relaxed text-fg">{selectedBook.blurb}</p>
                    {selectedBook.blurb_hi && (
                      <p className="leading-relaxed text-accent pt-1 border-t border-line/60">
                        {selectedBook.blurb_hi}
                      </p>
                    )}
                  </div>

                  <div className="mt-6 flex flex-wrap items-center gap-3">
                    {selectedBook.file_url ? (
                      <a
                        href={selectedBook.file_url}
                        target="_blank"
                        rel="noopener noreferrer"
                        className="inline-flex h-11 items-center gap-2 rounded-full bg-accent px-5 text-sm font-medium text-accent-fg shadow-glow transition hover:brightness-110"
                      >
                        <FileText className="h-4 w-4" />
                        <span>Lire le PDF · Open Book</span>
                      </a>
                    ) : (
                      <span className="text-xs text-muted italic">
                        Available in the physical / institute library
                      </span>
                    )}

                    <button
                      type="button"
                      onClick={() => setSelectedBook(null)}
                      className="inline-flex h-11 items-center rounded-full border border-line px-5 text-sm font-medium text-muted hover:text-fg"
                    >
                      Close
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        )}
      </main>

      <MobileTabBar />
    </div>
  );
}
