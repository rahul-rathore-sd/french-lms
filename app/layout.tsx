import type { Metadata } from "next";
import { ClerkProvider } from "@clerk/nextjs";
import "./globals.css";

export const metadata: Metadata = {
  title: "Académie de Français | Classical Immersion & CEFR Sanctuary",
  description:
    "Classical French Immersion, CEFR Syllabi (A1-C2), Multilingual Lexicon Sanctuary with Hindi translations, and Autonomous Hermes Ingestion.",
  icons: {
    icon: "data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🍁</text></svg>",
  },
};

const publishableKey =
  process.env.NEXT_PUBLIC_CLERK_PUBLISHABLE_KEY ||
  "pk_test_Y2xlcmsuYWNhZGVtaWUuZnIk";

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <ClerkProvider publishableKey={publishableKey}>
      <html lang="fr" className="dark" suppressHydrationWarning>
        <body className="bg-slate-50 text-slate-900 dark:bg-[#090d16] dark:text-slate-100 antialiased selection:bg-[#D52B1E] selection:text-white transition-colors duration-300">
          {children}
        </body>
      </html>
    </ClerkProvider>
  );
}
