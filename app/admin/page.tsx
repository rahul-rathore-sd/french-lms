"use client";

import { useState } from "react";
import Link from "next/link";
import { useUser, UserButton } from "@clerk/nextjs";
import {
  initialCourses,
  initialVocabulary,
  mockScholars,
  VocabularyWord,
  Course,
  Scholar
} from "@/lib/data";
import {
  ShieldCheck,
  BookOpen,
  Users,
  Globe2,
  Plus,
  Trash2,
  Edit2,
  ArrowLeft,
  Volume2,
  CheckCircle,
  Key,
  Flame,
  Search,
  Sparkles
} from "lucide-react";

export default function AdminConsolePage() {
  const { user } = useUser();
  const [activeTab, setActiveTab] = useState<"lexicon" | "courses" | "scholars" | "hermes">("lexicon");
  
  const [lexicon, setLexicon] = useState<VocabularyWord[]>(initialVocabulary);
  const [courses, setCourses] = useState<Course[]>(initialCourses);
  const [scholars, setScholars] = useState<Scholar[]>(mockScholars);

  const [searchTerm, setSearchTerm] = useState("");
  const [activeSpeech, setActiveSpeech] = useState<string | null>(null);

  // New Word Form State
  const [newWord, setNewWord] = useState({
    french_word: "",
    ipa_phonetic: "",
    part_of_speech: "nom",
    meaning_en: "",
    meaning_hi: "",
    use_case: "",
    example_sentence: "",
    category: "Littérature Classique",
    cefr_level: "B2",
  });

  const [isSuccessAlert, setIsSuccessAlert] = useState<string | null>(null);

  const speakFrench = (text: string) => {
    if (typeof window !== "undefined" && "speechSynthesis" in window) {
      window.speechSynthesis.cancel();
      const utterance = new SpeechSynthesisUtterance(text);
      utterance.lang = "fr-FR";
      utterance.rate = 0.85;
      setActiveSpeech(text);
      utterance.onend = () => setActiveSpeech(null);
      utterance.onerror = () => setActiveSpeech(null);
      window.speechSynthesis.speak(utterance);
    }
  };

  const handleAddLexicon = (e: React.FormEvent) => {
    e.preventDefault();
    if (!newWord.french_word || !newWord.meaning_en || !newWord.meaning_hi || !newWord.use_case) {
      alert("Tous les champs (Mot, Anglais, हिन्दी et Cas d'usage) sont requis.");
      return;
    }

    const created: VocabularyWord = {
      id: lexicon.length + 1,
      french_word: newWord.french_word,
      ipa_phonetic: newWord.ipa_phonetic || "/.../",
      part_of_speech: newWord.part_of_speech,
      meaning_en: newWord.meaning_en,
      meaning_hi: newWord.meaning_hi,
      use_case: newWord.use_case,
      example_sentence: newWord.example_sentence || newWord.french_word,
      category: newWord.category,
      cefr_level: newWord.cefr_level,
      notes: "Ajouté via Console d'Administration",
      created_at: new Date().toISOString(),
    };

    setLexicon([created, ...lexicon]);
    setIsSuccessAlert(`Le terme "${created.french_word}" a été ajouté au Sanctuaire avec succès.`);
    setTimeout(() => setIsSuccessAlert(null), 4000);

    setNewWord({
      french_word: "",
      ipa_phonetic: "",
      part_of_speech: "nom",
      meaning_en: "",
      meaning_hi: "",
      use_case: "",
      example_sentence: "",
      category: "Littérature Classique",
      cefr_level: "B2",
    });
  };

  const handleDeleteWord = (id: number) => {
    if (confirm("Supprimer ce terme du lexique officiel ?")) {
      setLexicon(lexicon.filter((w) => w.id !== id));
    }
  };

  const filteredLexicon = lexicon.filter((word) => {
    const q = searchTerm.toLowerCase();
    return (
      word.french_word.toLowerCase().includes(q) ||
      word.meaning_en.toLowerCase().includes(q) ||
      word.meaning_hi.includes(searchTerm) ||
      word.use_case.toLowerCase().includes(q)
    );
  });

  const currentUserEmail = user?.primaryEmailAddress?.emailAddress || "rrathore959910@gmail.com";

  return (
    <div className="min-h-screen bg-slate-50 dark:bg-[#090d16] text-slate-900 dark:text-slate-100 transition-colors">
      {/* Top Banner */}
      <div className="bg-[#D52B1E] text-white text-xs font-semibold py-1.5 px-4 flex items-center justify-between">
        <div className="flex items-center gap-2">
          <span>🍁 Console Faculté &bull; Administration Académie de Français</span>
        </div>
        <div className="text-[11px] font-mono">
          Compte Faculté : {currentUserEmail}
        </div>
      </div>

      {/* Main Admin Nav */}
      <header className="sticky top-0 z-40 backdrop-blur-md bg-white/85 dark:bg-[#090d16]/85 border-b border-slate-200 dark:border-slate-800">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
          <div className="flex items-center gap-4">
            <Link
              href="/dashboard"
              className="p-2 rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors text-slate-500 hover:text-slate-900 dark:hover:text-white"
            >
              <ArrowLeft className="w-4 h-4" />
            </Link>
            <div className="flex items-center gap-2.5">
              <ShieldCheck className="w-6 h-6 text-[#D52B1E]" />
              <span className="font-serif text-lg font-bold text-slate-900 dark:text-white">
                Direction Académique &bull; Gestion Globale
              </span>
            </div>
          </div>

          <div className="flex items-center gap-3">
            <UserButton afterSignOutUrl="/" />
          </div>
        </div>
      </header>

      {/* Main Container */}
      <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* Success Alert */}
        {isSuccessAlert && (
          <div className="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-700 dark:text-emerald-300 text-xs font-semibold flex items-center gap-2 animate-fade-in">
            <CheckCircle className="w-4 h-4 text-emerald-500" />
            <span>{isSuccessAlert}</span>
          </div>
        )}

        {/* Tab Switcher */}
        <div className="flex items-center gap-2 border-b border-slate-200 dark:border-slate-800 pb-4 mb-8 overflow-x-auto">
          <button
            onClick={() => setActiveTab("lexicon")}
            className={`py-2 px-4 rounded-xl text-xs font-bold transition-all flex items-center gap-2 whitespace-nowrap ${
              activeTab === "lexicon"
                ? "bg-[#D52B1E] text-white shadow-md shadow-red-500/20"
                : "bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white"
            }`}
          >
            <Globe2 className="w-4 h-4" />
            Gestion du Lexique ({lexicon.length})
          </button>

          <button
            onClick={() => setActiveTab("courses")}
            className={`py-2 px-4 rounded-xl text-xs font-bold transition-all flex items-center gap-2 whitespace-nowrap ${
              activeTab === "courses"
                ? "bg-[#D52B1E] text-white shadow-md shadow-red-500/20"
                : "bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white"
            }`}
          >
            <BookOpen className="w-4 h-4" />
            Cours & Syllabi ({courses.length})
          </button>

          <button
            onClick={() => setActiveTab("scholars")}
            className={`py-2 px-4 rounded-xl text-xs font-bold transition-all flex items-center gap-2 whitespace-nowrap ${
              activeTab === "scholars"
                ? "bg-[#D52B1E] text-white shadow-md shadow-red-500/20"
                : "bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white"
            }`}
          >
            <Users className="w-4 h-4" />
            Savants Inscrits ({scholars.length})
          </button>

          <button
            onClick={() => setActiveTab("hermes")}
            className={`py-2 px-4 rounded-xl text-xs font-bold transition-all flex items-center gap-2 whitespace-nowrap ${
              activeTab === "hermes"
                ? "bg-[#D52B1E] text-white shadow-md shadow-red-500/20"
                : "bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white"
            }`}
          >
            <Key className="w-4 h-4" />
            API Hermes & Tokens
          </button>
        </div>

        {/* Tab 1: Lexicon Management (Add, Edit, Delete, Hindi, Use Cases) */}
        {activeTab === "lexicon" && (
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
            {/* Add Term Form */}
            <div className="lg:col-span-5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
              <div className="flex items-center gap-2 mb-4">
                <Plus className="w-5 h-5 text-[#D52B1E]" />
                <h2 className="font-serif text-lg font-bold text-slate-900 dark:text-white">
                  Ajouter un Mot au Lexique
                </h2>
              </div>
              <p className="text-xs text-slate-500 dark:text-slate-400 mb-5">
                Chaque mot doit contenir sa phonétique, sa traduction en anglais, sa signification en हिन्दी et son cas d&apos;usage.
              </p>

              <form onSubmit={handleAddLexicon} className="space-y-4">
                <div className="grid grid-cols-2 gap-3">
                  <div>
                    <label className="text-[11px] font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 block mb-1">
                      Mot Français *
                    </label>
                    <input
                      type="text"
                      required
                      placeholder="ex: Sérendipité"
                      value={newWord.french_word}
                      onChange={(e) => setNewWord({ ...newWord, french_word: e.target.value })}
                      className="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-[#D52B1E] outline-none"
                    />
                  </div>
                  <div>
                    <label className="text-[11px] font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 block mb-1">
                      Phonétique IPA
                    </label>
                    <input
                      type="text"
                      placeholder="/se.ʁɑ̃.di.pi.te/"
                      value={newWord.ipa_phonetic}
                      onChange={(e) => setNewWord({ ...newWord, ipa_phonetic: e.target.value })}
                      className="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-[#D52B1E] outline-none"
                    />
                  </div>
                </div>

                <div>
                  <label className="text-[11px] font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 block mb-1">
                    Signification en Anglais (Meaning EN) *
                  </label>
                  <input
                    type="text"
                    required
                    placeholder="ex: Serendipity, fortunate coincidence"
                    value={newWord.meaning_en}
                    onChange={(e) => setNewWord({ ...newWord, meaning_en: e.target.value })}
                    className="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-[#D52B1E] outline-none"
                  />
                </div>

                <div className="bg-amber-500/5 dark:bg-amber-500/10 border border-amber-500/20 rounded-xl p-3">
                  <label className="text-[11px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400 block mb-1">
                    हिन्दी अर्थ (Devanagari Meaning) *
                  </label>
                  <input
                    type="text"
                    required
                    placeholder="ex: अप्रत्याशित सौभाग्य / सुखद संयोग"
                    value={newWord.meaning_hi}
                    onChange={(e) => setNewWord({ ...newWord, meaning_hi: e.target.value })}
                    className="w-full bg-white dark:bg-slate-800 border border-amber-300 dark:border-amber-700/60 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-[#D52B1E] outline-none font-medium"
                  />
                </div>

                <div>
                  <label className="text-[11px] font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 block mb-1">
                    Cas d&apos;Usage Pratique (Use Case Context) *
                  </label>
                  <textarea
                    rows={2}
                    required
                    placeholder="Circonstances exactes dans lesquelles employer ce mot..."
                    value={newWord.use_case}
                    onChange={(e) => setNewWord({ ...newWord, use_case: e.target.value })}
                    className="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-[#D52B1E] outline-none"
                  />
                </div>

                <div className="grid grid-cols-2 gap-3">
                  <div>
                    <label className="text-[11px] font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 block mb-1">
                      Niveau CEFR
                    </label>
                    <select
                      value={newWord.cefr_level}
                      onChange={(e) => setNewWord({ ...newWord, cefr_level: e.target.value })}
                      className="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-[#D52B1E] outline-none"
                    >
                      <option value="B1">B1</option>
                      <option value="B2">B2</option>
                      <option value="C1">C1</option>
                      <option value="C2">C2</option>
                    </select>
                  </div>
                  <div>
                    <label className="text-[11px] font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 block mb-1">
                      Nature Grammaticale
                    </label>
                    <select
                      value={newWord.part_of_speech}
                      onChange={(e) => setNewWord({ ...newWord, part_of_speech: e.target.value })}
                      className="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-[#D52B1E] outline-none"
                    >
                      <option value="nom féminin">nom féminin</option>
                      <option value="nom masculin">nom masculin</option>
                      <option value="adjectif">adjectif</option>
                      <option value="verbe">verbe</option>
                    </select>
                  </div>
                </div>

                <button
                  type="submit"
                  className="w-full py-2.5 px-4 rounded-xl text-xs font-bold bg-[#D52B1E] hover:bg-[#B52419] text-white shadow-md shadow-red-500/20 transition-all"
                >
                  Publier dans le Lexique Officiel
                </button>
              </form>
            </div>

            {/* Lexicon List Table */}
            <div className="lg:col-span-7 space-y-4">
              <div className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-sm flex items-center justify-between gap-4">
                <div className="relative flex-1">
                  <Search className="w-4 h-4 absolute left-3 top-2.5 text-slate-400" />
                  <input
                    type="text"
                    placeholder="Filtrer les termes..."
                    value={searchTerm}
                    onChange={(e) => setSearchTerm(e.target.value)}
                    className="w-full pl-9 pr-4 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs focus:ring-2 focus:ring-[#D52B1E] outline-none"
                  />
                </div>
                <span className="text-xs font-semibold text-slate-500">
                  {filteredLexicon.length} termes affichés
                </span>
              </div>

              <div className="space-y-3">
                {filteredLexicon.map((word) => (
                  <div
                    key={word.id}
                    className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-sm hover:border-[#D52B1E]/40 transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-4"
                  >
                    <div className="space-y-1.5 flex-1">
                      <div className="flex items-center gap-3">
                        <span className="font-serif text-lg font-bold text-slate-900 dark:text-white">
                          {word.french_word}
                        </span>
                        <span className="text-xs font-mono text-slate-400">{word.ipa_phonetic}</span>
                        <span className="px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 dark:bg-red-950 text-[#D52B1E]">
                          {word.cefr_level}
                        </span>
                      </div>

                      <div className="text-xs text-slate-600 dark:text-slate-300">
                        <span className="font-semibold text-slate-700 dark:text-slate-200">EN: </span>
                        {word.meaning_en}
                      </div>

                      <div className="text-xs text-amber-700 dark:text-amber-300 font-medium">
                        <span className="font-semibold">हिन्दी: </span>
                        {word.meaning_hi}
                      </div>

                      <div className="text-[11px] text-slate-500 dark:text-slate-400 italic">
                        <span className="font-semibold not-italic">Usage: </span>
                        {word.use_case}
                      </div>
                    </div>

                    <div className="flex items-center gap-2 self-end sm:self-center">
                      <button
                        onClick={() => speakFrench(word.french_word)}
                        className="p-2 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-[#D52B1E] hover:text-white hover:border-[#D52B1E] transition-all text-slate-600 dark:text-slate-300"
                        title="Écouter"
                      >
                        <Volume2 className="w-3.5 h-3.5" />
                      </button>
                      <button
                        onClick={() => handleDeleteWord(word.id)}
                        className="p-2 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-red-600 hover:text-white hover:border-red-600 transition-all text-slate-600 dark:text-slate-300"
                        title="Supprimer"
                      >
                        <Trash2 className="w-3.5 h-3.5" />
                      </button>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </div>
        )}

        {/* Tab 2: Courses & Syllabi */}
        {activeTab === "courses" && (
          <div className="space-y-6">
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {courses.map((course) => (
                <div
                  key={course.id}
                  className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm"
                >
                  <div className="flex items-center justify-between mb-3">
                    <span className="px-3 py-1 rounded-xl text-xs font-bold bg-[#D52B1E] text-white">
                      {course.cefr_level}
                    </span>
                    <span className="text-xs text-slate-400">{course.lessons.length} leçons</span>
                  </div>
                  <h3 className="font-serif text-lg font-bold text-slate-900 dark:text-white mb-2">
                    {course.title}
                  </h3>
                  <p className="text-xs text-slate-600 dark:text-slate-400 mb-4">{course.description}</p>

                  <div className="border-t border-slate-100 dark:border-slate-800 pt-3 space-y-2">
                    {course.lessons.map((lesson) => (
                      <div
                        key={lesson.id}
                        className="text-xs text-slate-700 dark:text-slate-300 flex items-center justify-between"
                      >
                        <span className="font-medium">
                          {lesson.order_num}. {lesson.title}
                        </span>
                        <span className="text-[10px] text-emerald-500 font-bold">Quiz Actif</span>
                      </div>
                    ))}
                  </div>
                </div>
              ))}
            </div>
          </div>
        )}

        {/* Tab 3: Scholars */}
        {activeTab === "scholars" && (
          <div className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
            <h3 className="font-serif text-lg font-bold text-slate-900 dark:text-white mb-4">
              Annuaire des Savants de l&apos;Académie
            </h3>
            <div className="overflow-x-auto">
              <table className="w-full text-xs text-left">
                <thead className="border-b border-slate-200 dark:border-slate-800 text-slate-400 uppercase tracking-wider">
                  <tr>
                    <th className="py-3 px-4">Savant</th>
                    <th className="py-3 px-4">Courriel</th>
                    <th className="py-3 px-4">Région</th>
                    <th className="py-3 px-4">Niveau CEFR</th>
                    <th className="py-3 px-4">Assiduité (Streak)</th>
                    <th className="py-3 px-4">Statut</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                  {scholars.map((s) => (
                    <tr key={s.id} className="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                      <td className="py-3 px-4 font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span>🍁</span>
                        <span>{s.name}</span>
                      </td>
                      <td className="py-3 px-4 font-mono">{s.email}</td>
                      <td className="py-3 px-4">{s.region}</td>
                      <td className="py-3 px-4">
                        <span className="px-2 py-0.5 rounded bg-red-100 dark:bg-red-950 text-[#D52B1E] font-bold">
                          {s.cefr_level}
                        </span>
                      </td>
                      <td className="py-3 px-4">
                        <span className="font-bold text-amber-500 flex items-center gap-1">
                          <Flame className="w-3.5 h-3.5" />
                          {s.streak_days} Jours
                        </span>
                      </td>
                      <td className="py-3 px-4">
                        <span className="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/10 text-emerald-500">
                          {s.role}
                        </span>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        )}

        {/* Tab 4: Hermes & Tokens */}
        {activeTab === "hermes" && (
          <div className="max-w-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm space-y-6">
            <div>
              <h3 className="font-serif text-lg font-bold text-slate-900 dark:text-white mb-1">
                Configuration de l&apos;Agent Autonome Hermes
              </h3>
              <p className="text-xs text-slate-500 dark:text-slate-400">
                L&apos;agent Hermes utilise un jeton d&apos;autorisation Bearer pour ingérer quotidiennement les nouveaux mots du lexique.
              </p>
            </div>

            <div className="bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl p-4">
              <span className="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">
                Jeton Secret d&apos;Ingestion (HERMES_API_KEY)
              </span>
              <div className="flex items-center justify-between font-mono text-xs text-slate-800 dark:text-slate-200">
                <span>hermes_classical_secure_token_2026</span>
                <span className="text-[10px] bg-emerald-500/10 text-emerald-500 font-bold px-2 py-0.5 rounded">
                  Actif
                </span>
              </div>
            </div>

            <div className="space-y-2">
              <span className="text-xs font-bold text-slate-700 dark:text-slate-300 block">
                Points de Terminaison API (REST Endpoints)
              </span>
              <div className="p-3 rounded-xl bg-black/60 font-mono text-xs text-slate-300 space-y-1">
                <p>GET  /api/v1/vocabulary (Lecture publique du lexique)</p>
                <p>POST /api/v1/vocabulary (Ingestion Hermes authentifiée)</p>
                <p>GET  /api/v1/status     (Sonde d&apos;intégrité de l&apos;Académie)</p>
              </div>
            </div>
          </div>
        )}
      </main>
    </div>
  );
}
