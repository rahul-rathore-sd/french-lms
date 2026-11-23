"use client";

import { useEffect, useState } from "react";
import Link from "next/link";
import { usePathname } from "next/navigation";
import { Flame, Menu, X } from "lucide-react";
import ThemeToggle from "./ThemeToggle";
import { liveStreak, useProgress } from "@/lib/progress";

type NavLink = { href: string; label: string; external?: boolean };
const appLinks: NavLink[] = [
  { href: "/dashboard", label: "Learn" },
  { href: "/practice", label: "Practice" },
  { href: "/library", label: "Library" },
  { href: "/books", label: "Books" },
  { href: "/dashboard?tab=lexicon", label: "Lexicon" },
  { href: "/blog/", label: "Blog", external: true },
];

const marketingLinks: NavLink[] = [
  { href: "#method", label: "Method" },
  { href: "#library", label: "Library" },
  { href: "/books", label: "Books" },
  { href: "#levels", label: "Levels" },
  { href: "#lexicon", label: "Lexicon" },
  { href: "/blog/", label: "Blog", external: true },
];

function NavAnchor({ external, href, children, ...rest }: { external?: boolean; href: string; children: React.ReactNode; className?: string; onClick?: () => void }) {
  return external ? (
    <a href={href} {...rest}>{children}</a>
  ) : (
    <Link href={href} {...rest}>{children}</Link>
  );
}

export function Logo() {
  return (
    <Link href="/" className="group flex min-w-0 items-center gap-2.5" aria-label="Académie de Français home">
      <span className="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-accent font-display text-lg font-semibold text-accent-fg shadow-glow transition group-hover:scale-105">
        É
      </span>
      <span className="truncate font-display text-base font-semibold tracking-tight sm:text-lg">
        <span className="hidden min-[400px]:inline">Académie de Français</span>
        <span className="min-[400px]:hidden">Académie</span>
      </span>
    </Link>
  );
}

export default function SiteHeader({ variant = "app" }: { variant?: "app" | "marketing" }) {
  const pathname = usePathname();
  const progress = useProgress();
  const streak = liveStreak(progress);
  const [open, setOpen] = useState(false);

  const links = variant === "marketing" ? marketingLinks : appLinks;

  // Close the mobile menu on navigation and lock body scroll while it is open.
  useEffect(() => setOpen(false), [pathname]);
  useEffect(() => {
    document.body.style.overflow = open ? "hidden" : "";
    return () => {
      document.body.style.overflow = "";
    };
  }, [open]);

  const isActive = (href: string) => !href.startsWith("#") && !href.includes("?") && pathname === href;

  return (
    <header className="sticky top-0 z-40 border-b border-line/70 bg-bg/85 backdrop-blur-xl">
      <div className="mx-auto flex h-16 max-w-6xl items-center justify-between gap-3 px-4 sm:px-6">
        <Logo />

        <nav className="hidden items-center gap-1 md:flex" aria-label="Main">
          {links.map((l) => (
            <NavAnchor
              key={l.href}
              href={l.href}
 external={l.external}
              className={`rounded-full px-3.5 py-2 text-sm transition ${isActive(l.href) ? "bg-raised text-fg" : "text-muted hover:text-fg"}`}
            >
              {l.label}
            </NavAnchor>
          ))}
        </nav>

        <div className="flex shrink-0 items-center gap-2">
          <span className="hidden items-center gap-1.5 rounded-full border border-line px-3 py-1.5 text-sm font-medium sm:flex" title="Day streak">
            <Flame className={`h-4 w-4 ${streak > 0 ? "text-orange-500" : "text-muted"}`} />
            {streak}
          </span>
          <ThemeToggle />
          <Link href="/apply" className="hidden rounded-full bg-fg px-4 py-2 text-sm font-medium text-bg transition hover:opacity-90 sm:block">
            Contact us
          </Link>
          <button
            onClick={() => setOpen((v) => !v)}
            aria-expanded={open}
            aria-controls="mobile-menu"
            aria-label={open ? "Close menu" : "Open menu"}
            className="grid h-9 w-9 place-items-center rounded-full border border-line text-muted transition hover:text-fg md:hidden"
          >
            {open ? <X className="h-4 w-4" /> : <Menu className="h-4 w-4" />}
          </button>
        </div>
      </div>

      {/* Mobile menu */}
      {open && (
        <div id="mobile-menu" className="fixed inset-x-0 bottom-0 top-16 z-30 overflow-y-auto bg-bg px-4 pb-10 pt-4 md:hidden">
          <nav className="flex flex-col gap-1" aria-label="Mobile">
            {links.map((l) => (
              <NavAnchor
                key={l.href}
                href={l.href}
 external={l.external}
                onClick={() => setOpen(false)}
                className={`rounded-2xl px-4 py-3.5 text-lg font-medium transition ${isActive(l.href) ? "bg-raised" : "hover:bg-raised"}`}
              >
                {l.label}
              </NavAnchor>
            ))}
          </nav>
          <div className="mt-6 grid gap-3">
            <div className="flex items-center justify-between rounded-2xl border border-line px-4 py-3 text-sm">
              <span className="text-muted">Day streak</span>
              <span className="flex items-center gap-1.5 font-semibold">
                <Flame className={`h-4 w-4 ${streak > 0 ? "text-orange-500" : "text-muted"}`} /> {streak}
              </span>
            </div>
            <Link href="/apply" className="grid h-12 place-items-center rounded-full bg-accent text-sm font-medium text-accent-fg">
              Contact us
            </Link>
          </div>
        </div>
      )}
    </header>
  );
}
