import books from "./books.json";

export interface BookItem {
  slug: string;
  file: string;
  pages: number;
  cover: string | null;
  title: string;
  author: string;
  level: string;
  blurb: string;
  blurb_hi: string;
}

export const bookShelf = books as BookItem[];

export type SiteCategory = "Listening" | "Grammar" | "Vocabulary" | "Reading" | "Exams" | "Tools";

export interface SiteItem {
  name: string;
  url: string;
  category: SiteCategory;
  level: string;
  free: "Free" | "Free + paid";
  desc: string;
  desc_hi: string;
}

/** Hand-picked sites. Reachable when added; a few block automated checks but open normally in a browser. */
export const sites: SiteItem[] = [
  { name: "TV5Monde – Apprendre le français", url: "https://apprendre.tv5monde.com", category: "Listening", level: "A1–C1", free: "Free", desc: "Graded videos with exercises and transcripts, plus TCF/TEF-style practice.", desc_hi: "वीडियो, अभ्यास और ट्रांसक्रिप्ट — स्तर के हिसाब से। TCF/TEF जैसा अभ्यास भी।" },
  { name: "RFI Savoirs / Français facile", url: "https://francaisfacile.rfi.fr", category: "Listening", level: "A2–B2", free: "Free", desc: "Slow news bulletins (Journal en français facile) with exercises.", desc_hi: "धीमी गति की ख़बरें और अभ्यास — सुनने के लिए बहुत अच्छी।" },
  { name: "Radio-Canada", url: "https://ici.radio-canada.ca", category: "Listening", level: "B2–C1", free: "Free", desc: "Canadian news, radio and TV: the accent and topics you meet in TEF Canada.", desc_hi: "कनाडाई ख़बरें और रेडियो — TEF Canada में मिलने वाला लहजा और विषय।" },
  { name: "Easy French (YouTube)", url: "https://www.youtube.com/@EasyFrench", category: "Listening", level: "A2–B2", free: "Free", desc: "Street interviews with French and English subtitles. Real speech at real speed.", desc_hi: "सड़क पर इंटरव्यू, फ़्रेंच-अंग्रेज़ी सबटाइटल के साथ — असली बोलचाल।" },
  { name: "InnerFrench", url: "https://innerfrench.com", category: "Listening", level: "B1–C1", free: "Free + paid", desc: "Podcast and video lessons spoken clearly in French for intermediate learners.", desc_hi: "मध्यम स्तर के लिए साफ़ फ़्रेंच में पॉडकास्ट और वीडियो।" },
  { name: "Français facile", url: "https://www.francaisfacile.com", category: "Grammar", level: "A1–C1", free: "Free", desc: "Thousands of free exercises on grammar, conjugation and vocabulary, with corrections.", desc_hi: "grammar, conjugation और vocabulary के हज़ारों मुफ़्त अभ्यास, जवाबों के साथ।" },
  { name: "Lingolia French", url: "https://www.lingolia.com", category: "Grammar", level: "A1–C1", free: "Free + paid", desc: "Clear explanations in English with exercises on every grammar point.", desc_hi: "अंग्रेज़ी में साफ़ व्याख्या और हर topic पर अभ्यास।" },
  { name: "Le Point du FLE", url: "https://www.lepointdufle.net", category: "Grammar", level: "A1–C1", free: "Free", desc: "Huge directory of free exercises, games and documents, sorted by level and skill.", desc_hi: "स्तर और कौशल के अनुसार सजाई गई मुफ़्त अभ्यास-सामग्री का बड़ा भंडार।" },
  { name: "Bonjour de France", url: "https://www.bonjourdefrance.com", category: "Grammar", level: "A1–B2", free: "Free", desc: "Short lessons and exercises for grammar, vocabulary and pronunciation.", desc_hi: "grammar, vocabulary और उच्चारण के छोटे पाठ।" },
  { name: "Le Conjugueur", url: "https://www.le-conjugueur.com", category: "Vocabulary", level: "All", free: "Free", desc: "Look up any verb in every tense in one click.", desc_hi: "किसी भी क्रिया का हर काल में रूप तुरंत देखें।" },
  { name: "Larousse (dictionnaire)", url: "https://www.larousse.fr", category: "Vocabulary", level: "All", free: "Free", desc: "Reference French dictionary with definitions, synonyms and expressions.", desc_hi: "फ़्रेंच शब्दकोश: अर्थ, पर्यायवाची और मुहावरे।" },
  { name: "Reverso Context", url: "https://context.reverso.net", category: "Vocabulary", level: "All", free: "Free + paid", desc: "See a word in real sentences with translations. Great for writing.", desc_hi: "असली वाक्यों में शब्द और अनुवाद देखें — लिखने के लिए उपयोगी।" },
  { name: "WordReference", url: "https://www.wordreference.com", category: "Vocabulary", level: "All", free: "Free", desc: "Bilingual dictionary with an active forum for tricky usage questions.", desc_hi: "द्विभाषी शब्दकोश और मुश्किल प्रयोग पर सवाल-जवाब का फ़ोरम।" },
  { name: "Forvo", url: "https://forvo.com", category: "Vocabulary", level: "All", free: "Free", desc: "Hear words pronounced by native speakers.", desc_hi: "मूल फ़्रेंच बोलने वालों से शब्दों का उच्चारण सुनें।" },
  { name: "Anki", url: "https://ankiweb.net", category: "Tools", level: "All", free: "Free + paid", desc: "Spaced-repetition flashcards, with ready-made French decks.", desc_hi: "spaced repetition फ़्लैशकार्ड, फ़्रेंच के तैयार डेक के साथ।" },
  { name: "LingQ", url: "https://www.lingq.com", category: "Reading", level: "A2–C1", free: "Free + paid", desc: "Read and listen to real content and save new words as you go.", desc_hi: "असली सामग्री पढ़ें-सुनें और नए शब्द सहेजते जाएँ।" },
  { name: "Lumni", url: "https://www.lumni.fr", category: "Reading", level: "A2–B2", free: "Free", desc: "French public-education videos and lessons for general culture and vocabulary.", desc_hi: "फ़्रांस की सरकारी शिक्षा के वीडियो और पाठ।" },
  { name: "TCF – France Éducation international", url: "https://www.france-education-international.fr", category: "Exams", level: "All", free: "Free", desc: "Official source for TCF and DELF/DALF: formats, sample papers, results.", desc_hi: "TCF और DELF/DALF का आधिकारिक स्रोत: फ़ॉर्मैट, नमूना पेपर।" },
  { name: "Le français des affaires (TEF)", url: "https://www.lefrancaisdesaffaires.fr", category: "Exams", level: "All", free: "Free", desc: "Official TEF organiser (CCI Paris Île-de-France): test information and preparation material.", desc_hi: "TEF के आयोजक (CCI Paris) — जानकारी और तैयारी सामग्री।" },
  { name: "Immigration Canada (IRCC)", url: "https://www.canada.ca/en/immigration-refugees-citizenship.html", category: "Exams", level: "All", free: "Free", desc: "Official language-test rules and the current NCLC equivalence charts.", desc_hi: "भाषा-परीक्षा के नियम और NCLC की ताज़ा तुलना तालिका।" },
];

export const siteCategories: SiteCategory[] = ["Listening", "Grammar", "Vocabulary", "Reading", "Exams", "Tools"];
