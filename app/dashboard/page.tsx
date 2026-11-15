"use client";

import { useState } from "react";
import Link from "next/link";
import { useUser, UserButton } from "@clerk/nextjs";
import {
  initialCourses,
  initialVocabulary,
  Course,
  Lesson,
  VocabularyWord
} from "@/lib/data";
import {
  BookOpen,
  Volume2,
  Flame,
  CheckCircle,
  HelpCircle,
  Plus,
  Search,
  Globe2,
  Sparkles,
  ShieldCheck,
  Award,
  ChevronRight,
  ArrowLeft,
  X,
  Play
} from "lucide-react";

export default function DashboardPage() {
  const { user } = useUser();

  // State
  const [courses, setCourses] = useState<Course[]>(initialCourses);
  const [lexicon, setLexicon] = useState<VocabularyWord[]>(initialVocabulary);
  const [selectedCourse, setSelectedCourse] = useState<Course>(initialCourses[0]);
  const [selectedLesson, setSelectedLesson] = useState<Lesson>(initialCourses[0].lessons[0]);
  const [activeTab, setActiveTab] = useState<"courses" | "lexicon">("courses");
  
  // Streak state (7-day activity)
  const [streakDays, setStreakDays] = useState<number>(14);
  const [completedDays, setCompletedDays] = useState<boolean[]>([
    true, true, true, true, true, false, false,
  ]);
  const [todayCompleted, setTodayCompleted] = useState<boolean>(false);

  // Audio speech
  const [activeSpeech, setActiveSpeech] = useState<string | null>(null);

  // Quiz state
  const [selectedQuizAnswer, setSelectedQuizAnswer] = useState<number | null>(null);
  const [quizSubmitted, setQuizSubmitted] = useState<boolean>(false);
  const [quizCorrect, setQuizCorrect] = useState<boolean>(false);

  // Lexicon search and filter
  const [searchLexicon, setSearchLexicon] = useState<string>("");
  const [selectedCefrFilter, setSelectedCefrFilter] = useState<string>("ALL");

  // Add Term Modal
  const [showAddModal, setShowAddModal] = useState<boolean>(false);
  const [newWord, setNewWord] = useState({
    french_word: "",
    ipa_phonetic: "",
    part_of_speech: "nom",
    meaning_en: "",
    meaning_hi: "",
    use_case: "",
    example_sentence: "",
    category: "Littérature",
    cefr_level: "B2",
  });

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

  const handleQuizAnswer = (index: number) => {
    if (quizSubmitted) return;
    setSelectedQuizAnswer(index);
    setQuizSubmitted(true);
    const isCorrect = index === selectedLesson.quiz.correct_index;
    setQuizCorrect(isCorrect);

    if (isCorrect && !todayCompleted) {
      setTodayCompleted(true);
      setStreakDays((prev) => prev + 1);
      setCompletedDays((prev) => {
        const next = [...prev];
        next[5] = true;
        return next;
      });
    }
  };

  const resetQuiz = (lesson: Lesson) => {
    setSelectedLesson(lesson);
    setSelectedQuizAnswer(null);
    setQuizSubmitted(false);
    setQuizCorrect(false);
  };

  const handleAddTerm = (e: React.FormEvent) => {
    e.preventDefault();
    if (!newWord.french_word || !newWord.meaning_en || !newWord.meaning_hi || !newWord.use_case) {
      alert("Veuillez remplir le mot français, les sens en anglais et हिन्दी, ainsi que le cas d'usage.");
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
      notes: "Ajouté manuellement par le Savant",
      created_at: new Date().toISOString(),
    };

    setLexicon([created, ...lexicon]);
    setShowAddModal(false);
    setNewWord({
      french_word: "",
      ipa_phonetic: "",
      part_of_speech: "nom",
      meaning_en: "",
      meaning_hi: "",
      use_case: "",
      example_sentence: "",
      category: "Littérature",
      cefr_level: "B2",
    });
  };

  const filteredLexicon = lexicon.filter((word) => {
    const q = searchLexicon.toLowerCase();
    const matchesSearch =
      word.french_word.toLowerCase().includes(q) ||
      word.meaning_en.toLowerCase().includes(q) ||
      word.meaning_hi.includes(searchLexicon) ||
      word.use_case.toLowerCase().includes(q);
    const matchesCefr = selectedCefrFilter === "ALL" || word.cefr_level === selectedCefrFilter;
    return matchesSearch && matchesCefr;
  });

  const scholarName = user?.fullName || user?.firstName || user?.username || "Savant Académique";
  const scholarEmail = user?.primaryEmailAddress?.emailAddress || "rrathore959910@gmail.com";

  return (
    <div className="min-h-screen bg-slate-50 dark:bg-[#090d16] text-slate-900 dark:text-slate-100 transition-colors duration-300">
      {/* Top Banner */}
      <div className="bg-[#D52B1E] text-white text-xs font-medium py-1 px-4 text-center tracking-wide flex items-center justify-center gap-2">
        <span>🍁 Académie de Français &bull; Sanctuaire d&apos;Immersion &bull; Ottawa &bull; Paris</span>
      </div>

      {/* Main Header */}
      <header className="sticky top-0 z-40 backdrop-blur-md bg-white/85 dark:bg-[#090d16]/85 border-b border-slate-200 dark:border-slate-800">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
          <div className="flex items-center gap-4">
            <Link href="/" className="flex items-center gap-3">
              <div className="w-10 h-10 rounded-xl bg-[#D52B1E] flex items-center justify-center text-white text-xl shadow-md shadow-red-500/20">
                🍁
              </div>
              <div>
                <span className="font-serif text-lg font-bold tracking-tight text-slate-900 dark:text-white block">
                  Sanctuaire de l&apos;Académie
                </span>
                <span className="text-[10px] uppercase font-bold tracking-wider text-[#D52B1E]">
                  Immersion CEFR &bull; Sans XP Clutter
                </span>
              </div>
            </Link>
          </div>

          {/* Navigation Controls */}
          <div className="flex items-center gap-4">
            <div className="flex items-center bg-slate-100 dark:bg-slate-800 p-1 rounded-xl text-xs font-semibold">
              <button
                onClick={() => setActiveTab("courses")}
                className={`py-1.5 px-3 rounded-lg transition-all flex items-center gap-1.5 ${
                  activeTab === "courses"
                    ? "bg-white dark:bg-slate-900 text-[#D52B1E] shadow-sm font-bold"
                    : "text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white"
                }`}
              >
                <BookOpen className="w-3.5 h-3.5" />
                Syllabi CEFR
              </button>
              <button
                onClick={() => setActiveTab("lexicon")}
                className={`py-1.5 px-3 rounded-lg transition-all flex items-center gap-1.5 ${
                  activeTab === "lexicon"
                    ? "bg-white dark:bg-slate-900 text-[#D52B1E] shadow-sm font-bold"
                    : "text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white"
                }`}
              >
                <Globe2 className="w-3.5 h-3.5" />
                Lexique Trilingue
              </button>
            </div>

            <Link
              href="/admin"
              className="text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-[#D52B1E] hidden sm:flex items-center gap-1"
            >
              <ShieldCheck className="w-4 h-4 text-[#D52B1E]" />
              Console Faculté
            </Link>

            <div className="h-6 w-px bg-slate-200 dark:bg-slate-800" />
            <UserButton afterSignOutUrl="/" />
          </div>
        </div>
      </header>

      {/* Main Container */}
      <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* Scholar Banner & Flame Activity Tracker */}
        <div className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm mb-8 flex flex-col md:flex-row items-center justify-between gap-6">
          <div className="flex items-center gap-4">
            <div className="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#D52B1E] to-[#8B180E] flex items-center justify-center text-white text-2xl font-serif font-bold shadow-md shadow-red-500/20">
              {scholarName.charAt(0)}
            </div>
            <div>
              <div className="flex items-center gap-2">
                <h1 className="font-serif text-xl font-bold text-slate-900 dark:text-white">
                  {scholarName}
                </h1>
                <span className="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-red-100 dark:bg-red-950/80 text-[#D52B1E] border border-red-200 dark:border-red-900/60">
                  {selectedCourse.cefr_level} Fellow
                </span>
              </div>
              <p className="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                {scholarEmail} &bull; Profil vérifié Clerk
              </p>
            </div>
          </div>

          {/* 7-Day Flame Tracker */}
          <div className="flex items-center gap-6 bg-slate-50 dark:bg-slate-850/80 border border-slate-200 dark:border-slate-800 rounded-xl p-3 sm:px-5">
            <div className="flex items-center gap-3">
              <div className="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-500">
                <Flame className="w-6 h-6 animate-pulse" />
              </div>
              <div>
                <div className="text-lg font-bold text-slate-900 dark:text-white leading-none">
                  {streakDays} Jours
                </div>
                <div className="text-[10px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">
                  Assiduité Continue
                </div>
              </div>
            </div>

            <div className="h-8 w-px bg-slate-200 dark:border-slate-700" />

            {/* 7-Day Dots */}
            <div className="flex items-center gap-1.5">
              {["L", "M", "M", "J", "V", "S", "D"].map((day, idx) => {
                const isDone = completedDays[idx];
                return (
                  <div key={idx} className="flex flex-col items-center gap-1">
                    <span className="text-[9px] font-semibold text-slate-400">{day}</span>
                    <div
                      className={`w-5 h-5 rounded-md flex items-center justify-center text-[10px] transition-all ${
                        isDone
                          ? "bg-amber-500 text-white shadow-sm shadow-amber-500/30"
                          : "bg-slate-200 dark:bg-slate-800 text-slate-400 border border-slate-300 dark:border-slate-700"
                      }`}
                    >
                      {isDone ? "🔥" : "·"}
                    </div>
                  </div>
                );
              })}
            </div>
          </div>
        </div>

        {/* Tab 1: CEFR Courses & Interactive Quizzes */}
        {activeTab === "courses" && (
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
            {/* Left Sidebar: CEFR Levels & Lessons List */}
            <div className="lg:col-span-4 space-y-6">
              {/* Level Selector */}
              <div className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-sm">
                <span className="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block mb-3">
                  Niveaux du Cadre Européen (CEFR)
                </span>
                <div className="grid grid-cols-5 gap-1.5">
                  {courses.map((course) => (
                    <button
                      key={course.id}
                      onClick={() => {
                        setSelectedCourse(course);
                        resetQuiz(course.lessons[0]);
                      }}
                      className={`py-2 text-center rounded-xl font-bold text-xs transition-all ${
                        selectedCourse.id === course.id
                          ? "bg-[#D52B1E] text-white shadow-md shadow-red-500/20"
                          : "bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700"
                      }`}
                    >
                      {course.cefr_level}
                    </button>
                  ))}
                </div>
              </div>

              {/* Course Title & Lessons */}
              <div className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm">
                <div className="flex items-center justify-between mb-2">
                  <h2 className="font-serif text-lg font-bold text-slate-900 dark:text-white">
                    {selectedCourse.title}
                  </h2>
                  <span className="text-xs font-semibold px-2 py-0.5 rounded bg-red-100 dark:bg-red-950 text-[#D52B1E]">
                    {selectedCourse.cefr_level}
                  </span>
                </div>
                <p className="text-xs text-slate-600 dark:text-slate-400 mb-4 leading-relaxed">
                  {selectedCourse.description}
                </p>

                <div className="space-y-2">
                  {selectedCourse.lessons.map((lesson) => (
                    <button
                      key={lesson.id}
                      onClick={() => resetQuiz(lesson)}
                      className={`w-full text-left p-3 rounded-xl transition-all flex items-center justify-between border ${
                        selectedLesson.id === lesson.id
                          ? "bg-red-50 dark:bg-red-950/40 border-[#D52B1E] text-slate-900 dark:text-white font-medium"
                          : "border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/60 text-slate-700 dark:text-slate-300"
                      }`}
                    >
                      <div className="flex items-center gap-2.5">
                        <span className="w-6 h-6 rounded-lg bg-slate-200 dark:bg-slate-800 text-xs font-bold flex items-center justify-center text-slate-600 dark:text-slate-400">
                          {lesson.order_num}
                        </span>
                        <div className="text-xs font-semibold">{lesson.title}</div>
                      </div>
                      <ChevronRight className="w-3.5 h-3.5 text-slate-400" />
                    </button>
                  ))}
                </div>
              </div>
            </div>

            {/* Right Pane: Lesson Content & Acoustic Quiz */}
            <div className="lg:col-span-8 space-y-6">
              {/* Lesson Reader Card */}
              <div className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 sm:p-8 shadow-sm">
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100 dark:border-slate-800 mb-6">
                  <div>
                    <span className="text-[10px] font-bold uppercase tracking-widest text-[#D52B1E] block mb-1">
                      Leçon {selectedLesson.order_num} &bull; Niveau {selectedCourse.cefr_level}
                    </span>
                    <h3 className="font-serif text-2xl font-bold text-slate-900 dark:text-white">
                      {selectedLesson.title}
                    </h3>
                  </div>

                  <button
                    onClick={() => speakFrench(selectedLesson.content_fr)}
                    className={`inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold border transition-all ${
                      activeSpeech === selectedLesson.content_fr
                        ? "bg-[#D52B1E] text-white border-[#D52B1E] animate-pulse"
                        : "bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 border-slate-200 dark:border-slate-700 hover:bg-[#D52B1E] hover:text-white"
                    }`}
                  >
                    <Volume2 className="w-4 h-4" />
                    Écouter la Synthèse Vocale
                  </button>
                </div>

                {/* French Immersion Text */}
                <div className="space-y-4 mb-8">
                  <div className="bg-slate-50 dark:bg-slate-850/60 border border-slate-200 dark:border-slate-800 rounded-xl p-5">
                    <span className="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-2">
                      Texte d&apos;Immersion Français (French Corpus)
                    </span>
                    <p className="font-serif text-base sm:text-lg text-slate-900 dark:text-slate-100 leading-relaxed italic">
                      &ldquo;{selectedLesson.content_fr}&rdquo;
                    </p>
                  </div>

                  <div className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-5">
                    <span className="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-2">
                      Traduction Philologique (English Translation)
                    </span>
                    <p className="text-sm text-slate-700 dark:text-slate-300 leading-relaxed">
                      {selectedLesson.content_en}
                    </p>
                  </div>
                </div>

                {/* Interactive Acoustic Quiz */}
                <div className="bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-900 dark:to-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl p-6">
                  <div className="flex items-center justify-between mb-4">
                    <div className="flex items-center gap-2">
                      <HelpCircle className="w-5 h-5 text-[#D52B1E]" />
                      <h4 className="font-serif text-lg font-bold text-slate-900 dark:text-white">
                        Contrôle de Compréhension Acoustique
                      </h4>
                    </div>
                    <span className="text-xs text-slate-500 font-medium">Validation Immédiate</span>
                  </div>

                  <p className="text-sm font-medium text-slate-800 dark:text-slate-200 mb-4">
                    {selectedLesson.quiz.question}
                  </p>

                  <div className="space-y-2.5 mb-6">
                    {selectedLesson.quiz.options.map((opt, idx) => {
                      let btnStyle =
                        "bg-white dark:bg-slate-800/80 border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 hover:border-[#D52B1E]";
                      if (quizSubmitted) {
                        if (idx === selectedLesson.quiz.correct_index) {
                          btnStyle =
                            "bg-emerald-500/10 border-emerald-500 text-emerald-600 dark:text-emerald-400 font-bold";
                        } else if (idx === selectedQuizAnswer) {
                          btnStyle = "bg-red-500/10 border-red-500 text-red-600 dark:text-red-400";
                        }
                      } else if (selectedQuizAnswer === idx) {
                        btnStyle = "border-[#D52B1E] bg-red-50 dark:bg-red-950/40 text-[#D52B1E]";
                      }

                      return (
                        <button
                          key={idx}
                          onClick={() => handleQuizAnswer(idx)}
                          disabled={quizSubmitted}
                          className={`w-full text-left p-3 rounded-xl border text-xs sm:text-sm font-medium transition-all flex items-center justify-between ${btnStyle}`}
                        >
                          <span>{opt}</span>
                          {quizSubmitted && idx === selectedLesson.quiz.correct_index && (
                            <CheckCircle className="w-4 h-4 text-emerald-500" />
                          )}
                        </button>
                      );
                    })}
                  </div>

                  {quizSubmitted && (
                    <div
                      className={`p-4 rounded-xl border text-xs leading-relaxed ${
                        quizCorrect
                          ? "bg-emerald-500/10 border-emerald-500/30 text-emerald-700 dark:text-emerald-300"
                          : "bg-red-500/10 border-red-500/30 text-red-700 dark:text-red-300"
                      }`}
                    >
                      <div className="font-bold mb-1">
                        {quizCorrect ? "Excellente réponse! 🍁" : "Réponse incorrecte."}
                      </div>
                      <p>{selectedLesson.quiz.explanation}</p>
                    </div>
                  )}
                </div>
              </div>
            </div>
          </div>
        )}

        {/* Tab 2: Multilingual Lexicon Sanctuary */}
        {activeTab === "lexicon" && (
          <div className="space-y-6">
            {/* Lexicon Controls & Search */}
            <div className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
              <div>
                <span className="text-xs font-bold uppercase tracking-widest text-[#D52B1E] block mb-1">
                  Corpus Philologique
                </span>
                <h2 className="font-serif text-2xl font-bold text-slate-900 dark:text-white">
                  Sanctuaire de Vocabulaire Trilingue (Français &bull; English &bull; हिन्दी)
                </h2>
                <p className="text-xs text-slate-500 dark:text-slate-400 mt-1">
                  Tous les termes disposent de prononciation audio, sens en Devanagari et cas d&apos;usage pratiques.
                </p>
              </div>

              <div className="flex flex-wrap items-center gap-3 w-full md:w-auto">
                <div className="relative flex-1 sm:w-64">
                  <Search className="w-4 h-4 absolute left-3 top-3 text-slate-400" />
                  <input
                    type="text"
                    placeholder="Filtrer (mot, हिन्दी, english)..."
                    value={searchLexicon}
                    onChange={(e) => setSearchLexicon(e.target.value)}
                    className="w-full pl-9 pr-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs focus:ring-2 focus:ring-[#D52B1E] outline-none"
                  />
                </div>

                <select
                  value={selectedCefrFilter}
                  onChange={(e) => setSelectedCefrFilter(e.target.value)}
                  className="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-[#D52B1E] outline-none"
                >
                  <option value="ALL">Tous Niveaux</option>
                  <option value="B1">B1</option>
                  <option value="B2">B2</option>
                  <option value="C1">C1</option>
                  <option value="C2">C2</option>
                </select>

                <button
                  onClick={() => setShowAddModal(true)}
                  className="bg-[#D52B1E] hover:bg-[#B52419] text-white text-xs font-bold py-2 px-4 rounded-xl shadow-md shadow-red-500/20 transition-all flex items-center gap-1.5"
                >
                  <Plus className="w-4 h-4" />
                  Nouveau Mot
                </button>
              </div>
            </div>

            {/* Vocabulary Grid */}
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {filteredLexicon.map((word) => (
                <div
                  key={word.id}
                  className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all flex flex-col justify-between"
                >
                  <div>
                    <div className="flex items-start justify-between gap-3 mb-4">
                      <div>
                        <div className="flex items-center gap-2">
                          <h3 className="font-serif text-2xl font-bold text-slate-900 dark:text-white">
                            {word.french_word}
                          </h3>
                          <button
                            onClick={() => speakFrench(word.french_word)}
                            className={`p-1.5 rounded-lg border transition-all ${
                              activeSpeech === word.french_word
                                ? "bg-[#D52B1E] text-white border-[#D52B1E] animate-pulse"
                                : "bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-[#D52B1E] hover:text-white"
                            }`}
                            title="Écouter la prononciation"
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

                    <div className="space-y-2.5 mb-4 pt-3 border-t border-slate-100 dark:border-slate-800">
                      <div>
                        <span className="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">
                          English Meaning
                        </span>
                        <p className="text-sm font-medium text-slate-800 dark:text-slate-200">
                          {word.meaning_en}
                        </p>
                      </div>

                      {/* Hindi Translation in Devanagari */}
                      <div className="bg-amber-500/5 dark:bg-amber-500/10 border border-amber-500/20 rounded-xl p-2.5">
                        <span className="text-[10px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400 block mb-0.5">
                          हिन्दी अर्थ (Hindi Meaning)
                        </span>
                        <p className="text-sm font-semibold text-slate-900 dark:text-amber-100">
                          {word.meaning_hi}
                        </p>
                      </div>
                    </div>

                    {/* Practical Use Case */}
                    <div className="bg-slate-50 dark:bg-slate-850/60 border border-slate-200 dark:border-slate-800 rounded-xl p-3 mb-4">
                      <div className="flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">
                        <Sparkles className="w-3 h-3 text-[#D52B1E]" />
                        <span>Cas d&apos;Usage Pratique</span>
                      </div>
                      <p className="text-xs text-slate-700 dark:text-slate-300 italic">
                        &ldquo;{word.use_case}&rdquo;
                      </p>
                    </div>

                    <div className="text-xs text-slate-600 dark:text-slate-400">
                      <span className="font-semibold text-slate-800 dark:text-slate-200">Exemple : </span>
                      <span>{word.example_sentence}</span>
                    </div>
                  </div>

                  <div className="mt-5 pt-3 border-t border-slate-100 dark:border-slate-800 text-[11px] text-slate-400 flex items-center justify-between">
                    <span>{word.category}</span>
                    <span className="text-slate-500 text-[10px]">Hermes Ingested</span>
                  </div>
                </div>
              ))}
            </div>
          </div>
        )}

        {/* Modal: Add New Classical Term */}
        {showAddModal && (
          <div className="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 sm:p-8 max-w-lg w-full shadow-2xl relative">
              <button
                onClick={() => setShowAddModal(false)}
                className="absolute top-4 right-4 p-1.5 rounded-lg text-slate-400 hover:text-slate-900 dark:hover:text-white"
              >
                <X className="w-5 h-5" />
              </button>

              <div className="flex items-center gap-2 mb-4">
                <span className="text-xl">🍁</span>
                <h3 className="font-serif text-xl font-bold text-slate-900 dark:text-white">
                  Ajouter un Terme au Sanctuaire
                </h3>
              </div>

              <form onSubmit={handleAddTerm} className="space-y-4">
                <div className="grid grid-cols-2 gap-3">
                  <div>
                    <label className="text-xs font-semibold text-slate-700 dark:text-slate-300 block mb-1">
                      Mot Français *
                    </label>
                    <input
                      type="text"
                      required
                      placeholder="ex: Panacée"
                      value={newWord.french_word}
                      onChange={(e) => setNewWord({ ...newWord, french_word: e.target.value })}
                      className="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-[#D52B1E] outline-none"
                    />
                  </div>
                  <div>
                    <label className="text-xs font-semibold text-slate-700 dark:text-slate-300 block mb-1">
                      Phonétique IPA
                    </label>
                    <input
                      type="text"
                      placeholder="/pa.na.se/"
                      value={newWord.ipa_phonetic}
                      onChange={(e) => setNewWord({ ...newWord, ipa_phonetic: e.target.value })}
                      className="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-[#D52B1E] outline-none"
                    />
                  </div>
                </div>

                <div>
                  <label className="text-xs font-semibold text-slate-700 dark:text-slate-300 block mb-1">
                    Sens en Anglais (Meaning EN) *
                  </label>
                  <input
                    type="text"
                    required
                    placeholder="ex: A solution or remedy for all difficulties"
                    value={newWord.meaning_en}
                    onChange={(e) => setNewWord({ ...newWord, meaning_en: e.target.value })}
                    className="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-[#D52B1E] outline-none"
                  />
                </div>

                <div>
                  <label className="text-xs font-semibold text-amber-600 dark:text-amber-400 block mb-1">
                    हिन्दी अर्थ (Hindi Meaning in Devanagari) *
                  </label>
                  <input
                    type="text"
                    required
                    placeholder="ex: रामबाण / सभी रोगों का इलाज"
                    value={newWord.meaning_hi}
                    onChange={(e) => setNewWord({ ...newWord, meaning_hi: e.target.value })}
                    className="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-[#D52B1E] outline-none"
                  />
                </div>

                <div>
                  <label className="text-xs font-semibold text-slate-700 dark:text-slate-300 block mb-1">
                    Cas d&apos;Usage Pratique (Use Case) *
                  </label>
                  <textarea
                    rows={2}
                    required
                    placeholder="Quand et comment utiliser ce mot dans une dissertation ou discussion littéraire..."
                    value={newWord.use_case}
                    onChange={(e) => setNewWord({ ...newWord, use_case: e.target.value })}
                    className="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-[#D52B1E] outline-none"
                  />
                </div>

                <div className="grid grid-cols-2 gap-3">
                  <div>
                    <label className="text-xs font-semibold text-slate-700 dark:text-slate-300 block mb-1">
                      Niveau CEFR
                    </label>
                    <select
                      value={newWord.cefr_level}
                      onChange={(e) => setNewWord({ ...newWord, cefr_level: e.target.value })}
                      className="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-[#D52B1E] outline-none"
                    >
                      <option value="B1">B1 Intermédiaire</option>
                      <option value="B2">B2 Avancé</option>
                      <option value="C1">C1 Autonome</option>
                      <option value="C2">C2 Maîtrise</option>
                    </select>
                  </div>
                  <div>
                    <label className="text-xs font-semibold text-slate-700 dark:text-slate-300 block mb-1">
                      Catégorie
                    </label>
                    <input
                      type="text"
                      value={newWord.category}
                      onChange={(e) => setNewWord({ ...newWord, category: e.target.value })}
                      className="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-[#D52B1E] outline-none"
                    />
                  </div>
                </div>

                <div className="flex justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-800">
                  <button
                    type="button"
                    onClick={() => setShowAddModal(false)}
                    className="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800"
                  >
                    Annuler
                  </button>
                  <button
                    type="submit"
                    className="px-5 py-2 rounded-xl text-xs font-bold bg-[#D52B1E] hover:bg-[#B52419] text-white shadow-md shadow-red-500/20"
                  >
                    Enregistrer au Sanctuaire
                  </button>
                </div>
              </form>
            </div>
          </div>
        )}
      </main>
    </div>
  );
}
