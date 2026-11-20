import Link from "next/link";
import type { ReactNode } from "react";
import ThemeToggle from "./ThemeToggle";
import { Logo } from "./SiteHeader";

export default function AuthShell({ children, notice }: { children: ReactNode; notice?: ReactNode }) {
  return (
    <div className="ambient flex min-h-screen flex-col">
      <div className="mx-auto flex w-full max-w-6xl items-center justify-between px-4 py-5 sm:px-6">
        <Logo />
        <ThemeToggle />
      </div>
      <main className="mx-auto flex w-full max-w-md flex-1 flex-col items-center justify-center gap-5 px-4 pb-16">
        {notice}
        {children}
        <p className="text-xs text-muted">
          <Link href="/" className="hover:text-fg">← Back to home</Link>
        </p>
      </main>
    </div>
  );
}
