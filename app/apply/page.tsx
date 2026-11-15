"use client";

import { useState } from "react";
import Link from "next/link";
import {
  ShieldCheck,
  CheckCircle,
  Clock,
  Sparkles,
  ArrowRight,
  BookOpen,
  GraduationCap,
  Globe2,
  Lock
} from "lucide-react";

export default function ApplyPage() {
  const [formData, setFormData] = useState({
    full_name: "",
    email: "",
    target_cefr: "B1",
    academic_background: "",
    motivation: "",
  });

  const [isSubmitting, setIsSubmitting] = useState(false);
  const [submittedApp, setSubmittedApp] = useState<{
    id: number;
    full_name: string;
    email: string;
    target_cefr: string;
    status: string;
  } | null>(null);
  const [errorMessage, setErrorMessage] = useState<string | null>(null);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsSubmitting(true);
    setErrorMessage(null);

    try {
      const res = await fetch("/api/v1/admissions", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: json_payload(),
      });

      const data = await res.json();
      if (!res.ok) {
        throw new Error(data.error || "Une erreur est survenue lors de la soumission.");
      }

      setSubmittedApp(data.data);
    } catch (err: unknown) {
      const msg = err instanceof Error ? err.message : "Erreur de connexion";
      setErrorMessage(msg);
    } finally {
      setIsSubmitting(false);
    }
  };

  const json_payload = () => {
    return JSON.stringify({
      full_name: formData.full_name,
      email: formData.email,
      target_cefr: formData.target_cefr,
      academic_background: formData.academic_background,
      motivation: formData.motivation,
    });
  };

  return (
    <div className="min-h-screen bg-slate-50 dark:bg-[#090d16] text-slate-900 dark:text-slate-100 transition-colors py-12 px-4 sm:px-6 lg:px-8">
      {/* Top Banner */}
      <div className="max-w-2xl mx-auto mb-8 text-center">
        <Link href="/" className="inline-flex items-center gap-3 group mb-4">
          <div className="w-12 h-12 rounded-xl bg-[#D52B1E] flex items-center justify-center text-white text-2xl shadow-lg shadow-red-500/20 group-hover:scale-105 transition-transform">
            🍁
          </div>
          <div className="text-left">
            <span className="font-serif text-2xl font-bold tracking-tight text-slate-900 dark:text-white block">
              Académie de Français
            </span>
            <span className="text-xs uppercase tracking-widest text-[#D52B1E] font-semibold">
              Sanctuaire d&apos;Excellence & Philologie
            </span>
          </div>
        </Link>

        <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-600 dark:text-amber-400 text-xs font-semibold uppercase tracking-wider mb-3">
          <Lock className="w-3.5 h-3.5" />
          Plateforme Restreinte &bull; Admission sur Candidature
        </div>

        <h1 className="font-serif text-3xl font-bold text-slate-900 dark:text-white mb-2">
          Demande d&apos;Admission au Sanctuaire
        </h1>
        <p className="text-xs sm:text-sm text-slate-600 dark:text-slate-400 max-w-lg mx-auto leading-relaxed">
          Afin de préserver un standard académique rigoureux et un encadrement philologique d&apos;élite,
          l&apos;accès aux cours CEFR et au lexique classique est validé individuellement par la Faculté.
        </p>
      </div>

      <div className="max-w-2xl mx-auto">
        {submittedApp ? (
          /* Submission Confirmation Card */
          <div className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-8 sm:p-10 shadow-2xl text-center animate-fade-in">
            <div className="w-16 h-16 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-500 mx-auto mb-5 text-2xl">
              ⏳
            </div>
            <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 mb-4">
              <Clock className="w-3.5 h-3.5" />
              Candidature N° {submittedApp.id} &bull; En Cours d&apos;Examen
            </div>

            <h2 className="font-serif text-2xl font-bold text-slate-900 dark:text-white mb-3">
              Dossier Transmis à la Faculté
            </h2>

            <p className="text-sm text-slate-600 dark:text-slate-300 leading-relaxed mb-6 max-w-md mx-auto">
              Merci, <span className="font-semibold text-slate-900 dark:text-white">{submittedApp.full_name}</span>.
              Votre demande d&apos;admission pour le niveau{" "}
              <span className="font-bold text-[#D52B1E]">{submittedApp.target_cefr}</span> est actuellement entre les mains de la direction académique.
            </p>

            <div className="bg-slate-50 dark:bg-slate-850/80 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 text-left text-xs space-y-2 mb-8">
              <div className="flex justify-between text-slate-500 dark:text-slate-400">
                <span>Courriel enregistré :</span>
                <span className="font-mono text-slate-800 dark:text-slate-200">{submittedApp.email}</span>
              </div>
              <div className="flex justify-between text-slate-500 dark:text-slate-400">
                <span>Délai d&apos;examen habituel :</span>
                <span className="font-semibold text-slate-800 dark:text-slate-200">24 à 48 heures</span>
              </div>
              <div className="flex justify-between text-slate-500 dark:text-slate-400">
                <span>Notification :</span>
                <span className="text-emerald-500 font-semibold">Par courriel automatique dès acceptation</span>
              </div>
            </div>

            <div className="flex flex-col sm:flex-row items-center justify-center gap-3">
              <Link
                href="/"
                className="w-full sm:w-auto px-6 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-semibold transition-all"
              >
                Retour à l&apos;Accueil
              </Link>
              <Link
                href="/sign-in"
                className="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-[#D52B1E] hover:bg-[#B52419] text-white text-xs font-bold transition-all shadow-md shadow-red-500/20"
              >
                Espace Connexion (Membres Admis)
              </Link>
            </div>
          </div>
        ) : (
          /* Application Form Card */
          <div className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-8 sm:p-10 shadow-2xl">
            {errorMessage && (
              <div className="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-600 dark:text-red-400 text-xs font-medium">
                {errorMessage}
              </div>
            )}

            <form onSubmit={handleSubmit} className="space-y-5">
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label className="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 block mb-1.5">
                    Nom Complet *
                  </label>
                  <input
                    type="text"
                    required
                    placeholder="ex: Jean-Baptiste Poquelin"
                    value={formData.full_name}
                    onChange={(e) => setFormData({ ...formData, full_name: e.target.value })}
                    className="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm focus:ring-2 focus:ring-[#D52B1E] outline-none transition-all"
                  />
                </div>

                <div>
                  <label className="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 block mb-1.5">
                    Adresse Courriel *
                  </label>
                  <input
                    type="email"
                    required
                    placeholder="votre.nom@institution.edu"
                    value={formData.email}
                    onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                    className="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm focus:ring-2 focus:ring-[#D52B1E] outline-none transition-all"
                  />
                </div>
              </div>

              <div>
                <label className="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 block mb-1.5">
                  Niveau CEFR Visé pour l&apos;Immersion *
                </label>
                <div className="grid grid-cols-3 sm:grid-cols-6 gap-2">
                  {(["A1", "A2", "B1", "B2", "C1", "C2"] as const).map((lvl) => (
                    <button
                      type="button"
                      key={lvl}
                      onClick={() => setFormData({ ...formData, target_cefr: lvl })}
                      className={`py-2 text-center rounded-xl font-bold text-xs transition-all border ${
                        formData.target_cefr === lvl
                          ? "bg-[#D52B1E] border-[#D52B1E] text-white shadow-md shadow-red-500/20"
                          : "border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:border-slate-400"
                      }`}
                    >
                      {lvl}
                    </button>
                  ))}
                </div>
              </div>

              <div>
                <label className="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 block mb-1.5">
                  Parcours Académique ou Professionnel
                </label>
                <input
                  type="text"
                  placeholder="ex: Étudiant en lettres, diplomate, traducteur, chercheur indépendant..."
                  value={formData.academic_background}
                  onChange={(e) => setFormData({ ...formData, academic_background: e.target.value })}
                  className="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm focus:ring-2 focus:ring-[#D52B1E] outline-none transition-all"
                />
              </div>

              <div>
                <label className="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 block mb-1.5">
                  Projet Philologique & Motivation *
                </label>
                <textarea
                  rows={4}
                  required
                  placeholder="Décrivez brièvement vos motivations pour intégrer le Sanctuaire (intérêt pour la littérature classique, la phonétique acoustique, les correspondances multilingues Français-Hindi)..."
                  value={formData.motivation}
                  onChange={(e) => setFormData({ ...formData, motivation: e.target.value })}
                  className="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm focus:ring-2 focus:ring-[#D52B1E] outline-none transition-all leading-relaxed"
                />
              </div>

              <div className="pt-2">
                <button
                  type="submit"
                  disabled={isSubmitting}
                  className="w-full bg-[#D52B1E] hover:bg-[#B52419] disabled:opacity-60 text-white font-bold py-3 px-6 rounded-xl shadow-lg shadow-red-500/20 transition-all flex items-center justify-center gap-2 hover:scale-[1.01]"
                >
                  {isSubmitting ? (
                    <span>Transmission en cours...</span>
                  ) : (
                    <>
                      <span>Soumettre ma Candidature d&apos;Admission</span>
                      <ArrowRight className="w-4 h-4" />
                    </>
                  )}
                </button>
              </div>

              <div className="pt-4 border-t border-slate-100 dark:border-slate-800 text-center text-xs text-slate-500">
                Vous possédez déjà une admission validée ?{" "}
                <Link href="/sign-in" className="text-[#D52B1E] hover:underline font-semibold">
                  Accéder à votre compte
                </Link>
              </div>
            </form>
          </div>
        )}
      </div>
    </div>
  );
}
