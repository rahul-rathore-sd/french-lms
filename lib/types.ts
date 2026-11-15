export interface QuizData {
  question: string;
  options: string[];
  correct_index: number;
  explanation: string;
}

export interface Lesson {
  id: number;
  course_id: number;
  order?: number;
  order_num: number;
  title: string;
  section_title?: string;
  duration_mins?: number;
  french_text?: string;
  content_fr: string;
  english_translation?: string;
  content_en: string;
  audio_phrase?: string;
  quiz_question?: string;
  quiz_options_json?: string[];
  quiz_correct_index?: number;
  quiz: QuizData;
}

export interface Course {
  id: number;
  title: string;
  slug?: string;
  level?: "A1" | "A2" | "B1" | "B2" | "C1" | "C2";
  cefr_level: "A1" | "A2" | "B1" | "B2" | "C1" | "C2";
  description: string;
  instructor_name?: string;
  instructor_role?: string;
  duration_weeks?: number;
  lessons_count?: number;
  badge_color?: string;
  icon?: string;
  lessons: Lesson[];
}

export interface VocabularyWord {
  id: number;
  french_word: string;
  word?: string;
  ipa_phonetic: string;
  ipa?: string;
  part_of_speech: string;
  meaning_en: string;
  meaning?: string;
  meaning_hi: string; // Devanagari Hindi translation
  cefr_level: string;
  level?: string;
  category: string;
  example_sentence: string;
  example_fr?: string;
  example_en?: string;
  example_hi?: string;
  use_case: string; // Practical usage context
  notes?: string;
  source?: string;
  created_at?: string;
}

export interface Scholar {
  id: number;
  name: string;
  email: string;
  role: string;
  role_title?: string;
  level?: string;
  cefr_level: string;
  country?: string;
  region: string;
  current_streak?: number;
  streak_days: number;
  target_goal?: string;
}

export interface AdmissionRequest {
  id: number;
  full_name: string;
  email: string;
  target_cefr: "A1" | "A2" | "B1" | "B2" | "C1" | "C2";
  academic_background: string;
  motivation: string;
  status: "pending" | "approved" | "rejected";
  submitted_at: string;
  reviewed_at?: string;
  rejection_reason?: string;
}

