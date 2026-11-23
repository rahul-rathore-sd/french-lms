"use client";

import { useEffect, useState } from "react";
import { Moon, Sun } from "lucide-react";

type Theme = "light" | "dark";
const THEME_COLOR: Record<Theme, string> = { dark: "#111219", light: "#f8f6f1" };

/** Dark is the default (kind to the eyes). The visitor can switch to light; the choice is remembered. */
export default function ThemeToggle() {
  const [theme, setTheme] = useState<Theme>("dark");

  useEffect(() => {
    setTheme(document.documentElement.dataset.theme === "light" ? "light" : "dark");
  }, []);

  const choose = (next: Theme) => {
    if (next === theme) return;
    const root = document.documentElement;
    root.classList.add("theme-anim");
    root.dataset.theme = next;
    setTheme(next);
    document.querySelector('meta[name="theme-color"]')?.setAttribute("content", THEME_COLOR[next]);
    window.setTimeout(() => root.classList.remove("theme-anim"), 450);
    try {
      localStorage.setItem("academie.theme", next);
    } catch {
      /* ignore */
    }
  };

  const isDark = theme === "dark";

  return (
    <div
      role="group"
      aria-label="Colour theme"
      className="relative flex h-9 items-center rounded-full border border-line bg-raised/60 p-0.5"
    >
      {/* sliding thumb */}
      <span
        aria-hidden
        className={`absolute top-0.5 h-8 w-8 rounded-full bg-accent shadow-glow transition-transform duration-300 ease-out ${isDark ? "translate-x-0" : "translate-x-8"}`}
      />
      <button
        type="button"
        onClick={() => choose("dark")}
        aria-pressed={isDark}
        aria-label="Dark mode"
        title="Dark mode (easy on the eyes)"
        className={`relative z-10 grid h-8 w-8 place-items-center rounded-full transition-colors ${isDark ? "text-accent-fg" : "text-muted hover:text-fg"}`}
      >
        <Moon className="h-4 w-4" />
      </button>
      <button
        type="button"
        onClick={() => choose("light")}
        aria-pressed={!isDark}
        aria-label="Light mode"
        title="Light mode"
        className={`relative z-10 grid h-8 w-8 place-items-center rounded-full transition-colors ${!isDark ? "text-accent-fg" : "text-muted hover:text-fg"}`}
      >
        <Sun className="h-4 w-4" />
      </button>
    </div>
  );
}
