import type { Metadata, Viewport } from "next";
import { Fraunces, Inter } from "next/font/google";
import { ClerkProvider } from "@clerk/nextjs";
import "./globals.css";

const sans = Inter({ subsets: ["latin", "latin-ext"], variable: "--font-sans", display: "swap" });
const display = Fraunces({ subsets: ["latin", "latin-ext"], variable: "--font-display", display: "swap" });

export const metadata: Metadata = {
  title: {
    default: "Académie de Français — Learn French, beautifully",
    template: "%s · Académie de Français",
  },
  description:
    "A premium French learning platform: structured CEFR courses from A1 to C2, native-quality audio, spaced-repetition flashcards and a living lexicon with English and Hindi translations.",
  icons: {
    icon: "data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><rect width=%22100%22 height=%22100%22 rx=%2222%22 fill=%22%232c52d6%22/><text x=%2250%22 y=%2272%22 font-size=%2262%22 text-anchor=%22middle%22 fill=%22white%22 font-family=%22serif%22>É</text></svg>",
  },
};

export const viewport: Viewport = {
  viewportFit: "cover",
  themeColor: [
    { media: "(prefers-color-scheme: light)", color: "#faf9f6" },
    { media: "(prefers-color-scheme: dark)", color: "#090b12" },
  ],
};

// Placeholder only so the build can prerender without secrets; real key comes from env.
const publishableKey =
  process.env.NEXT_PUBLIC_CLERK_PUBLISHABLE_KEY || "pk_test_Y2xlcmsuYWNhZGVtaWUuZnIk";

const themeScript = `(function(){try{var t=localStorage.getItem('academie.theme');if(!t){t=window.matchMedia('(prefers-color-scheme: light)').matches?'light':'dark'}document.documentElement.dataset.theme=t}catch(e){document.documentElement.dataset.theme='dark'}})();`;

export default function RootLayout({ children }: Readonly<{ children: React.ReactNode }>) {
  return (
    <ClerkProvider publishableKey={publishableKey}>
      <html lang="en" data-theme="dark" className={`${sans.variable} ${display.variable}`} suppressHydrationWarning>
        <head>
          <script dangerouslySetInnerHTML={{ __html: themeScript }} />
        </head>
        <body>{children}</body>
      </html>
    </ClerkProvider>
  );
}
