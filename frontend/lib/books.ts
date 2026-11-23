"use client";

import { useEffect, useState, useCallback } from "react";
import defaultBooks from "./library/books.json";

export interface BookItem {
  id?: number | string;
  slug: string;
  file?: string;
  file_url?: string;
  pages: number;
  cover: string | null;
  title: string;
  author: string;
  level: string;
  category?: string;
  blurb: string;
  blurb_hi?: string;
  created_at?: string;
  updated_at?: string;
}

const STORAGE_KEY = "academie_custom_books_v1";
const SYNC_EVENT = "academie_books_updated";

/** Get initial seed books */
export function getInitialBooks(): BookItem[] {
  return (defaultBooks as BookItem[]).map((b, idx) => ({
    ...b,
    id: idx + 1,
    category: b.category || (b.title.toLowerCase().includes("grammar") || b.title.toLowerCase().includes("grammaire") ? "Grammar" : "Literature"),
    file_url: b.file ? `/books/${encodeURIComponent(b.file)}` : undefined,
  }));
}

/** Read locally cached custom books from localStorage */
export function getStoredBooks(): BookItem[] {
  if (typeof window === "undefined") return [];
  try {
    const raw = localStorage.getItem(STORAGE_KEY);
    if (!raw) return [];
    return JSON.parse(raw) as BookItem[];
  } catch {
    return [];
  }
}

/** Save custom books to localStorage and notify all listeners */
export function setStoredBooks(books: BookItem[]) {
  if (typeof window === "undefined") return;
  try {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(books));
    window.dispatchEvent(new CustomEvent(SYNC_EVENT, { detail: books }));
  } catch (e) {
    console.error("Failed to save books to localStorage", e);
  }
}

/** Hook to fetch and sync books in real-time across admin and frontend */
export function useBooks() {
  const [books, setBooks] = useState<BookItem[]>(() => {
    const seed = getInitialBooks();
    const stored = getStoredBooks();
    // Merge seed and stored
    const map = new Map<string, BookItem>();
    seed.forEach((b) => map.set(b.slug, b));
    stored.forEach((b) => map.set(b.slug, b));
    return Array.from(map.values());
  });
  const [loading, setLoading] = useState(false);

  const reload = useCallback(() => {
    let cancelled = false;
    const seed = getInitialBooks();
    const stored = getStoredBooks();

    // Check API if reachable
    fetch("/api/v1/books")
      .then((r) => (r.ok ? r.json() : null))
      .then((json) => {
        if (cancelled) return;
        const map = new Map<string, BookItem>();
        seed.forEach((b) => map.set(b.slug, b));
        stored.forEach((b) => map.set(b.slug, b));

        if (json?.data && Array.isArray(json.data)) {
          (json.data as BookItem[]).forEach((b) => map.set(b.slug, b));
        }
        setBooks(Array.from(map.values()));
      })
      .catch(() => {
        if (cancelled) return;
        const map = new Map<string, BookItem>();
        seed.forEach((b) => map.set(b.slug, b));
        stored.forEach((b) => map.set(b.slug, b));
        setBooks(Array.from(map.values()));
      });

    return () => {
      cancelled = true;
    };
  }, []);

  useEffect(() => {
    const cancel = reload();

    const handleSync = () => {
      reload();
    };

    window.addEventListener(SYNC_EVENT, handleSync);
    window.addEventListener("storage", handleSync);
    return () => {
      cancel?.();
      window.removeEventListener(SYNC_EVENT, handleSync);
      window.removeEventListener("storage", handleSync);
    };
  }, [reload]);

  const saveBook = async (book: Omit<BookItem, "slug"> & { slug?: string }) => {
    const slug =
      book.slug ||
      book.title
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, "-")
        .replace(/(^-|-$)/g, "")
        .slice(0, 50) ||
      `book-${Date.now()}`;

    const newBook: BookItem = {
      ...book,
      slug,
      id: book.id || Date.now(),
      created_at: book.created_at || new Date().toISOString(),
      updated_at: new Date().toISOString(),
    };

    // Try posting to API
    try {
      await fetch("/api/v1/books", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(newBook),
      });
    } catch {
      // offline / client-only fallback
    }

    // Always update localStorage
    const stored = getStoredBooks();
    const filtered = stored.filter((b) => b.slug !== slug);
    filtered.unshift(newBook);
    setStoredBooks(filtered);

    // Update in-memory state
    setBooks((prev) => {
      const map = new Map<string, BookItem>();
      [newBook, ...prev].forEach((b) => map.set(b.slug, b));
      return Array.from(map.values());
    });

    return newBook;
  };

  const deleteBook = async (slug: string) => {
    try {
      await fetch(`/api/v1/books/${slug}`, { method: "DELETE" });
    } catch {
      // offline fallback
    }

    const stored = getStoredBooks().filter((b) => b.slug !== slug);
    setStoredBooks(stored);

    setBooks((prev) => prev.filter((b) => b.slug !== slug));
  };

  return { books, loading, saveBook, deleteBook, reload };
}
