import type { Metadata, Viewport } from "next";
import { Fraunces, Inter } from "next/font/google";
import "./globals.css";

const sans = Inter({ subsets: ["latin", "latin-ext"], variable: "--font-sans", display: "swap" });
const display = Fraunces({
  subsets: ["latin", "latin-ext"],
  variable: "--font-display",
  display: "swap",
  style: ["normal", "italic"],
  axes: ["SOFT", "opsz"],
});

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
  // Dark is the default theme, so the browser chrome (mobile address bar) is dark too.
  // ThemeToggle updates this tag if the visitor chooses light.
  themeColor: "#111219",
  colorScheme: "dark light",
};

// Dark by default on every device, whatever the system setting is. Only an explicit choice of
// "light" (saved by the toggle) switches it off.
const themeScript = `(function(){var t='dark';try{if(localStorage.getItem('academie.theme')==='light')t='light'}catch(e){}document.documentElement.dataset.theme=t;if(t==='light'){var m=document.querySelector('meta[name="theme-color"]');if(m)m.setAttribute('content','#f8f6f1')}})();`;

export default function RootLayout({ children }: Readonly<{ children: React.ReactNode }>) {
  return (
      <html lang="en" data-theme="dark" className={`${sans.variable} ${display.variable}`} suppressHydrationWarning>
        <head>
          <script dangerouslySetInnerHTML={{ __html: themeScript }} />
        </head>
        <body>{children}</body>
      </html>
  );
}
