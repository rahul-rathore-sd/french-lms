import type { Lesson, VocabularyWord } from "./types";
import { moreRows } from "./content-more";

/**
 * Core everyday vocabulary (A1–B1). Compact tuple form keeps the file readable;
 * `core()` expands each row into a full VocabularyWord.
 */
export type Row = [
  french: string,
  ipa: string,
  pos: string,
  en: string,
  hi: string,
  level: string,
  category: string,
  exFr: string,
  exEn: string,
  exHi: string,
];

const rows: Row[] = [
  // Greetings & politeness
  ["Bonjour", "/bɔ̃.ʒuʁ/", "interjection", "Hello; good morning", "नमस्ते; सुप्रभात", "A1", "Greetings", "Bonjour, comment allez-vous ?", "Hello, how are you?", "नमस्ते, आप कैसे हैं?"],
  ["Merci", "/mɛʁ.si/", "interjection", "Thank you", "धन्यवाद", "A1", "Greetings", "Merci beaucoup pour votre aide.", "Thank you very much for your help.", "आपकी मदद के लिए बहुत धन्यवाद।"],
  ["S’il vous plaît", "/sil vu plɛ/", "locution", "Please (formal)", "कृपया (औपचारिक)", "A1", "Greetings", "Un café, s’il vous plaît.", "A coffee, please.", "एक कॉफ़ी, कृपया।"],
  ["Au revoir", "/o ʁə.vwaʁ/", "interjection", "Goodbye", "अलविदा", "A1", "Greetings", "Au revoir et à demain !", "Goodbye and see you tomorrow!", "अलविदा और कल मिलते हैं!"],
  ["Excusez-moi", "/ɛk.sky.ze mwa/", "locution", "Excuse me; sorry", "क्षमा कीजिए", "A1", "Greetings", "Excusez-moi, où est la gare ?", "Excuse me, where is the station?", "क्षमा कीजिए, स्टेशन कहाँ है?"],
  ["Enchanté", "/ɑ̃.ʃɑ̃.te/", "adjectif", "Pleased to meet you", "आपसे मिलकर खुशी हुई", "A1", "Greetings", "Je m’appelle Léa, enchantée.", "My name is Léa, pleased to meet you.", "मेरा नाम लेआ है, आपसे मिलकर खुशी हुई।"],
  ["De rien", "/də ʁjɛ̃/", "locution", "You are welcome", "कोई बात नहीं; आपका स्वागत है", "A1", "Greetings", "— Merci ! — De rien.", "“Thank you!” “You are welcome.”", "“धन्यवाद!” “कोई बात नहीं।”"],

  // Essentials & questions
  ["Oui", "/wi/", "adverbe", "Yes", "हाँ", "A1", "Essentials", "Oui, je parle un peu français.", "Yes, I speak a little French.", "हाँ, मैं थोड़ी फ़्रेंच बोलता हूँ।"],
  ["Non", "/nɔ̃/", "adverbe", "No", "नहीं", "A1", "Essentials", "Non, merci, ça va.", "No, thank you, I am fine.", "नहीं, धन्यवाद, मैं ठीक हूँ।"],
  ["Comment", "/kɔ.mɑ̃/", "adverbe", "How; what (asking to repeat)", "कैसे; क्या (दोहराने के लिए)", "A1", "Essentials", "Comment vous appelez-vous ?", "What is your name?", "आपका नाम क्या है?"],
  ["Où", "/u/", "adverbe", "Where", "कहाँ", "A1", "Essentials", "Où habitez-vous ?", "Where do you live?", "आप कहाँ रहते हैं?"],
  ["Pourquoi", "/puʁ.kwa/", "adverbe", "Why", "क्यों", "A1", "Essentials", "Pourquoi apprenez-vous le français ?", "Why are you learning French?", "आप फ़्रेंच क्यों सीख रहे हैं?"],
  ["Parler", "/paʁ.le/", "verbe", "To speak", "बोलना", "A1", "Verbs", "Je voudrais parler plus lentement.", "I would like to speak more slowly.", "मैं और धीरे बोलना चाहूँगा।"],
  ["Comprendre", "/kɔ̃.pʁɑ̃dʁ/", "verbe", "To understand", "समझना", "A1", "Verbs", "Je ne comprends pas, répétez s’il vous plaît.", "I do not understand, please repeat.", "मुझे समझ नहीं आया, कृपया दोहराइए।"],

  // Verbs
  ["Être", "/ɛtʁ/", "verbe", "To be", "होना", "A1", "Verbs", "Je suis étudiant et elle est médecin.", "I am a student and she is a doctor.", "मैं छात्र हूँ और वह डॉक्टर है।"],
  ["Avoir", "/a.vwaʁ/", "verbe", "To have", "रखना; पास होना", "A1", "Verbs", "Nous avons deux enfants.", "We have two children.", "हमारे दो बच्चे हैं।"],
  ["Aller", "/a.le/", "verbe", "To go", "जाना", "A1", "Verbs", "Je vais au marché ce matin.", "I am going to the market this morning.", "मैं आज सुबह बाज़ार जा रहा हूँ।"],
  ["Faire", "/fɛʁ/", "verbe", "To do; to make", "करना; बनाना", "A1", "Verbs", "Que faites-vous ce week-end ?", "What are you doing this weekend?", "आप इस सप्ताहांत क्या कर रहे हैं?"],
  ["Vouloir", "/vu.lwaʁ/", "verbe", "To want", "चाहना", "A2", "Verbs", "Je veux visiter Paris un jour.", "I want to visit Paris one day.", "मैं किसी दिन पेरिस घूमना चाहता हूँ।"],
  ["Pouvoir", "/pu.vwaʁ/", "verbe", "To be able to; can", "सकना", "A2", "Verbs", "Pouvez-vous m’aider, s’il vous plaît ?", "Can you help me, please?", "क्या आप मेरी मदद कर सकते हैं?"],
  ["Savoir", "/sa.vwaʁ/", "verbe", "To know (a fact or how to)", "जानना", "A2", "Verbs", "Je sais nager depuis l’enfance.", "I have known how to swim since childhood.", "मैं बचपन से तैरना जानता हूँ।"],
  ["Manger", "/mɑ̃.ʒe/", "verbe", "To eat", "खाना", "A1", "Verbs", "Nous mangeons à midi.", "We eat at noon.", "हम दोपहर में खाना खाते हैं।"],
  ["Apprendre", "/a.pʁɑ̃dʁ/", "verbe", "To learn", "सीखना", "A2", "Verbs", "J’apprends le français tous les jours.", "I learn French every day.", "मैं रोज़ फ़्रेंच सीखता हूँ।"],

  // Food & drink
  ["Le pain", "/lə pɛ̃/", "nom masculin", "Bread", "रोटी; ब्रेड", "A1", "Food & Drink", "Le pain de la boulangerie est délicieux.", "The bakery bread is delicious.", "बेकरी की ब्रेड बहुत स्वादिष्ट है।"],
  ["L’eau", "/lo/", "nom féminin", "Water", "पानी", "A1", "Food & Drink", "Une carafe d’eau, s’il vous plaît.", "A jug of water, please.", "पानी की एक सुराही, कृपया।"],
  ["Le café", "/lə ka.fe/", "nom masculin", "Coffee; café", "कॉफ़ी; कैफ़े", "A1", "Food & Drink", "Je prends un café crème le matin.", "I have a café crème in the morning.", "मैं सुबह क्रीम वाली कॉफ़ी लेता हूँ।"],
  ["Le fromage", "/lə fʁɔ.maʒ/", "nom masculin", "Cheese", "पनीर; चीज़", "A1", "Food & Drink", "La France produit plus de mille fromages.", "France produces over a thousand cheeses.", "फ़्रांस में एक हज़ार से अधिक प्रकार के चीज़ बनते हैं।"],
  ["L’addition", "/la.di.sjɔ̃/", "nom féminin", "The bill (restaurant)", "बिल (रेस्तराँ में)", "A2", "Food & Drink", "L’addition, s’il vous plaît.", "The bill, please.", "बिल दीजिए, कृपया।"],
  ["Délicieux", "/de.li.sjø/", "adjectif", "Delicious", "स्वादिष्ट", "A2", "Food & Drink", "Ce gâteau est vraiment délicieux.", "This cake is really delicious.", "यह केक सचमुच स्वादिष्ट है।"],

  // Family & people
  ["La famille", "/la fa.mij/", "nom féminin", "Family", "परिवार", "A1", "Family", "Ma famille habite à Lyon.", "My family lives in Lyon.", "मेरा परिवार लियों में रहता है।"],
  ["Le frère", "/lə fʁɛʁ/", "nom masculin", "Brother", "भाई", "A1", "Family", "Mon frère travaille à Montréal.", "My brother works in Montreal.", "मेरा भाई मॉन्ट्रियल में काम करता है।"],
  ["La sœur", "/la sœʁ/", "nom féminin", "Sister", "बहन", "A1", "Family", "Ma sœur est plus âgée que moi.", "My sister is older than me.", "मेरी बहन मुझसे बड़ी है।"],
  ["L’ami", "/la.mi/", "nom masculin", "Friend", "दोस्त", "A1", "Family", "C’est un ami d’enfance.", "He is a childhood friend.", "वह बचपन का दोस्त है।"],
  ["Les parents", "/le pa.ʁɑ̃/", "nom masculin pluriel", "Parents", "माता-पिता", "A1", "Family", "Mes parents voyagent souvent.", "My parents travel often.", "मेरे माता-पिता अक्सर यात्रा करते हैं।"],

  // Time & numbers
  ["Aujourd’hui", "/o.ʒuʁ.dɥi/", "adverbe", "Today", "आज", "A1", "Time", "Aujourd’hui, il fait beau.", "Today, the weather is nice.", "आज मौसम अच्छा है।"],
  ["Demain", "/də.mɛ̃/", "adverbe", "Tomorrow", "कल (आने वाला)", "A1", "Time", "À demain, mes amis !", "See you tomorrow, my friends!", "कल मिलते हैं, मेरे दोस्तो!"],
  ["Hier", "/jɛʁ/", "adverbe", "Yesterday", "कल (बीता हुआ)", "A1", "Time", "Hier, j’ai visité le Louvre.", "Yesterday, I visited the Louvre.", "कल मैंने लूव्र देखा।"],
  ["Maintenant", "/mɛ̃t.nɑ̃/", "adverbe", "Now", "अब", "A1", "Time", "Je commence maintenant.", "I am starting now.", "मैं अब शुरू कर रहा हूँ।"],
  ["Toujours", "/tu.ʒuʁ/", "adverbe", "Always; still", "हमेशा; अब भी", "A2", "Time", "Elle arrive toujours à l’heure.", "She always arrives on time.", "वह हमेशा समय पर आती है।"],
  ["Souvent", "/su.vɑ̃/", "adverbe", "Often", "अक्सर", "A2", "Time", "Nous allons souvent au cinéma.", "We often go to the cinema.", "हम अक्सर सिनेमा जाते हैं।"],
  ["La semaine", "/la sə.mɛn/", "nom féminin", "Week", "सप्ताह; हफ़्ता", "A1", "Time", "Je travaille cinq jours par semaine.", "I work five days a week.", "मैं हफ़्ते में पाँच दिन काम करता हूँ।"],
  ["L’heure", "/lœʁ/", "nom féminin", "Hour; time (of day)", "घंटा; समय", "A1", "Time", "Quelle heure est-il ?", "What time is it?", "क्या समय हुआ है?"],

  // Travel & places
  ["La gare", "/la ɡaʁ/", "nom féminin", "Train station", "रेलवे स्टेशन", "A1", "Travel", "La gare est à côté de l’hôtel.", "The station is next to the hotel.", "स्टेशन होटल के बगल में है।"],
  ["Le billet", "/lə bi.jɛ/", "nom masculin", "Ticket", "टिकट", "A1", "Travel", "Un billet pour Paris, s’il vous plaît.", "A ticket to Paris, please.", "पेरिस का एक टिकट, कृपया।"],
  ["La rue", "/la ʁy/", "nom féminin", "Street", "सड़क; गली", "A1", "Travel", "Tournez à gauche dans la rue Victor Hugo.", "Turn left onto Victor Hugo street.", "विक्टर ह्यूगो गली में बाएँ मुड़िए।"],
  ["À gauche", "/a ɡoʃ/", "locution", "To the left", "बाईं ओर", "A1", "Travel", "La poste est à gauche.", "The post office is on the left.", "डाकघर बाईं ओर है।"],
  ["À droite", "/a dʁwat/", "locution", "To the right", "दाईं ओर", "A1", "Travel", "Prenez la deuxième rue à droite.", "Take the second street on the right.", "दाईं ओर की दूसरी सड़क लीजिए।"],
  ["Tout droit", "/tu dʁwa/", "locution", "Straight ahead", "सीधे", "A1", "Travel", "Continuez tout droit jusqu’au pont.", "Keep going straight to the bridge.", "पुल तक सीधे चलते रहिए।"],
  ["Le voyage", "/lə vwa.jaʒ/", "nom masculin", "Trip; journey", "यात्रा", "A2", "Travel", "Bon voyage et à bientôt !", "Have a good trip and see you soon!", "यात्रा शुभ हो, जल्द मिलेंगे!"],

  // Describing & feelings
  ["Grand", "/ɡʁɑ̃/", "adjectif", "Big; tall", "बड़ा; लंबा", "A1", "Adjectives", "C’est une grande ville.", "It is a big city.", "यह एक बड़ा शहर है।"],
  ["Petit", "/pə.ti/", "adjectif", "Small; short", "छोटा", "A1", "Adjectives", "J’habite dans un petit appartement.", "I live in a small apartment.", "मैं एक छोटे फ़्लैट में रहता हूँ।"],
  ["Content", "/kɔ̃.tɑ̃/", "adjectif", "Happy; pleased", "खुश; संतुष्ट", "A1", "Adjectives", "Je suis très content de vous voir.", "I am very glad to see you.", "आपको देखकर मुझे बहुत खुशी हुई।"],
  ["Fatigué", "/fa.ti.ɡe/", "adjectif", "Tired", "थका हुआ", "A1", "Adjectives", "Après le voyage, nous sommes fatigués.", "After the trip, we are tired.", "यात्रा के बाद हम थके हुए हैं।"],
  ["Difficile", "/di.fi.sil/", "adjectif", "Difficult", "कठिन", "A2", "Adjectives", "Le subjonctif est difficile mais logique.", "The subjunctive is difficult but logical.", "सब्जंक्टिव कठिन है लेकिन तर्कसंगत है।"],
  ["Facile", "/fa.sil/", "adjectif", "Easy", "आसान", "A2", "Adjectives", "Cet exercice est facile.", "This exercise is easy.", "यह अभ्यास आसान है।"],

  // Connectors & useful phrases (A2–B1)
  ["Parce que", "/paʁs kə/", "conjonction", "Because", "क्योंकि", "A2", "Connectors", "J’étudie parce que j’aime la langue.", "I study because I love the language.", "मैं पढ़ता हूँ क्योंकि मुझे यह भाषा पसंद है।"],
  ["Mais", "/mɛ/", "conjonction", "But", "लेकिन", "A1", "Connectors", "Je suis fatigué, mais heureux.", "I am tired, but happy.", "मैं थका हूँ, लेकिन खुश हूँ।"],
  ["Pourtant", "/puʁ.tɑ̃/", "adverbe", "Yet; and yet", "फिर भी", "B1", "Connectors", "Il pleuvait, pourtant nous sommes sortis.", "It was raining, yet we went out.", "बारिश हो रही थी, फिर भी हम बाहर निकले।"],
  ["Cependant", "/sə.pɑ̃.dɑ̃/", "adverbe", "However", "तथापि; हालाँकि", "B1", "Connectors", "Le plan est bon ; cependant, il coûte cher.", "The plan is good; however, it is expensive.", "योजना अच्छी है; हालाँकि, यह महँगी है।"],
  ["Davantage", "/da.vɑ̃.taʒ/", "adverbe", "More", "और अधिक", "B1", "Connectors", "Je voudrais en savoir davantage.", "I would like to know more.", "मैं और अधिक जानना चाहूँगा।"],
  ["Il faut", "/il fo/", "locution verbale", "One must; it is necessary to", "ज़रूरी है; करना चाहिए", "A2", "Connectors", "Il faut pratiquer chaque jour.", "You have to practise every day.", "हर दिन अभ्यास करना ज़रूरी है।"],
  ["Peut-être", "/pø.tɛtʁ/", "adverbe", "Maybe; perhaps", "शायद", "A2", "Connectors", "Peut-être qu’il viendra demain.", "Maybe he will come tomorrow.", "शायद वह कल आएगा।"],
  ["Bientôt", "/bjɛ̃.to/", "adverbe", "Soon", "जल्द ही", "A2", "Connectors", "À bientôt !", "See you soon!", "जल्द मिलेंगे!"],
];

export const coreVocabulary: VocabularyWord[] = [...rows, ...moreRows].map(
  ([french, ipa, pos, en, hi, level, category, exFr, exEn, exHi], i) => ({
    id: 100 + i,
    word: french,
    french_word: french,
    ipa,
    ipa_phonetic: ipa,
    part_of_speech: pos,
    meaning: en,
    meaning_en: en,
    meaning_hi: hi,
    level,
    cefr_level: level,
    category,
    example_fr: exFr,
    example_sentence: exFr,
    example_en: exEn,
    example_hi: exHi,
    use_case: `Everyday ${category.toLowerCase()} vocabulary at level ${level}.`,
    source: "Core curriculum",
  })
);

/* ------------------------------------------------------------------ */
/* Additional lessons                                                 */
/* ------------------------------------------------------------------ */

interface LessonInput {
  id: number;
  course_id: number;
  order: number;
  title: string;
  title_en: string;
  section: string;
  mins: number;
  fr: string;
  en: string;
  audio?: string;
  question: string;
  question_en: string;
  options: string[];
  correct: number;
  explanation: string;
}

const lesson = (l: LessonInput): Lesson => ({
  id: l.id,
  course_id: l.course_id,
  order: l.order,
  order_num: l.order,
  title: l.title,
  title_en: l.title_en,
  section_title: l.section,
  duration_mins: l.mins,
  french_text: l.fr,
  content_fr: l.fr,
  english_translation: l.en,
  content_en: l.en,
  audio_phrase: l.audio ?? l.fr,
  quiz_question: l.question,
  quiz_options_json: l.options,
  quiz_correct_index: l.correct,
  quiz: {
    question: l.question,
    question_en: l.question_en,
    options: l.options,
    correct_index: l.correct,
    explanation: l.explanation,
  },
});

export const extraLessons: Lesson[] = [
  // ---- A1 (course 1) ----
  lesson({
    id: 16, course_id: 1, order: 4,
    title: "Être et Avoir : les deux verbes essentiels", title_en: "Être and Avoir: The Two Essential Verbs",
    section: "Core verbs", mins: 15,
    fr: "Je suis étudiant. Tu es fatigué. Elle a vingt ans. Nous avons un chat.",
    en: "I am a student. You are tired. She is twenty years old. We have a cat.",
    question: "Comment dit-on « We have a cat » ?", question_en: "How do you say “We have a cat”?",
    options: ["Nous sommes un chat", "Nous avons un chat", "Nous allons un chat", "Nous faisons un chat"], correct: 1,
    explanation: "« Avoir » means to have: nous avons. « Être » (to be) gives nous sommes, which would describe what we are, not what we own.",
  }),
  lesson({
    id: 17, course_id: 1, order: 5,
    title: "Le, la, un, une : le genre des noms", title_en: "Le, La, Un, Une: Gender of Nouns",
    section: "Articles", mins: 15,
    fr: "Le livre est sur la table. Une amie achète un café et une baguette.",
    en: "The book is on the table. A friend buys a coffee and a baguette.",
    question: "Which article goes with « baguette » (feminine)?", question_en: "Which article goes with “baguette” (feminine)?",
    options: ["un", "une", "le", "des"], correct: 1,
    explanation: "Baguette is feminine, so the indefinite article is « une » (une baguette) and the definite article is « la ».",
  }),
  lesson({
    id: 18, course_id: 1, order: 6,
    title: "Les nombres, les jours et l’heure", title_en: "Numbers, Days and Telling the Time",
    section: "Numbers & time", mins: 18,
    fr: "Il est huit heures et demie. Aujourd’hui, c’est lundi. Le train part à dix-sept heures quinze.",
    en: "It is half past eight. Today is Monday. The train leaves at 17:15.",
    question: "Que signifie « huit heures et demie » ?", question_en: "What does “huit heures et demie” mean?",
    options: ["8:15", "8:30", "8:45", "9:30"], correct: 1,
    explanation: "« Et demie » means “and a half”, so huit heures et demie is 8:30. « Et quart » is a quarter past.",
  }),
  lesson({
    id: 19, course_id: 1, order: 7,
    title: "Présenter sa famille", title_en: "Introducing Your Family",
    section: "Family & possession", mins: 15,
    fr: "Voici ma famille : mon père, ma mère, mon frère et ma sœur. Ils habitent à Lyon.",
    en: "This is my family: my father, my mother, my brother and my sister. They live in Lyon.",
    question: "Which possessive goes with « sœur »?", question_en: "Which possessive goes with “sœur”?",
    options: ["mon", "ma", "mes", "ton"], correct: 1,
    explanation: "Possessives agree with the noun: « ma » for feminine singular (ma sœur), « mon » for masculine (mon frère), « mes » for plurals.",
  }),

  // ---- A2 (course 2) ----
  lesson({
    id: 20, course_id: 2, order: 4,
    title: "Demander son chemin", title_en: "Asking for Directions",
    section: "Getting around", mins: 18,
    fr: "Excusez-moi, pour aller à la gare ? Continuez tout droit, puis tournez à gauche après la pharmacie.",
    en: "Excuse me, how do I get to the station? Keep going straight, then turn left after the pharmacy.",
    question: "Que veut dire « tout droit » ?", question_en: "What does “tout droit” mean?",
    options: ["To the left", "To the right", "Straight ahead", "Behind you"], correct: 2,
    explanation: "« Tout droit » means straight ahead. « À gauche » is left and « à droite » is right.",
  }),
  lesson({
    id: 21, course_id: 2, order: 5,
    title: "Faire les courses et les prix", title_en: "Shopping and Prices",
    section: "Everyday errands", mins: 16,
    fr: "Combien coûte ce fromage ? Il coûte douze euros le kilo. Je prends deux cents grammes, s’il vous plaît.",
    en: "How much does this cheese cost? It costs twelve euros a kilo. I will take two hundred grams, please.",
    question: "Comment demande-t-on le prix ?", question_en: "How do you ask for the price?",
    options: ["Combien ça coûte ?", "Comment ça va ?", "Où est-ce ?", "Quand ça ferme ?"], correct: 0,
    explanation: "« Combien ça coûte ? » (or « C’est combien ? ») asks how much something costs. « Comment ça va ? » asks how you are.",
  }),
  lesson({
    id: 22, course_id: 2, order: 6,
    title: "Ma journée : les verbes pronominaux", title_en: "My Day: Reflexive Verbs",
    section: "Daily routine", mins: 18,
    fr: "Je me réveille à sept heures, je me lève, puis je me prépare. Le soir, nous nous couchons tôt.",
    en: "I wake up at seven, get up, then get ready. In the evening, we go to bed early.",
    question: "Complete: « Nous ___ couchons tôt. »", question_en: "Complete: “Nous ___ couchons tôt.”",
    options: ["me", "te", "nous", "vous"], correct: 2,
    explanation: "Reflexive pronouns match the subject: je me, tu te, il se, nous nous, vous vous, ils se.",
  }),

  // ---- B1 (course 3) ----
  lesson({
    id: 23, course_id: 3, order: 4,
    title: "Parler de l’avenir : le futur simple", title_en: "Talking About the Future: Le Futur Simple",
    section: "Future tense", mins: 18,
    fr: "Demain, nous partirons tôt. Tu verras, le voyage sera magnifique et nous visiterons beaucoup de villes.",
    en: "Tomorrow, we will leave early. You will see, the trip will be wonderful and we will visit many towns.",
    question: "Which is the futur simple of « nous partons »?", question_en: "Which is the futur simple of “nous partons”?",
    options: ["nous partirons", "nous sommes partis", "nous partions", "nous partons"], correct: 0,
    explanation: "The futur simple is built on the infinitive plus endings: partir + ons = nous partirons.",
  }),
  lesson({
    id: 24, course_id: 3, order: 5,
    title: "Écrire un courriel professionnel", title_en: "Writing a Professional Email",
    section: "Written French", mins: 20,
    fr: "Madame, Monsieur, je me permets de vous contacter au sujet de votre annonce. Dans l’attente de votre réponse, je vous prie d’agréer mes salutations distinguées.",
    en: "Dear Sir or Madam, I am writing to you regarding your advertisement. Awaiting your reply, please accept my best regards.",
    question: "Which closing is the most formal?", question_en: "Which closing is the most formal?",
    options: ["Bisous", "À plus", "Je vous prie d’agréer mes salutations distinguées", "Ciao"], correct: 2,
    explanation: "« Je vous prie d’agréer mes salutations distinguées » is the standard formal sign-off in French business correspondence.",
  }),
];
