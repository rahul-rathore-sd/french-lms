"use client";

import { useState } from "react";
import Link from "next/link";
import { useUser, UserButton } from "@clerk/nextjs";
import { initialCourses, initialVocabulary } from "@/lib/data";
import {
  Volume2,
  BookOpen,
  Flame,
  Award,
  Globe2,
  ChevronRight,
  Sparkles,
  ShieldCheck,
  Moon,
  Sun,
  Layers,
  ArrowRight
} from "lucide-react";

export default function HomePage() {
  const { isSignedIn, user } = useUser();
  const [activeSpeech, setActiveSpeech] = useState<string | null>(null);
  const [searchQuery, setSearchQuery] = useState("");
  const [selectedCefr, setSelectedCefr] = useState<string>("ALL");
  const [isDark, setIsDark] = useState(true);

  const toggleTheme = () => {
    setIsDark(!isDark);
    if (typeof document !== "undefined") {
      document.documentElement.classList.toggle("dark");
    }
  };

  const speakFrench = (text: string) => {
    if (typeof window !== "undefined" && "speechSynthesis" in window) {
      window.speechSynthesis.cancel();
      const utterance = new SpeechSynthesisUtterance(text);
      utterance.lang = "fr-FR";
      utterance.rate = 0.85;

      const voices = window.speechSynthesis.getVoices();
      const frenchVoice = voices.find(
        (v) => v.lang.includes("fr-FR") || v.lang.includes("fr-CA") || v.lang.includes("fr")
      );
      if (frenchVoice) {
        utterance.voice = frenchVoice;
      }

      setActiveSpeech(text);
      utterance.onend = () => setActiveSpeech(null);
      utterance.onerror = () => setActiveSpeech(null);

      window.speechSynthesis.speak(utterance);
    }
  };

  const filteredWords = initialVocabulary.filter((word) => {
    const matchesSearch =
      word.french_word.toLowerCase().includes(searchQuery.toLowerCase()) ||
      word.meaning_en.toLowerCase().includes(searchQuery.toLowerCase()) ||
      word.meaning_hi.includes(searchQuery) ||
      word.use_case.toLowerCase().includes(searchQuery.toLowerCase());
    const matchesCefr = selectedCefr === "ALL" || word.cefr_level === selectedCefr;
    return matchesSearch && matchesCefr;
  });

  return (
    <div className="min-h-screen bg-slate-50 dark:bg-[#090d16] text-slate-800 dark:text-slate-100 transition-colors duration-300">
      {/* Top Canadian Heritage Bar */}
      <div className="bg-[#D52B1E] text-white text-xs font-medium py-1.5 px-4 text-center tracking-wide flex items-center justify-center gap-2">
        <span className="inline-block w-4 h-2.5 bg-white border border-red-700 relative overflow-hidden">
          <span className="absolute inset-x-1 inset-y-0 bg-[#D52B1E] flex items-center justify-center text-[6px]">🍁</span>
        </span>
        <span>Académie de Français &bull; Sanctuaire d&apos;Immersion Classique CEFR &bull; Ottawa &bull; Paris</span>
      </div>

      {/* Main Navigation */}
      <header className="sticky top-0 z-50 backdrop-blur-md bg-white/80 dark:bg-[#090d16]/80 border-b border-slate-200 dark:border-slate-800">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
          <Link href="/" className="flex items-center gap-3 group">
            <div className="w-10 h-10 rounded-xl bg-[#D52B1E] flex items-center justify-center text-white text-xl shadow-md shadow-red-500/20 group-hover:scale-105 transition-transform">
              🍁
            </div>
            <div>
              <span className="font-serif text-lg font-bold tracking-tight text-slate-900 dark:text-white block">
                Académie de Français
              </span>
              <span className="text-[10px] tracking-widest uppercase font-semibold text-[#D52B1E]">
                LMS & Lexique Sanctuary
              </span>
            </div>
          </Link>

          <nav className="hidden md:flex items-center gap-6 text-sm font-medium text-slate-600 dark:text-slate-300">
            <a href="#lexicon" className="hover:text-[#D52B1E] transition-colors">
              Lexique Classique
            </a>
            <a href="#syllabi" className="hover:text-[#D52B1E] transition-colors">
              Niveaux CEFR (A1-C2)
            </a>
            <Link href="/admin" className="hover:text-[#D52B1E] transition-colors flex items-center gap-1">
              <ShieldCheck className="w-4 h-4 text-[#D52B1E]" />
              Console Faculté
            </Link>
          </nav>

          <div className="flex items-center gap-3">
            <button
              onClick={toggleTheme}
              className="p-2 rounded-lg text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
              title="Changer de thème"
            >
              {isDark ? <Sun className="w-4 h-4" /> : <Moon className="w-4 h-4" />}
            </button>

            {isSignedIn ? (
              <div className="flex items-center gap-3">
                <Link
                  href="/dashboard"
                  className="bg-[#D52B1E] hover:bg-[#B52419] text-white text-sm font-semibold py-2 px-4 rounded-xl shadow-md shadow-red-500/20 transition-all flex items-center gap-1.5"
                >
                  <BookOpen className="w-4 h-4" />
                  Sanctuaire
                </Link>
                <UserButton afterSignOutUrl="/" />
              </div>
            ) : (
              <div className="flex items-center gap-2">
                <Link
                  href="/sign-in"
                  className="text-sm font-medium text-slate-700 dark:text-slate-200 hover:text-[#D52B1E] py-2 px-3 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                >
                  Connexion
                </Link>
                <Link
                  href="/sign-up"
                  className="bg-[#D52B1E] hover:bg-[#B52419] text-white text-sm font-semibold py-2 px-4 rounded-xl shadow-md shadow-red-500/20 transition-all flex items-center gap-1.5"
                >
                  Inscription
                  <ArrowRight className="w-3.5 h-3.5" />
                </Link>
              </div>
            )}
          </div>
        </div>
      </header>

      {/* Hero Section */}
      <section className="relative overflow-hidden py-16 lg:py-24 border-b border-slate-200 dark:border-slate-800 bg-gradient-to-b from-white via-slate-50 to-slate-100 dark:from-[#0b101c] dark:via-[#090d16] dark:to-[#070a12]">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-100 dark:bg-red-950/60 border border-red-300 dark:border-red-900/60 text-[#D52B1E] dark:text-red-400 text-xs font-semibold uppercase tracking-wider mb-6">
            <Sparkles className="w-3.5 h-3.5" />
            Nouveau &bull; Next.js 15 App Router &bull; Clerk Auth &bull; 0 XP Clutter
          </div>

          <h1 className="font-serif text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight text-slate-900 dark:text-white max-w-4xl mx-auto leading-tight">
            L&apos;Immersion en Français Classique & le Sanctuaire CEFR
          </h1>

          <p className="mt-6 text-base sm:text-lg text-slate-600 dark:text-slate-300 max-w-2xl mx-auto leading-relaxed">
            Une méthodologie académique pure du niveau débutant A1 à la haute maîtrise C2.
            Découvrez nos modules littéraires, notre synthèse vocale native et notre lexique trilingue{" "}
            <span className="font-semibold text-slate-900 dark:text-white">Français &bull; English &bull; हिन्दी (Devanagari)</span>.
          </p>

          <div className="mt-8 flex flex-wrap justify-center gap-4">
            <Link
              href={isSignedIn ? "/dashboard" : "/sign-up"}
              className="bg-[#D52B1E] hover:bg-[#B52419] text-white text-base font-semibold py-3 px-8 rounded-xl shadow-lg shadow-red-500/25 transition-all flex items-center gap-2 hover:scale-[1.02]"
            >
              <BookOpen className="w-5 h-5" />
              {isSignedIn ? "Ouvrir mon Sanctuaire" : "Commencer Mon Immersion"}
            </Link>
            <a
              href="#lexicon"
              className="bg-white dark:bg-slate-800/80 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-800 dark:text-white border border-slate-300 dark:border-slate-700 text-base font-medium py-3 px-8 rounded-xl transition-all flex items-center gap-2"
            >
              <Volume2 className="w-5 h-5 text-[#D52B1E]" />
              Écouter le Lexique
            </a>
          </div>

          {/* Real Canadian Flag & Streak Indicator Strip */}
          <div className="mt-14 max-w-3xl mx-auto bg-white/70 dark:bg-slate-900/70 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 sm:p-6 backdrop-blur-sm shadow-xl flex flex-col sm:flex-row items-center justify-between gap-6">
            <div className="flex items-center gap-4">
              <div className="w-12 h-8 rounded-md bg-[#D52B1E] flex items-center justify-center text-white text-lg font-bold shadow-inner border border-red-700 relative overflow-hidden">
                <span className="text-xl">🍁</span>
              </div>
              <div className="text-left">
                <div className="text-xs uppercase font-bold tracking-wider text-slate-500 dark:text-slate-400">
                  Norme Académique Canadienne
                </div>
                <div className="text-sm font-semibold text-slate-900 dark:text-white">
                  Bilinguisme Fédéral &bull; Ottawa & Paris
                </div>
              </div>
            </div>

            <div className="h-8 w-px bg-slate-200 dark:bg-slate-800 hidden sm:block" />

            <div className="flex items-center gap-4">
              <div className="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-500">
                <Flame className="w-5 h-5 animate-pulse" />
              </div>
              <div className="text-left">
                <div className="text-xs uppercase font-bold tracking-wider text-slate-500 dark:text-slate-400">
                  Indicateur d&apos;Assiduité (Streak)
                </div>
                <div className="text-sm font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                  <span>Cycle 7-Jours Actif</span>
                  <span className="text-xs bg-emerald-500/10 text-emerald-500 font-bold px-2 py-0.5 rounded-full border border-emerald-500/20">
                    Sans XP Fictif
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Classical Immersion Lexicon Sanctuary */}
      <section id="lexicon" className="py-16 sm:py-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
          <div>
            <div className="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-[#D52B1E] mb-2">
              <Globe2 className="w-4 h-4" />
              Sanctuaire Philologique Trilingue
            </div>
            <h2 className="font-serif text-3xl sm:text-4xl font-bold text-slate-900 dark:text-white">
              Vocabulaire Classique avec Prononciation & Traduction हिन्दी
            </h2>
            <p className="mt-2 text-slate-600 dark:text-slate-400 text-sm max-w-xl">
              Chaque mot intègre sa phonétique IPA, son audio de synthèse vocale native, sa signification en anglais, son sens en{" "}
              <span className="font-semibold text-slate-900 dark:text-white">हिन्दी (Devanagari)</span> et un exemple d&apos;usage contextuel.
            </p>
          </div>

          {/* Search & CEFR filter */}
          <div className="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <input
              type="text"
              placeholder="Rechercher (Français, हिन्दी, English)..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#D52B1E] w-full sm:w-64"
            />
            <select
              value={selectedCefr}
              onChange={(e) => setSelectedCefr(e.target.value)}
              className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#D52B1E]"
            >
              <option value="ALL">Tous les Niveaux CEFR</option>
              <option value="B1">B1 Intermédiaire</option>
              <option value="B2">B2 Avancé</option>
              <option value="C1">C1 Autonome</option>
              <option value="C2">C2 Maîtrise</option>
            </select>
          </div>
        </div>

        {/* Vocabulary Cards Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {filteredWords.map((word) => (
            <div
              key={word.id}
              className="bg-white dark:bg-slate-900/90 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-md hover:shadow-xl hover:border-[#D52B1E]/40 transition-all group flex flex-col justify-between"
            >
              <div>
                {/* Header: Word + IPA + Audio + CEFR Badge */}
                <div className="flex items-start justify-between gap-3 mb-4">
                  <div>
                    <div className="flex items-center gap-2">
                      <h3 className="font-serif text-2xl font-bold text-slate-900 dark:text-white group-hover:text-[#D52B1E] transition-colors">
                        {word.french_word}
                      </h3>
                      <button
                        onClick={() => speakFrench(word.french_word)}
                        className={`p-1.5 rounded-lg border transition-all ${
                          activeSpeech === word.french_word
                            ? "bg-[#D52B1E] text-white border-[#D52B1E] animate-pulse"
                            : "bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-[#D52B1E] hover:text-white hover:border-[#D52B1E]"
                        }`}
                        title="Écouter la prononciation française"
                      >
                        <Volume2 className="w-4 h-4" />
                      </button>
                    </div>
                    <div className="text-xs font-mono text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-2">
                      <span>{word.ipa_phonetic}</span>
                      <span>&bull;</span>
                      <span className="italic">{word.part_of_speech}</span>
                    </div>
                  </div>

                  <span className="px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wider bg-red-100 dark:bg-red-950/80 text-[#D52B1E] border border-red-200 dark:border-red-900/60">
                    {word.cefr_level}
                  </span>
                </div>

                {/* Meanings */}
                <div className="space-y-2.5 mb-5 pt-3 border-t border-slate-100 dark:border-slate-800/80">
                  <div>
                    <span className="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">
                      English Meaning
                    </span>
                    <p className="text-sm font-medium text-slate-800 dark:text-slate-200">
                      {word.meaning_en}
                    </p>
                  </div>

                  {/* Hindi Meaning in Devanagari */}
                  <div className="bg-amber-500/5 dark:bg-amber-500/10 border border-amber-500/20 rounded-xl p-2.5">
                    <span className="text-[10px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400 block mb-0.5">
                      हिन्दी अर्थ (Hindi Translation)
                    </span>
                    <p className="text-sm font-semibold text-slate-900 dark:text-amber-100">
                      {word.meaning_hi}
                    </p>
                  </div>
                </div>

                {/* Practical Use Case */}
                <div className="bg-slate-50 dark:bg-slate-850/60 border border-slate-200 dark:border-slate-800 rounded-xl p-3 mb-4">
                  <div className="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">
                    <Sparkles className="w-3 h-3 text-[#D52B1E]" />
                    <span>Usage Contextuel & Pratique</span>
                  </div>
                  <p className="text-xs text-slate-700 dark:text-slate-300 italic">
                    &ldquo;{word.use_case}&rdquo;
                  </p>
                </div>

                {/* Example sentence */}
                <div className="text-xs text-slate-600 dark:text-slate-400">
                  <span className="font-semibold text-slate-800 dark:text-slate-200">Exemple : </span>
                  <span>{word.example_sentence}</span>
                </div>
              </div>

              {/* Card Footer Tag */}
              <div className="mt-5 pt-3 border-t border-slate-100 dark:border-slate-800 text-[11px] text-slate-400 flex items-center justify-between">
                <span>{word.category}</span>
                <span className="text-[#D52B1E] font-medium flex items-center gap-1">
                  Immersion 🍁
                </span>
              </div>
            </div>
          ))}
        </div>
      </section>

      {/* CEFR Syllabi Overview Section */}
      <section id="syllabi" className="py-16 bg-white dark:bg-[#070a12] border-t border-slate-200 dark:border-slate-800">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center max-w-3xl mx-auto mb-16">
            <span className="text-xs font-bold uppercase tracking-widest text-[#D52B1E] block mb-2">
              Cadre Européen Commun de Référence
            </span>
            <h2 className="font-serif text-3xl sm:text-4xl font-bold text-slate-900 dark:text-white">
              Syllabi Officiels du Niveau A1 au C2
            </h2>
            <p className="mt-3 text-slate-600 dark:text-slate-400 text-sm">
              Chaque cours est structuré avec des leçons bilingues immersives, des exercices acoustiques interactifs et un suivi d&apos;assiduité rigoureux.
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {initialCourses.map((course) => (
              <div
                key={course.id}
                className="bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 hover:border-[#D52B1E]/50 transition-all flex flex-col justify-between"
              >
                <div>
                  <div className="flex items-center justify-between mb-4">
                    <span className="px-3 py-1 rounded-xl text-sm font-bold bg-[#D52B1E] text-white">
                      {course.cefr_level}
                    </span>
                    <span className="text-xs font-medium text-slate-500 dark:text-slate-400">
                      {course.lessons.length} Leçons Immersion
                    </span>
                  </div>
                  <h3 className="font-serif text-xl font-bold text-slate-900 dark:text-white mb-2">
                    {course.title}
                  </h3>
                  <p className="text-xs text-slate-600 dark:text-slate-400 leading-relaxed mb-4">
                    {course.description}
                  </p>
                  <ul className="space-y-2 mb-6">
                    {course.lessons.slice(0, 3).map((l, i) => (
                      <li key={i} className="text-xs text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <span className="w-1.5 h-1.5 rounded-full bg-[#D52B1E]" />
                        <span className="font-medium">{l.title}</span>
                      </li>
                    ))}
                  </ul>
                </div>

                <Link
                  href={isSignedIn ? "/dashboard" : "/sign-up"}
                  className="w-full text-center text-xs font-semibold py-2.5 px-4 rounded-xl border border-slate-300 dark:border-slate-700 hover:bg-[#D52B1E] hover:text-white hover:border-[#D52B1E] transition-all flex items-center justify-center gap-1.5"
                >
                  Étudier ce Niveau
                  <ChevronRight className="w-3.5 h-3.5" />
                </Link>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Autonomous Hermes API Section */}
      <section className="py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 border-t border-slate-200 dark:border-slate-800">
        <div className="bg-slate-900 text-white rounded-3xl p-8 sm:p-12 relative overflow-hidden shadow-2xl border border-slate-800">
          <div className="relative z-10 max-w-3xl">
            <div className="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-emerald-400 mb-3">
              <span className="w-2 h-2 rounded-full bg-emerald-400 animate-ping" />
              Hermes Autonomous Agent Ingestion API Active
            </div>
            <h3 className="font-serif text-2xl sm:text-3xl font-bold mb-3">
              API REST d&apos;Ingestion Quotidienne de Vocabulaire
            </h3>
            <p className="text-slate-300 text-sm leading-relaxed mb-6">
              L&apos;agent autonome Hermes synchronise chaque jour de nouveaux termes classiques avec leurs phonétiques IPA, leurs sens en anglais et हिन्दी (Devanagari), ainsi que leurs cas d&apos;usage pratiques.
            </p>
            <div className="bg-black/60 rounded-xl p-4 font-mono text-xs text-slate-300 border border-slate-800 overflow-x-auto">
              <p className="text-emerald-400"># Ingestion de nouveau mot via Hermes</p>
              <p>POST /api/v1/vocabulary</p>
              <p>Authorization: Bearer &lt;HERMES_API_KEY&gt;</p>
              <p>Content-Type: application/json</p>
              <p className="text-slate-400 mt-2">
                &#123; &quot;french_word&quot;: &quot;...&quot;, &quot;ipa_phonetic&quot;: &quot;...&quot;, &quot;meaning_en&quot;: &quot;...&quot;, &quot;meaning_hi&quot;: &quot;...&quot;, &quot;use_case&quot;: &quot;...&quot; &#125;
              </p>
            </div>
          </div>
        </div>
      </section>

      {/* Footer */}
      <footer className="border-t border-slate-200 dark:border-slate-800 py-10 bg-slate-100 dark:bg-[#06080e] text-xs text-slate-500">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
          <div className="flex items-center gap-3">
            <span className="text-lg">🍁</span>
            <span>Académie de Français &bull; Sanctuaire d&apos;Immersion & Philologie &bull; 2026</span>
          </div>
          <div className="flex items-center gap-6">
            <Link href="/sign-in" className="hover:text-slate-900 dark:hover:text-white transition-colors">
              Connexion Savant
            </Link>
            <Link href="/admin" className="hover:text-slate-900 dark:hover:text-white transition-colors">
              Console Faculté
            </Link>
            <a href="/api/v1/status" className="hover:text-slate-900 dark:hover:text-white transition-colors">
              Statut API
            </a>
          </div>
        </div>
      </footer>
    </div>
  );
}
