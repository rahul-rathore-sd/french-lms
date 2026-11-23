"use client";

import { useState } from "react";
import { BookOpen, ExternalLink, FileText, Image as ImageIcon, Loader2, Pencil, Plus, Trash2, UploadCloud, X } from "lucide-react";
import { useBooks, type BookItem } from "@/lib/books";
import { btn, inputCls, Card, Empty, ErrorNote } from "./ui";

const LEVELS = ["All", "A1", "A2", "A2–B1", "B1", "B2", "C1", "C2"] as const;
const CATEGORIES = ["Grammar", "Literature", "Exam Prep", "Vocabulary", "Reading", "General"] as const;

type DraftBook = {
  id?: number | string;
  slug?: string;
  title: string;
  author: string;
  level: string;
  category: string;
  pages: number;
  cover: string;
  file_url: string;
  blurb: string;
  blurb_hi: string;
};

const blank: DraftBook = {
  title: "",
  author: "",
  level: "A2–B1",
  category: "Grammar",
  pages: 150,
  cover: "",
  file_url: "",
  blurb: "",
  blurb_hi: "",
};

export default function BooksTab({ onChanged }: { onChanged?: () => void }) {
  const { books, saveBook, deleteBook } = useBooks();
  const [levelFilter, setLevelFilter] = useState<string>("All");
  const [query, setQuery] = useState("");
  const [editing, setEditing] = useState<DraftBook | null>(null);
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const filtered = books.filter((b) => {
    const matchesLevel = levelFilter === "All" || b.level.includes(levelFilter) || (levelFilter === "A2" && b.level.includes("A2"));
    const matchesQuery =
      !query ||
      b.title.toLowerCase().includes(query.toLowerCase()) ||
      b.author.toLowerCase().includes(query.toLowerCase()) ||
      b.blurb.toLowerCase().includes(query.toLowerCase());
    return matchesLevel && matchesQuery;
  });

  const handleEdit = (b: BookItem) => {
    setEditing({
      id: b.id,
      slug: b.slug,
      title: b.title,
      author: b.author,
      level: b.level,
      category: b.category || "Grammar",
      pages: b.pages,
      cover: b.cover || "",
      file_url: b.file_url || (b.file ? `/books/${encodeURIComponent(b.file)}` : ""),
      blurb: b.blurb,
      blurb_hi: b.blurb_hi || "",
    });
  };

  const handleSave = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!editing) return;
    setSaving(true);
    setError(null);
    try {
      await saveBook({
        id: editing.id,
        slug: editing.slug,
        title: editing.title.trim(),
        author: editing.author.trim(),
        level: editing.level,
        category: editing.category,
        pages: Number(editing.pages) || 0,
        cover: editing.cover.trim() || null,
        file_url: editing.file_url.trim() || undefined,
        blurb: editing.blurb.trim(),
        blurb_hi: editing.blurb_hi.trim(),
      });
      setEditing(null);
      onChanged?.();
    } catch (err) {
      setError((err as Error).message || "Failed to save book.");
    } finally {
      setSaving(false);
    }
  };

  const handleDelete = async (slug: string, title: string) => {
    if (!window.confirm(`Delete "${title}"? This will remove it from the books section.`)) return;
    try {
      await deleteBook(slug);
      onChanged?.();
    } catch (err) {
      setError((err as Error).message || "Failed to delete book.");
    }
  };

  // Convert uploaded image file to data URL
  const handleCoverUpload = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (!file || !editing) return;
    const reader = new FileReader();
    reader.onload = (ev) => {
      if (typeof ev.target?.result === "string") {
        setEditing({ ...editing, cover: ev.target.result });
      }
    };
    reader.readAsDataURL(file);
  };

  // Convert uploaded PDF file to data URL or file path
  const handlePdfUpload = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (!file || !editing) return;
    const reader = new FileReader();
    reader.onload = (ev) => {
      if (typeof ev.target?.result === "string") {
        setEditing({ ...editing, file_url: ev.target.result });
      }
    };
    reader.readAsDataURL(file);
  };

  return (
    <div className="mt-6 space-y-6">
      {/* Top action bar & stats */}
      <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h2 className="font-display text-2xl font-semibold">Books Management</h2>
          <p className="mt-0.5 text-xs text-muted sm:text-sm">
            Upload new French books, manuals and PDF resources for your students.
          </p>
        </div>
        <button
          onClick={() => setEditing({ ...blank })}
          className={`${btn.primary} self-start sm:self-auto shadow-glow`}
        >
          <Plus className="h-4 w-4" /> Add New Book
        </button>
      </div>

      {/* Stats counter */}
      <div className="grid grid-cols-2 gap-3 sm:grid-cols-4">
        <div className="panel rounded-2xl p-4">
          <p className="text-xs text-muted">Total Books</p>
          <p className="mt-1 font-display text-2xl font-semibold">{books.length}</p>
        </div>
        <div className="panel rounded-2xl p-4">
          <p className="text-xs text-muted">Filtered</p>
          <p className="mt-1 font-display text-2xl font-semibold">{filtered.length}</p>
        </div>
        <div className="panel rounded-2xl p-4">
          <p className="text-xs text-muted">Total Pages</p>
          <p className="mt-1 font-display text-2xl font-semibold">
            {books.reduce((acc, b) => acc + (b.pages || 0), 0)}
          </p>
        </div>
        <div className="panel rounded-2xl p-4">
          <p className="text-xs text-muted">CEFR Covered</p>
          <p className="mt-1 font-display text-2xl font-semibold">A1 – C2</p>
        </div>
      </div>

      <ErrorNote error={error} />

      {/* Add / Edit Form Modal */}
      {editing && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm">
          <div className="relative max-h-[90vh] w-full max-w-2xl overflow-y-auto panel rounded-3xl p-6 shadow-2xl sm:p-8">
            <div className="flex items-center justify-between border-b border-line pb-4">
              <div>
                <h3 className="font-display text-xl font-semibold">
                  {editing.id ? "Edit Book" : "Upload New Book"}
                </h3>
                <p className="text-xs text-muted">Fill in the book details, cover image and PDF link.</p>
              </div>
              <button
                onClick={() => setEditing(null)}
                className="grid h-8 w-8 place-items-center rounded-full border border-line text-muted hover:text-fg"
              >
                <X className="h-4 w-4" />
              </button>
            </div>

            <form onSubmit={handleSave} className="mt-5 space-y-4">
              <div className="grid gap-4 sm:grid-cols-2">
                <div>
                  <label className="mb-1.5 block text-xs font-medium text-fg">Book Title *</label>
                  <input
                    required
                    value={editing.title}
                    onChange={(e) => setEditing({ ...editing, title: e.target.value })}
                    placeholder="e.g. Grammaire progressive du français"
                    className={inputCls}
                  />
                </div>
                <div>
                  <label className="mb-1.5 block text-xs font-medium text-fg">Author *</label>
                  <input
                    required
                    value={editing.author}
                    onChange={(e) => setEditing({ ...editing, author: e.target.value })}
                    placeholder="e.g. Maïa Grégoire, Odile Thiévenaz"
                    className={inputCls}
                  />
                </div>
              </div>

              <div className="grid grid-cols-3 gap-3">
                <div>
                  <label className="mb-1.5 block text-xs font-medium text-fg">CEFR Level</label>
                  <select
                    value={editing.level}
                    onChange={(e) => setEditing({ ...editing, level: e.target.value })}
                    className={inputCls}
                  >
                    {LEVELS.filter((l) => l !== "All").map((lvl) => (
                      <option key={lvl} value={lvl}>{lvl}</option>
                    ))}
                  </select>
                </div>
                <div>
                  <label className="mb-1.5 block text-xs font-medium text-fg">Category</label>
                  <select
                    value={editing.category}
                    onChange={(e) => setEditing({ ...editing, category: e.target.value })}
                    className={inputCls}
                  >
                    {CATEGORIES.map((c) => (
                      <option key={c} value={c}>{c}</option>
                    ))}
                  </select>
                </div>
                <div>
                  <label className="mb-1.5 block text-xs font-medium text-fg">Pages</label>
                  <input
                    type="number"
                    min={1}
                    value={editing.pages}
                    onChange={(e) => setEditing({ ...editing, pages: parseInt(e.target.value) || 0 })}
                    className={inputCls}
                  />
                </div>
              </div>

              {/* Cover Image */}
              <div className="rounded-2xl border border-line bg-raised/40 p-4">
                <label className="mb-1.5 block text-xs font-medium text-fg">Book Cover Image</label>
                <div className="flex flex-col gap-3 sm:flex-row sm:items-center">
                  <input
                    value={editing.cover}
                    onChange={(e) => setEditing({ ...editing, cover: e.target.value })}
                    placeholder="Cover image URL (e.g. /books/grammaire.jpg or https://...)"
                    className={`${inputCls} flex-1`}
                  />
                  <label className="inline-flex cursor-pointer items-center justify-center gap-1.5 rounded-full border border-line bg-surface px-4 py-2.5 text-xs font-medium text-fg transition hover:border-accent">
                    <ImageIcon className="h-3.5 w-3.5" />
                    <span>Upload file</span>
                    <input type="file" accept="image/*" onChange={handleCoverUpload} className="hidden" />
                  </label>
                </div>

                {editing.cover && (
                  <div className="mt-3 flex items-center gap-3">
                    {/* eslint-disable-next-line @next/next/no-img-element */}
                    <img
                      src={editing.cover}
                      alt="Cover preview"
                      className="h-16 w-12 rounded-lg object-cover shadow-sm"
                    />
                    <span className="text-xs text-muted">Preview ready</span>
                    <button
                      type="button"
                      onClick={() => setEditing({ ...editing, cover: "" })}
                      className="text-xs text-danger hover:underline"
                    >
                      Clear
                    </button>
                  </div>
                )}
              </div>

              {/* PDF or Reading link */}
              <div className="rounded-2xl border border-line bg-raised/40 p-4">
                <label className="mb-1.5 block text-xs font-medium text-fg">Book PDF / File Link</label>
                <div className="flex flex-col gap-3 sm:flex-row sm:items-center">
                  <input
                    value={editing.file_url}
                    onChange={(e) => setEditing({ ...editing, file_url: e.target.value })}
                    placeholder="PDF URL or link (e.g. /books/sample.pdf or https://...)"
                    className={`${inputCls} flex-1`}
                  />
                  <label className="inline-flex cursor-pointer items-center justify-center gap-1.5 rounded-full border border-line bg-surface px-4 py-2.5 text-xs font-medium text-fg transition hover:border-accent">
                    <UploadCloud className="h-3.5 w-3.5" />
                    <span>Attach PDF</span>
                    <input type="file" accept=".pdf" onChange={handlePdfUpload} className="hidden" />
                  </label>
                </div>
              </div>

              {/* Descriptions */}
              <div>
                <label className="mb-1.5 block text-xs font-medium text-fg">English Description / Blurb *</label>
                <textarea
                  required
                  rows={3}
                  value={editing.blurb}
                  onChange={(e) => setEditing({ ...editing, blurb: e.target.value })}
                  placeholder="Concise synopsis of the book and what learners will gain..."
                  className={`${inputCls} resize-y`}
                />
              </div>

              <div>
                <label className="mb-1.5 block text-xs font-medium text-fg">Hindi Summary / हिंदी सारांश (Optional)</label>
                <textarea
                  rows={2}
                  value={editing.blurb_hi}
                  onChange={(e) => setEditing({ ...editing, blurb_hi: e.target.value })}
                  placeholder="किताब का संक्षिप्त विवरण हिंदी में..."
                  className={`${inputCls} resize-y`}
                />
              </div>

              <div className="flex justify-end gap-3 pt-3">
                <button
                  type="button"
                  disabled={saving}
                  onClick={() => setEditing(null)}
                  className={btn.ghost}
                >
                  Cancel
                </button>
                <button type="submit" disabled={saving} className={btn.primary}>
                  {saving && <Loader2 className="h-4 w-4 animate-spin" />}
                  {editing.id ? "Update Book" : "Publish Book"}
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      {/* Filter and search row */}
      <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div className="-mx-4 flex overflow-x-auto px-4 sm:mx-0 sm:px-0 gap-1.5">
          {LEVELS.map((lvl) => (
            <button
              key={lvl}
              onClick={() => setLevelFilter(lvl)}
              className={`rounded-full px-3.5 py-1.5 text-xs font-medium transition ${
                levelFilter === lvl
                  ? "bg-accent text-accent-fg shadow-glow"
                  : "border border-line bg-surface text-muted hover:text-fg"
              }`}
            >
              {lvl}
            </button>
          ))}
        </div>

        <input
          value={query}
          onChange={(e) => setQuery(e.target.value)}
          placeholder="Search books, authors, topics..."
          className={`${inputCls} max-w-xs`}
        />
      </div>

      {/* Books List Grid */}
      {filtered.length === 0 ? (
        <Empty>No books found matching your search. Click &ldquo;Add New Book&rdquo; above to upload one.</Empty>
      ) : (
        <ul className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
          {filtered.map((b) => (
            <Card key={b.slug}>
              <div className="flex gap-4">
                {/* Book Cover */}
                <div className="w-24 shrink-0 sm:w-28">
                  {b.cover ? (
                    // eslint-disable-next-line @next/next/no-img-element
                    <img
                      src={b.cover}
                      alt={b.title}
                      loading="lazy"
                      className="aspect-[3/4] w-full rounded-xl object-cover shadow-soft transition hover:scale-105"
                    />
                  ) : (
                    <div className="flex aspect-[3/4] w-full flex-col justify-between rounded-xl bg-gradient-to-br from-accent to-fg p-3 text-accent-fg shadow-soft">
                      <BookOpen className="h-5 w-5 opacity-80" />
                      <p className="line-clamp-3 font-display text-xs font-semibold leading-tight">{b.title}</p>
                    </div>
                  )}
                </div>

                {/* Details */}
                <div className="flex min-w-0 flex-1 flex-col justify-between">
                  <div>
                    <div className="flex items-center gap-1.5 flex-wrap">
                      <span className="rounded-full bg-fg px-2 py-0.5 text-[11px] font-semibold text-bg">
                        {b.level || "All"}
                      </span>
                      {b.category && (
                        <span className="rounded-full bg-raised px-2 py-0.5 text-[11px] text-muted">
                          {b.category}
                        </span>
                      )}
                    </div>

                    <h3 className="mt-2 line-clamp-2 font-display text-base font-semibold leading-snug">
                      {b.title}
                    </h3>
                    <p className="text-xs text-muted truncate">{b.author}</p>
                    <p className="mt-2 line-clamp-2 text-xs text-muted">{b.blurb}</p>
                    {b.blurb_hi && <p className="mt-1 line-clamp-1 text-xs text-accent">{b.blurb_hi}</p>}
                  </div>

                  <div className="mt-3 flex items-center justify-between border-t border-line/60 pt-2 text-xs">
                    <span className="text-muted">{b.pages} pages</span>
                    <div className="flex items-center gap-1.5">
                      {b.file_url && (
                        <a
                          href={b.file_url}
                          target="_blank"
                          rel="noopener noreferrer"
                          title="Open / Download PDF"
                          className="grid h-7 w-7 place-items-center rounded-lg border border-line text-muted transition hover:border-accent hover:text-accent"
                        >
                          <FileText className="h-3.5 w-3.5" />
                        </a>
                      )}
                      <button
                        onClick={() => handleEdit(b)}
                        title="Edit Book"
                        className="grid h-7 w-7 place-items-center rounded-lg border border-line text-muted transition hover:border-accent hover:text-accent"
                      >
                        <Pencil className="h-3.5 w-3.5" />
                      </button>
                      <button
                        onClick={() => handleDelete(b.slug, b.title)}
                        title="Delete Book"
                        className="grid h-7 w-7 place-items-center rounded-lg border border-line text-muted transition hover:border-danger hover:text-danger"
                      >
                        <Trash2 className="h-3.5 w-3.5" />
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </Card>
          ))}
        </ul>
      )}
    </div>
  );
}
