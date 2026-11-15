import { SignIn } from "@clerk/nextjs";
import Link from "next/link";

export default function SignInPage() {
  return (
    <div className="min-h-screen bg-slate-50 dark:bg-slate-950 flex flex-col justify-center items-center px-4 py-12 transition-colors">
      {/* Academy Crest Header */}
      <div className="mb-8 text-center">
        <Link href="/" className="inline-flex items-center gap-3 group">
          <div className="w-12 h-12 rounded-xl bg-[#D52B1E] flex items-center justify-center text-white text-2xl shadow-lg shadow-red-500/20 group-hover:scale-105 transition-transform">
            🍁
          </div>
          <div className="text-left">
            <span className="font-serif text-2xl font-bold tracking-tight text-slate-900 dark:text-white block">
              Académie de Français
            </span>
            <span className="text-xs uppercase tracking-widest text-[#D52B1E] font-semibold">
              Sanctuaire d&apos;Immersion CEFR
            </span>
          </div>
        </Link>
        <p className="mt-3 text-sm text-slate-600 dark:text-slate-400">
          Entrez vos identifiants pour reprendre votre parcours d&apos;étude.
        </p>
      </div>

      {/* Clerk SignIn Component Container */}
      <div className="w-full max-w-md flex justify-center">
        <SignIn
          appearance={{
            elements: {
              card: "bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xl rounded-2xl p-6 w-full",
              headerTitle: "font-serif text-xl font-bold text-slate-900 dark:text-white",
              headerSubtitle: "text-slate-600 dark:text-slate-400 text-sm",
              formButtonPrimary:
                "bg-[#D52B1E] hover:bg-[#B52419] text-white font-medium py-2.5 px-4 rounded-xl transition-all shadow-md shadow-red-500/20 text-sm",
              socialButtonsBlockButton:
                "border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-850 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-xl py-2 text-sm",
              formFieldLabel: "text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300",
              formFieldInput:
                "bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white focus:ring-2 focus:ring-[#D52B1E] text-sm",
              footerActionLink: "text-[#D52B1E] hover:text-[#B52419] font-medium transition-colors",
              identityPreviewText: "text-slate-700 dark:text-slate-300 font-medium",
              identityPreviewEditButton: "text-[#D52B1E] hover:text-[#B52419]",
            },
            variables: {
              colorPrimary: "#D52B1E",
            },
          }}
        />
      </div>

      <div className="mt-8 text-center text-xs text-slate-500 dark:text-slate-500">
        Secured by Clerk Authentication &bull; Académie de Français &bull; Canada
      </div>
    </div>
  );
}
