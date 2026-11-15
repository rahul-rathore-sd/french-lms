import Link from "next/link";
import type { ReactNode } from "react";
import ThemeToggle from "./ThemeToggle";
import { Logo } from "./SiteHeader";

export const clerkAppearance = {
  variables: { colorPrimary: "#4f6ef7", borderRadius: "0.8rem" },
  elements: {
    rootBox: "w-full",
    card: "bg-surface border border-line shadow-soft rounded-2xl w-full",
    headerTitle: "font-display text-fg",
    headerSubtitle: "text-muted",
    formButtonPrimary: "bg-accent text-accent-fg hover:brightness-110 shadow-none normal-case font-medium",
    formFieldLabel: "text-fg",
    formFieldInput: "bg-bg border-line text-fg",
    socialButtonsBlockButton: "border-line text-fg bg-surface hover:bg-raised",
    socialButtonsBlockButtonText: "text-fg",
    dividerLine: "bg-line",
    dividerText: "text-muted",
    footerActionText: "text-muted",
    footerActionLink: "text-accent hover:text-accent",
    identityPreviewText: "text-fg",
    identityPreviewEditButton: "text-accent",
    formFieldAction: "text-accent",
    footer: "bg-transparent",
  },
};

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
