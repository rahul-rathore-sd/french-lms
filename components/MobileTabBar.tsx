"use client";

import { Suspense } from "react";
import Link from "next/link";
import { usePathname, useSearchParams } from "next/navigation";
import { BookOpen, Layers, Library, Search } from "lucide-react";

const tabs = [
  { href: "/dashboard", label: "Learn", icon: BookOpen, match: (p: string, tab: string | null) => p === "/dashboard" && tab !== "lexicon" },
  { href: "/practice", label: "Practice", icon: Layers, match: (p: string) => p === "/practice" },
  { href: "/library", label: "Library", icon: Library, match: (p: string) => p.startsWith("/library") },
  { href: "/dashboard?tab=lexicon", label: "Lexicon", icon: Search, match: (p: string, tab: string | null) => p === "/dashboard" && tab === "lexicon" },
];

function Bar() {
  const pathname = usePathname();
  const tab = useSearchParams().get("tab");

  return (
    <>
      {/* Spacer so page content is never hidden behind the bar. */}
      <div aria-hidden className="h-24 md:hidden" />
      <nav
        aria-label="Primary"
        className="fixed inset-x-0 bottom-0 z-40 border-t border-line/80 bg-bg/90 backdrop-blur-xl md:hidden"
        style={{ paddingBottom: "env(safe-area-inset-bottom)" }}
      >
        <ul className="mx-auto grid max-w-md grid-cols-4">
          {tabs.map(({ href, label, icon: Icon, match }) => {
            const active = match(pathname, tab);
            return (
              <li key={href}>
                <Link
                  href={href}
                  aria-current={active ? "page" : undefined}
                  className={`flex flex-col items-center gap-1 px-2 py-2.5 text-[11px] font-medium transition ${active ? "text-accent" : "text-muted hover:text-fg"}`}
                >
                  <span className={`grid h-7 w-12 place-items-center rounded-full transition ${active ? "bg-accent/15" : ""}`}>
                    <Icon className="h-[18px] w-[18px]" />
                  </span>
                  {label}
                </Link>
              </li>
            );
          })}
        </ul>
      </nav>
    </>
  );
}

export default function MobileTabBar() {
  return (
    <Suspense fallback={null}>
      <Bar />
    </Suspense>
  );
}
