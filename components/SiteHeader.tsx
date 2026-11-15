"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { SignedIn, SignedOut, UserButton } from "@clerk/nextjs";
import ThemeToggle from "./ThemeToggle";
import { liveStreak, useProgress } from "@/lib/progress";
import { Flame } from "lucide-react";

const links = [
  { href: "/dashboard", label: "Learn" },
  { href: "/practice", label: "Practice" },
  { href: "/dashboard?tab=lexicon", label: "Lexicon" },
];

export function Logo() {
  return (
    <Link href="/" className="group flex items-center gap-2.5" aria-label="Académie de Français home">
      <span className="grid h-9 w-9 place-items-center rounded-xl bg-accent font-display text-lg font-semibold text-accent-fg shadow-glow transition group-hover:scale-105">
        É
      </span>
      <span className="font-display text-lg font-semibold tracking-tight">Académie de Français</span>
    </Link>
  );
}

export default function SiteHeader({ variant = "app" }: { variant?: "app" | "marketing" }) {
  const pathname = usePathname();
  const progress = useProgress();
  const streak = liveStreak(progress);

  return (
    <header className="sticky top-0 z-40 border-b border-line/70 bg-bg/80 backdrop-blur-xl">
      <div className="mx-auto flex h-16 max-w-6xl items-center justify-between gap-4 px-4 sm:px-6">
        <Logo />

        <nav className="hidden items-center gap-1 md:flex" aria-label="Main">
          {variant === "marketing" ? (
            <>
              <a href="#method" className="rounded-full px-3.5 py-2 text-sm text-muted transition hover:text-fg">Method</a>
              <a href="#levels" className="rounded-full px-3.5 py-2 text-sm text-muted transition hover:text-fg">Levels</a>
              <a href="#lexicon" className="rounded-full px-3.5 py-2 text-sm text-muted transition hover:text-fg">Lexicon</a>
            </>
          ) : (
            links.map((l) => {
              const active = pathname === l.href.split("?")[0] && !l.href.includes("?");
              return (
                <Link
                  key={l.href}
                  href={l.href}
                  className={`rounded-full px-3.5 py-2 text-sm transition ${
                    active ? "bg-raised text-fg" : "text-muted hover:text-fg"
                  }`}
                >
                  {l.label}
                </Link>
              );
            })
          )}
        </nav>

        <div className="flex items-center gap-2.5">
          <SignedIn>
            <span
              className="hidden items-center gap-1.5 rounded-full border border-line px-3 py-1.5 text-sm font-medium sm:flex"
              title="Day streak"
            >
              <Flame className={`h-4 w-4 ${streak > 0 ? "text-orange-500" : "text-muted"}`} />
              {streak}
            </span>
          </SignedIn>
          <ThemeToggle />
          <SignedOut>
            <Link href="/sign-in" className="hidden rounded-full px-3.5 py-2 text-sm text-muted transition hover:text-fg sm:block">
              Sign in
            </Link>
            <Link
              href="/apply"
              className="rounded-full bg-fg px-4 py-2 text-sm font-medium text-bg transition hover:opacity-90"
            >
              Request access
            </Link>
          </SignedOut>
          <SignedIn>
            {variant === "marketing" && (
              <Link
                href="/dashboard"
                className="rounded-full bg-fg px-4 py-2 text-sm font-medium text-bg transition hover:opacity-90"
              >
                Open app
              </Link>
            )}
            <UserButton />
          </SignedIn>
        </div>
      </div>
    </header>
  );
}
