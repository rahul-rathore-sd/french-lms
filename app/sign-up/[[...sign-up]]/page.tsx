import { SignUp } from "@clerk/nextjs";
import Link from "next/link";
import { Lock, ArrowRight } from "lucide-react";

export default function SignUpPage() {
  return (
    <div className="min-h-screen bg-slate-50 dark:bg-slate-950 flex flex-col justify-center items-center px-4 py-12 transition-colors">
      {/* Academy Crest Header */}
      <div className="mb-6 text-center">
        <Link href="/" className="inline-flex items-center gap-3 group">
          <div className="w-12 h-12 rounded-xl bg-[#D52B1E] flex items-center justify-center text-white text-2xl shadow-lg shadow-red-500/20 group-hover:scale-105 transition-transform">
            🍁
          </div>
          <div className="text-left">
            <span className="font-serif text-2xl font-bold tracking-tight text-slate-900 dark:text-white block">
              Académie de Français
            </span>
            <span className="text-xs uppercase tracking-widest text-[#D52B1E] font-semibold">
              Inscription sur Invitation & Candidature
            </span>
          </div>
        </Link>
      </div>

      {/* Admission Notice Banner */}
      <div className="w-full max-w-md mb-6 bg-amber-500/10 border border-amber-500/25 rounded-2xl p-4 text-xs text-amber-700 dark:text-amber-300 flex items-start gap-3">
        <Lock className="w-4 h-4 mt-0.5 flex-shrink-0 text-amber-600 dark:text-amber-400" />
        <div>
          <span className="font-bold block mb-0.5">Accès Académique Restreint</span>
          <span>
            Les inscriptions sont réservées aux candidats dont le dossier a été préalablement validé par la Faculté.
          </span>
          <Link
            href="/apply"
            className="mt-2 inline-flex items-center gap-1 font-bold text-[#D52B1E] dark:text-red-400 hover:underline"
          >
            Déposer une demande d&apos;admission <ArrowRight className="w-3 h-3" />
          </Link>
        </div>
      </div>

      {/* Clerk SignUp Component Container */}
      <div className="w-full max-w-md flex justify-center">
        <SignUp
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
        Secured by Clerk Authentication &bull; Académie de Français &bull; Admission Contrôlée
      </div>
    </div>
  );
}
