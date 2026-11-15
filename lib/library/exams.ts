import type { Level, Question } from "./types";
import { q } from "./gh.ts";

/**
 * French exam knowledge base: TEF Canada, TCF Canada, DELF/DALF, plus NCLC (Canadian) conversion.
 * Score bands are published by IRCC / France Éducation international / CCI Paris Île-de-France.
 * Formats change over time: always confirm on the official site before booking.
 */

export type Skill = "CO" | "CE" | "EO" | "EE";
export const SKILL_LABEL: Record<Skill, { fr: string; en: string; hi: string }> = {
  CO: { fr: "Compréhension orale", en: "Listening", hi: "सुनना" },
  CE: { fr: "Compréhension écrite", en: "Reading", hi: "पढ़ना" },
  EO: { fr: "Expression orale", en: "Speaking", hi: "बोलना" },
  EE: { fr: "Expression écrite", en: "Writing", hi: "लिखना" },
};

export interface ExamPart {
  skill: Skill;
  duration: string;
  detail: string;
  detail_hi: string;
}

export interface ExamGuide {
  id: string;
  name: string;
  tagline: string;
  tagline_hi: string;
  levels: string;
  usedFor: string[];
  parts: ExamPart[];
  scoring: string;
  tips: string[];
  tips_hi: string[];
}

export const examGuides: ExamGuide[] = [
  {
    id: "tef-canada", name: "TEF Canada", tagline: "The most common test for Canadian immigration (Express Entry) and citizenship.",
    tagline_hi: "कनाडा इमिग्रेशन (Express Entry) और नागरिकता के लिए सबसे आम परीक्षा।",
    levels: "A1 → C2 (results mapped to NCLC 1-12)", usedFor: ["Express Entry", "Quebec immigration", "Citizenship"],
    parts: [
      { skill: "CO", duration: "≈ 40 min", detail: "60 questions, audio played once. Short dialogues, announcements, interviews. Difficulty rises as you go.", detail_hi: "60 सवाल, ऑडियो सिर्फ़ एक बार चलता है। सवाल धीरे-धीरे कठिन होते जाते हैं।" },
      { skill: "CE", duration: "≈ 60 min", detail: "50 questions: ads, notices, articles. Reading speed matters: ~1 minute per question.", detail_hi: "50 सवाल: विज्ञापन, सूचनाएँ, लेख। गति ज़रूरी है: लगभग 1 मिनट प्रति सवाल।" },
      { skill: "EE", duration: "60 min", detail: "Section A: continue a news item (min. 80 words). Section B: formal letter giving an opinion (min. 200 words).", detail_hi: "भाग A: किसी समाचार को आगे बढ़ाएँ (कम से कम 80 शब्द)। भाग B: राय देने वाला औपचारिक पत्र (कम से कम 200 शब्द)।" },
      { skill: "EO", duration: "15 min", detail: "Section A: ask questions about an ad to get information (≈ 5 min). Section B: convince a friend / argue a point (≈ 10 min).", detail_hi: "भाग A: विज्ञापन के बारे में सवाल पूछें। भाग B: दोस्त को मनाएँ / अपनी बात तर्क से रखें।" },
    ],
    scoring: "Each skill scored out of 360 (CO), 300 (CE), 450 (EO and EE). Each score maps to its own NCLC level.",
    tips: ["In listening, read the options BEFORE the audio and write numbers quickly.", "Writing B: use a formal opening and closing (Madame, Monsieur … Veuillez agréer…).", "Speaking A: prepare 8-10 question openers: Pouvez-vous me dire… ? Est-ce qu’il est possible de… ?"],
    tips_hi: ["सुनने में ऑडियो से पहले विकल्प पढ़ें और संख्याएँ जल्दी लिखें।", "औपचारिक पत्र में शुरुआत और अंत के नियम पक्के करें।", "बोलने में सवाल पूछने के 8-10 वाक्य रट लें।"],
  },
  {
    id: "tcf-canada", name: "TCF Canada", tagline: "Another IRCC-accepted test with multiple-choice listening and reading.",
    tagline_hi: "IRCC द्वारा मान्य दूसरी परीक्षा; सुनना और पढ़ना बहुविकल्पीय होता है।",
    levels: "A1 → C2 (results mapped to NCLC)", usedFor: ["Express Entry", "Citizenship", "University"],
    parts: [
      { skill: "CO", duration: "35 min", detail: "39 questions, audio played once, difficulty rises (A1 to C2).", detail_hi: "39 सवाल, ऑडियो एक बार, कठिनाई बढ़ती जाती है।" },
      { skill: "CE", duration: "60 min", detail: "39 questions on texts of increasing difficulty.", detail_hi: "39 सवाल, पाठ क्रमशः कठिन।" },
      { skill: "EE", duration: "60 min", detail: "3 tasks: short message (60-120 words), article/blog (120-150 words), argumentative text with two viewpoints (120-180 words).", detail_hi: "3 कार्य: छोटा संदेश, लेख/ब्लॉग, दो पक्षों वाला तर्क-पाठ।" },
      { skill: "EO", duration: "≈ 12 min", detail: "3 tasks: guided interview (no preparation), role-play (2 min preparation), express and defend an opinion.", detail_hi: "3 कार्य: साक्षात्कार, रोल-प्ले, अपनी राय का बचाव।" },
    ],
    scoring: "Listening and reading scored out of 699; writing and speaking scored out of 20. Each maps to NCLC.",
    tips: ["Wrong answers are not penalised: always answer every question.", "Time yourself: 90 seconds per reading question maximum.", "Writing: respect the word limit of each task."],
    tips_hi: ["गलत जवाब पर नंबर नहीं कटते — हर सवाल का जवाब दें।", "समय का हिसाब रखें।", "हर कार्य की शब्द-सीमा का पालन करें।"],
  },
  {
    id: "delf-dalf", name: "DELF / DALF", tagline: "Lifelong diplomas from the French Ministry of Education, one per CEFR level.",
    tagline_hi: "फ़्रांस के शिक्षा मंत्रालय के आजीवन वैध डिप्लोमा, हर CEFR स्तर के लिए अलग।",
    levels: "DELF A1, A2, B1, B2 · DALF C1, C2", usedFor: ["Study in France", "Visa / residence", "Career"],
    parts: [
      { skill: "CO", duration: "20-40 min", detail: "Listen to 2-3 recordings and answer questions (heard twice at A1-B2).", detail_hi: "2-3 रिकॉर्डिंग सुनकर जवाब दें।" },
      { skill: "CE", duration: "30-60 min", detail: "Read documents and answer comprehension questions.", detail_hi: "पाठ पढ़कर सवालों के जवाब।" },
      { skill: "EE", duration: "45-90 min", detail: "Write letters, essays or an argued synthesis according to the level.", detail_hi: "स्तर के अनुसार पत्र, निबंध या संश्लेषण।" },
      { skill: "EO", duration: "6-30 min", detail: "Guided talk, monologue and exchange with the examiner.", detail_hi: "परीक्षक के साथ बातचीत और एकालाप।" },
    ],
    scoring: "Each skill out of 25; total out of 100. Pass = 50/100 with at least 5/25 in every skill.",
    tips: ["B2 (DELF) is roughly NCLC 7-8: the best target for most visa and study goals.", "DELF diplomas never expire."],
    tips_hi: ["DELF B2 लगभग NCLC 7-8 के बराबर है।", "DELF डिप्लोमा कभी एक्सपायर नहीं होता।"],
  },
];

export interface NclcRow {
  nclc: number;
  cefr: string;
  tefCO: string; tefCE: string; tefEE: string; tefEO: string;
  tcfCO: string; tcfCE: string; tcfEE: string; tcfEO: string;
  note: string;
  note_hi: string;
}

/** Published TEF Canada / TCF Canada → NCLC conversion bands. Verify on canada.ca before relying on them. */
export const nclcTable: NclcRow[] = [
  { nclc: 4, cefr: "A2", tefCO: "145-180", tefCE: "121-150", tefEE: "181-225", tefEO: "181-225", tcfCO: "331-368", tcfCE: "342-374", tcfEE: "4-5", tcfEO: "4-5", note: "Basic survival French.", note_hi: "बुनियादी ज़रूरत भर की फ़्रेंच।" },
  { nclc: 5, cefr: "B1", tefCO: "181-216", tefCE: "151-180", tefEE: "226-270", tefEO: "226-270", tcfCO: "369-397", tcfCE: "375-405", tcfEE: "6", tcfEO: "6", note: "Handles everyday topics.", note_hi: "रोज़मर्रा के विषय संभाल लेता है।" },
  { nclc: 6, cefr: "B1+", tefCO: "217-248", tefCE: "181-206", tefEE: "271-309", tefEO: "271-309", tcfCO: "398-457", tcfCE: "406-452", tcfEE: "7-9", tcfEO: "7-9", note: "Independent user; many Quebec streams start here.", note_hi: "स्वतंत्र उपयोगकर्ता; कई क्यूबेक स्ट्रीम यहीं से शुरू।" },
  { nclc: 7, cefr: "B2", tefCO: "249-279", tefCE: "207-232", tefEE: "310-348", tefEO: "310-348", tcfCO: "458-502", tcfCE: "453-498", tcfEE: "10-11", tcfEO: "10-11", note: "Key Express Entry French threshold (bonus points).", note_hi: "Express Entry का मुख्य फ़्रेंच लक्ष्य (बोनस अंक)।" },
  { nclc: 8, cefr: "B2+", tefCO: "280-297", tefCE: "233-247", tefEE: "349-373", tefEO: "349-373", tcfCO: "503-522", tcfCE: "499-523", tcfEE: "12-13", tcfEO: "12-13", note: "Strong professional French.", note_hi: "मज़बूत पेशेवर फ़्रेंच।" },
  { nclc: 9, cefr: "C1", tefCO: "298-315", tefCE: "248-262", tefEE: "374-397", tefEO: "374-397", tcfCO: "523-548", tcfCE: "524-548", tcfEE: "14-15", tcfEO: "14-15", note: "Advanced user.", note_hi: "उन्नत उपयोगकर्ता।" },
  { nclc: 10, cefr: "C1+", tefCO: "316-360", tefCE: "263-300", tefEE: "398-450", tefEO: "398-450", tcfCO: "549-699", tcfCE: "549-699", tcfEE: "16-20", tcfEO: "16-20", note: "Maximum points for French.", note_hi: "फ़्रेंच के अधिकतम अंक।" },
];

export interface RoadmapStep {
  stage: number;
  level: Level;
  nclc: string;
  title: string;
  title_hi: string;
  hours: string;
  goals: string[];
  goals_hi: string[];
  doNow: string[];
}

export const roadmap: RoadmapStep[] = [
  { stage: 1, level: "A1", nclc: "NCLC 1-3", title: "Survive and introduce yourself", title_hi: "शुरुआत: परिचय और ज़रूरी बातें", hours: "60-80 h",
    goals: ["Greet, introduce yourself, numbers, time", "être/avoir, -er verbs, articles", "300-500 core words"], goals_hi: ["अभिवादन, परिचय, संख्याएँ, समय", "être/avoir, -er क्रियाएँ, articles", "300-500 मूल शब्द"],
    doNow: ["Do grammar topics A1", "Flashcards: 10 words/day", "Listen to slow audio daily"] },
  { stage: 2, level: "A2", nclc: "NCLC 4", title: "Daily life and the past", title_hi: "रोज़मर्रा की ज़िंदगी और अतीत", hours: "80-100 h",
    goals: ["Passé composé, futur proche, pronouns", "Shop, travel, describe routine", "800-1,200 words"], goals_hi: ["passé composé, सर्वनाम", "ख़रीदारी, यात्रा, दिनचर्या", "800-1200 शब्द"],
    doNow: ["Grammar A2 + quizzes", "Translate drill A2", "Practise TEF speaking Section A questions"] },
  { stage: 3, level: "B1", nclc: "NCLC 5-6", title: "Independent speaker", title_hi: "स्वतंत्र वक्ता", hours: "120-150 h",
    goals: ["Imparfait vs passé composé, conditionnel, si-clauses", "Express opinions with connectors", "2,000+ words"], goals_hi: ["imparfait, conditionnel, si-वाक्य", "connectors से राय देना", "2000+ शब्द"],
    doNow: ["Grammar B1 topics", "Write a 120-word message every day", "First timed practice test"] },
  { stage: 4, level: "B2", nclc: "NCLC 7-8", title: "Target for Express Entry", title_hi: "लक्ष्य: Express Entry (NCLC 7+)", hours: "150-200 h",
    goals: ["Subjonctif, relatifs composés, concession", "Write 200-word formal letters", "Understand news and interviews at normal speed", "3,500+ words"], goals_hi: ["subjonctif, relatifs composés", "200 शब्द का औपचारिक पत्र", "सामान्य गति की ख़बरें समझना", "3500+ शब्द"],
    doNow: ["Full TEF/TCF mock tests every week", "Shadow podcasts / RFI / TV5Monde", "Record yourself on speaking Task 3"] },
  { stage: 5, level: "C1", nclc: "NCLC 9-10", title: "Advanced mastery beyond NCLC 7", title_hi: "NCLC 7 से आगे: उन्नत महारत", hours: "200+ h",
    goals: ["Nuanced argumentation, register control", "Stylistic inversion, passé simple recognition", "Read Le Monde, Radio-Canada, literature", "5,000+ words"], goals_hi: ["तर्क की बारीकियाँ, भाषा-स्तर पर पकड़", "साहित्य और अख़बार पढ़ना", "5000+ शब्द"],
    doNow: ["C1 grammar + argumentation toolbox", "Weekly opinion essays with feedback", "Debate a topic aloud for 5 minutes"] },
];

export interface ExamPractice {
  id: string;
  skill: "CE" | "CO";
  level: Level;
  nclc: string;
  title: string;
  title_en: string;
  /** Text to read (CE) or audio script spoken by text-to-speech (CO). */
  fr: string;
  en: string;
  hi: string;
  questions: Question[];
}

export const practice: ExamPractice[] = [
  {
    id: "ce-a2-affiche", skill: "CE", level: "A2", nclc: "NCLC 4", title: "Affiche : cours de cuisine", title_en: "Poster: cooking class",
    fr: "Cours de cuisine française\nTous les samedis, de 10 h à 12 h, au centre culturel de la rue Victor-Hugo. Prix : 25 € par personne. Apportez un tablier. Inscription obligatoire avant le jeudi.",
    en: "French cooking class. Every Saturday, 10 am to 12 pm, at the cultural centre on Victor-Hugo Street. Price: €25 per person. Bring an apron. Registration required before Thursday.",
    hi: "फ़्रेंच खाना पकाने की क्लास। हर शनिवार सुबह 10 से 12 बजे, विक्टर-ह्यूगो गली के सांस्कृतिक केंद्र में। कीमत: 25 € प्रति व्यक्ति। एप्रन लाएँ। गुरुवार से पहले पंजीकरण ज़रूरी।",
    questions: [q("Quand a lieu le cours ?", ["Le jeudi matin", "Le samedi matin", "Le samedi soir"], 1, "Tous les samedis, 10 h-12 h."), q("Qu’est-ce qu’on doit apporter ?", ["Un tablier", "Des légumes", "Un livre"], 0, "Apportez un tablier."), q("Avant quand faut-il s’inscrire ?", ["Avant samedi", "Avant jeudi", "Avant lundi"], 1, "Inscription avant le jeudi.")],
  },
  {
    id: "ce-b1-article", skill: "CE", level: "B1", nclc: "NCLC 5-6", title: "Article : le télétravail", title_en: "Article: remote work",
    fr: "Depuis la pandémie, le télétravail s’est installé dans de nombreuses entreprises. Les salariés apprécient de gagner du temps sur les trajets et de mieux organiser leur journée. Pourtant, certains se plaignent de l’isolement et de la difficulté à séparer vie privée et vie professionnelle. Beaucoup d’employeurs choisissent donc un système hybride : deux ou trois jours à la maison, le reste au bureau.",
    en: "Since the pandemic, remote work has become established in many companies. Employees appreciate saving commute time and organising their day better. However, some complain about isolation and difficulty separating private and professional life. Many employers therefore choose a hybrid system: two or three days at home, the rest at the office.",
    hi: "महामारी के बाद कई कंपनियों में घर से काम आम हो गया। कर्मचारियों को यात्रा का समय बचना पसंद है, पर कुछ अकेलेपन की शिकायत करते हैं। इसलिए कई नियोक्ता हाइब्रिड व्यवस्था चुनते हैं: 2-3 दिन घर, बाकी दफ़्तर।",
    questions: [q("Quel est un avantage du télétravail ?", ["Gagner du temps", "Gagner plus d’argent", "Voir ses collègues"], 0, "Gagner du temps sur les trajets."), q("Quel problème est mentionné ?", ["Le bruit", "L’isolement", "Le salaire"], 1, "L’isolement."), q("Que choisissent beaucoup d’employeurs ?", ["Un système hybride", "Le bureau uniquement", "Le télétravail total"], 0, "Un système hybride.")],
  },
  {
    id: "ce-b2-opinion", skill: "CE", level: "B2", nclc: "NCLC 7", title: "Opinion : réduire la voiture en ville", title_en: "Opinion: reducing cars in cities",
    fr: "Interdire les voitures au centre-ville ne résoudra pas, à lui seul, le problème de la pollution. Certes, l’air y serait plus pur, mais encore faut-il que les transports en commun soient assez fréquents et abordables. Faute de quoi, les habitants des banlieues, qui n’ont souvent pas d’alternative, se sentiraient pénalisés. Il importe donc d’accompagner toute mesure restrictive d’un investissement massif dans les bus, le métro et les pistes cyclables.",
    en: "Banning cars from the city centre will not, on its own, solve pollution. Admittedly the air would be cleaner, but public transport must be frequent and affordable. Otherwise suburban residents, who often have no alternative, would feel penalised. Any restrictive measure should therefore come with massive investment in buses, metro and bike lanes.",
    hi: "शहर के केंद्र में गाड़ियों पर रोक लगाना अकेले प्रदूषण का हल नहीं है। हवा तो साफ़ होगी, पर सार्वजनिक परिवहन बार-बार चलने वाला और सस्ता होना चाहिए, वरना उपनगर वाले खुद को दंडित महसूस करेंगे। इसलिए हर पाबंदी के साथ बस, मेट्रो और साइकिल लेन में बड़ा निवेश चाहिए।",
    questions: [q("Quelle est la thèse de l’auteur ?", ["L’interdiction suffit", "L’interdiction ne suffit pas seule", "Il faut plus de voitures"], 1, "Elle ne résoudra pas à elle seule."), q("« Faute de quoi » signifie :", ["Sinon", "Parce que", "Ensuite"], 0, "Faute de quoi = otherwise."), q("Que demande-t-il ?", ["Moins de bus", "Plus d’investissement dans les transports", "Plus de parkings"], 1, "Investissement massif.")],
  },
  {
    id: "ce-c1-editorial", skill: "CE", level: "C1", nclc: "NCLC 9-10", title: "Éditorial : l’intelligence artificielle", title_en: "Editorial: artificial intelligence",
    fr: "Qu’on s’en réjouisse ou qu’on le déplore, l’intelligence artificielle s’invite désormais dans la plupart des métiers. Si elle promet des gains de productivité substantiels, elle suscite aussi des inquiétudes légitimes quant à la place de l’humain. Reste à savoir si les pouvoirs publics sauront encadrer ces technologies sans en brider l’essor. Toujours est-il que l’éducation, plus que jamais, devra préparer chacun à travailler avec ces outils plutôt que contre eux.",
    en: "Whether we welcome or deplore it, artificial intelligence is now entering most professions. While promising substantial productivity gains, it raises legitimate concerns about the human role. It remains to be seen whether public authorities can regulate without stifling growth. Still, education must now prepare everyone to work with these tools, not against them.",
    hi: "चाहे स्वागत करें या अफ़सोस, AI अब ज़्यादातर पेशों में आ चुका है। यह उत्पादकता बढ़ाता है पर इंसान की जगह को लेकर चिंता भी पैदा करता है। देखना है कि सरकारें बिना विकास रोके नियमन कर पाती हैं या नहीं। शिक्षा को लोगों को इन औज़ारों के साथ काम करना सिखाना होगा।",
    questions: [q("Quelle est la position de l’auteur ?", ["Rejet total", "Nuancée : promesses et risques", "Enthousiasme sans réserve"], 1, "Il met en balance les gains et les inquiétudes."), q("« Brider l’essor » signifie :", ["Freiner le développement", "Accélérer", "Financer"], 0, "Brider = to restrain."), q("Quel rôle pour l’éducation ?", ["Interdire ces outils", "Préparer à travailler avec eux", "Ne rien changer"], 1, "Travailler avec plutôt que contre.")],
  },
  {
    id: "co-a2-gare", skill: "CO", level: "A2", nclc: "NCLC 4", title: "Annonce en gare", title_en: "Station announcement",
    fr: "Mesdames et messieurs, le train à destination de Lyon, prévu à quatorze heures trente, partira avec vingt minutes de retard, voie numéro douze. Nous vous prions de nous excuser pour ce désagrément.",
    en: "Ladies and gentlemen, the train to Lyon, scheduled for 2:30 pm, will leave 20 minutes late from platform 12. We apologise for the inconvenience.",
    hi: "देवियो और सज्जनो, लियों जाने वाली ट्रेन जो 14:30 पर थी, 20 मिनट देरी से प्लेटफ़ॉर्म 12 से जाएगी। असुविधा के लिए खेद है।",
    questions: [q("Quelle est la destination ?", ["Lyon", "Lille", "Nice"], 0, "Lyon."), q("Quel est le retard ?", ["10 minutes", "20 minutes", "30 minutes"], 1, "Vingt minutes."), q("Quelle voie ?", ["2", "12", "20"], 1, "Voie douze.")],
  },
  {
    id: "co-b1-message", skill: "CO", level: "B1", nclc: "NCLC 5-6", title: "Message vocal", title_en: "Voicemail",
    fr: "Bonjour Claire, c’est Marc. Je t’appelle parce que la réunion de demain est reportée à jeudi, à dix heures, en salle B. Comme ma voiture est au garage, je viendrai en métro. Peux-tu apporter les documents du client ? Merci et à bientôt !",
    en: "Hello Claire, it’s Marc. The meeting tomorrow is postponed to Thursday at ten, in room B. As my car is in the garage I’ll come by metro. Can you bring the client documents? Thanks and see you soon!",
    hi: "हैलो क्लेयर, मैं मार्क हूँ। कल की मीटिंग गुरुवार को दस बजे कमरा B में होगी। मेरी गाड़ी गैराज में है, इसलिए मैं मेट्रो से आऊँगा। क्या तुम क्लाइंट के दस्तावेज़ ला सकती हो?",
    questions: [q("Quand a lieu la réunion ?", ["Demain", "Jeudi", "Vendredi"], 1, "Reportée à jeudi."), q("Pourquoi Marc vient-il en métro ?", ["Sa voiture est au garage", "Il aime le métro", "Il n’a pas de voiture"], 0, "Sa voiture est au garage."), q("Que doit apporter Claire ?", ["Un café", "Les documents du client", "Son ordinateur"], 1, "Les documents du client.")],
  },
  {
    id: "co-b2-radio", skill: "CO", level: "B2", nclc: "NCLC 7", title: "Interview radio : le bénévolat", title_en: "Radio interview: volunteering",
    fr: "Selon une étude récente, près d’un Français sur quatre s’engage dans une association. Ce qui motive les bénévoles, ce n’est pas seulement l’envie d’aider : c’est aussi le désir d’apprendre de nouvelles compétences et de créer des liens. Cependant, de plus en plus d’associations peinent à recruter des jeunes, qui manquent de temps à cause de leurs études.",
    en: "According to a recent study, nearly one French person in four volunteers in an association. Volunteers are motivated not only by the wish to help but also to learn skills and make connections. However, more associations struggle to recruit young people, who lack time because of their studies.",
    hi: "एक अध्ययन के अनुसार हर चार में से लगभग एक फ़्रेंच व्यक्ति किसी संस्था में स्वयंसेवा करता है। प्रेरणा सिर्फ़ मदद नहीं, कौशल सीखना और संबंध बनाना भी है। पर संस्थाओं को युवा नहीं मिल रहे क्योंकि उनके पास समय कम है।",
    questions: [q("Quelle proportion de Français est bénévole ?", ["1 sur 4", "1 sur 10", "1 sur 2"], 0, "Près d’un sur quatre."), q("Quelle motivation est citée ?", ["L’argent", "Apprendre de nouvelles compétences", "Voyager"], 1, "Compétences et liens."), q("Quel est le problème pour les associations ?", ["Recruter des jeunes", "Trouver des salles", "Payer les salariés"], 0, "Peinent à recruter des jeunes.")],
  },
];

export interface WritingTask {
  id: string;
  exam: "TEF" | "TCF" | "DELF";
  level: Level;
  nclc: string;
  title: string;
  prompt: string;
  prompt_hi: string;
  minWords: number;
  model: string;
  model_en: string;
  checklist: string[];
}

export const writingTasks: WritingTask[] = [
  {
    id: "ee-a2-message", exam: "TCF", level: "A2", nclc: "NCLC 4", title: "Message à un ami", minWords: 60,
    prompt: "Vous avez un nouvel appartement. Écrivez un message à un ami pour l’inviter à une fête samedi. Décrivez l’appartement et dites ce qu’il doit apporter.",
    prompt_hi: "आपका नया फ़्लैट है। दोस्त को शनिवार की पार्टी का निमंत्रण लिखें, फ़्लैट का वर्णन करें और बताएँ वह क्या लाए।",
    model: "Salut Paul,\nJ’ai enfin un nouvel appartement ! Il est petit mais lumineux, avec un grand balcon. Pour fêter ça, j’organise une fête samedi à 19 h. Peux-tu venir ? Apporte une boisson et ta bonne humeur !\nÀ bientôt,\nSara",
    model_en: "Hi Paul, I finally have a new flat! It is small but bright, with a large balcony. To celebrate I’m having a party Saturday at 7 pm. Can you come? Bring a drink and your good mood! See you soon, Sara",
    checklist: ["Greeting + closing", "Invitation (date, time)", "Description (2 adjectives)", "Instruction with the imperative"],
  },
  {
    id: "ee-b1-reclamation", exam: "TEF", level: "B1", nclc: "NCLC 5-6", title: "Lettre de réclamation", minWords: 120,
    prompt: "Vous avez acheté un ordinateur qui ne fonctionne pas. Écrivez au service client pour expliquer le problème et demander une solution.",
    prompt_hi: "आपने जो कंप्यूटर खरीदा वह काम नहीं करता। ग्राहक-सेवा को समस्या बताते हुए पत्र लिखें और समाधान माँगें।",
    model: "Madame, Monsieur,\nJe vous écris au sujet de l’ordinateur que j’ai acheté le 3 mars dans votre magasin. Dès la première utilisation, l’écran s’est éteint plusieurs fois et la batterie ne tient pas plus de vingt minutes. J’ai pourtant suivi toutes les instructions.\nC’est pourquoi je vous demande de bien vouloir remplacer l’appareil ou de me rembourser. Vous trouverez ci-joint la facture.\nDans l’attente de votre réponse, je vous prie d’agréer, Madame, Monsieur, mes salutations distinguées.\nA. Kumar",
    model_en: "I am writing regarding the computer I bought on 3 March in your shop. From first use the screen turned off several times and the battery lasts only twenty minutes… I therefore ask you to replace or refund it. Invoice attached. Yours faithfully.",
    checklist: ["Formal opening/closing", "Facts: date, problem, what you tried", "Clear request (remplacer / rembourser)", "Connectors: pourtant, c’est pourquoi"],
  },
  {
    id: "ee-b2-opinion", exam: "TEF", level: "B2", nclc: "NCLC 7", title: "Lettre d’opinion : les réseaux sociaux", minWords: 200,
    prompt: "Un journal local demande l’avis des lecteurs : les réseaux sociaux sont-ils bons pour les jeunes ? Écrivez une lettre (200 mots minimum) pour donner et justifier votre opinion.",
    prompt_hi: "स्थानीय अख़बार पूछता है: क्या सोशल मीडिया युवाओं के लिए अच्छा है? कम से कम 200 शब्दों में अपनी राय तर्क सहित लिखें।",
    model: "Madame, Monsieur,\nEn réponse à votre question, je pense que les réseaux sociaux présentent autant d’avantages que de risques pour les jeunes.\nD’une part, ils permettent de rester en contact avec ses proches, de s’informer rapidement et même de développer des compétences créatives. Par exemple, de nombreux adolescents apprennent les langues grâce à des vidéos.\nD’autre part, un usage excessif peut nuire au sommeil, à la concentration et à l’estime de soi, car on a tendance à se comparer aux autres. Bien que ces plateformes soient gratuites, elles collectent aussi beaucoup de données personnelles.\nÀ mon avis, il ne faut ni les interdire ni les laisser sans contrôle. Il est essentiel que les parents et l’école apprennent aux jeunes à les utiliser avec discernement.\nEn conclusion, c’est l’usage, et non l’outil, qui est bon ou mauvais.\nVeuillez agréer, Madame, Monsieur, mes salutations distinguées.",
    model_en: "Argues both sides: benefits (contact, information, skills) and risks (sleep, concentration, self-esteem, data), then concludes that education and supervision are the solution.",
    checklist: ["Thesis in the first paragraph", "D’une part… d’autre part…", "At least one concession (bien que + subjonctif)", "Example + conclusion", "200+ words"],
  },
  {
    id: "ee-c1-argument", exam: "DELF", level: "C1", nclc: "NCLC 9-10", title: "Essai argumentatif : le télétravail généralisé", minWords: 250,
    prompt: "« Le télétravail devrait devenir la norme pour tous les emplois qui le permettent. » Discutez cette affirmation dans un essai structuré (250 mots).",
    prompt_hi: "« जिन नौकरियों में संभव है, उनके लिए घर से काम ही नियम बने। » इस कथन पर संरचित निबंध लिखें।",
    model: "Il est indéniable que le télétravail a bouleversé nos habitudes. Reste à savoir s’il doit devenir la norme.\nCertes, il offre une autonomie accrue et réduit l’empreinte écologique des déplacements. En outre, il élargit le recrutement à des candidats éloignés des grands centres.\nIl n’en reste pas moins que l’éloignement fragilise la cohésion d’équipe et la transmission des savoir-faire, surtout pour les jeunes recrues. De surcroît, tous les foyers ne disposent pas d’un espace de travail adéquat.\nÀ mon sens, la solution réside dans un modèle hybride, négocié au cas par cas. En définitive, force est de constater que la flexibilité vaut mieux que la norme unique.",
    model_en: "Thesis → concession → counter-argument → personal position (hybrid) → conclusion. Uses C1 linkers: il est indéniable que, certes, il n’en reste pas moins que, force est de constater.",
    checklist: ["Problématique", "Plan en 3 parties", "Linkers C1", "Nuanced personal position", "Conclusion with opening"],
  },
];

export interface SpeakingTask {
  id: string;
  exam: "TEF" | "TCF" | "DELF";
  level: Level;
  nclc: string;
  title: string;
  prompt: string;
  prompt_hi: string;
  phrases: { fr: string; hi: string }[];
  minutes: number;
}

export const speakingTasks: SpeakingTask[] = [
  {
    id: "eo-a2-interview", exam: "TCF", level: "A2", nclc: "NCLC 4", title: "Entretien dirigé", minutes: 2,
    prompt: "Présentez-vous : nom, âge, pays, travail ou études, famille, loisirs, et pourquoi vous apprenez le français.",
    prompt_hi: "अपना परिचय दें: नाम, उम्र, देश, काम/पढ़ाई, परिवार, शौक, और फ़्रेंच क्यों सीख रहे हैं।",
    phrases: [{ fr: "Je m’appelle… et j’ai… ans.", hi: "मेरा नाम… है और मैं… साल का हूँ।" }, { fr: "J’apprends le français parce que je veux vivre au Canada.", hi: "मैं फ़्रेंच इसलिए सीख रहा हूँ कि कनाडा में रहना चाहता हूँ।" }, { fr: "Pendant mon temps libre, j’aime…", hi: "खाली समय में मुझे… पसंद है।" }],
  },
  {
    id: "eo-b1-infos", exam: "TEF", level: "B1", nclc: "NCLC 5-6", title: "Section A : demander des informations", minutes: 5,
    prompt: "Vous lisez une annonce pour un cours de natation. Téléphonez pour obtenir un maximum d’informations (prix, horaires, niveau, inscription).",
    prompt_hi: "आप तैराकी क्लास का विज्ञापन पढ़ते हैं। फ़ोन करके अधिकतम जानकारी लें (कीमत, समय, स्तर, पंजीकरण)।",
    phrases: [{ fr: "Bonjour, je vous appelle au sujet de votre annonce.", hi: "नमस्ते, मैं आपके विज्ञापन के बारे में फ़ोन कर रहा हूँ।" }, { fr: "Pourriez-vous me dire combien coûte le cours ?", hi: "क्या आप बता सकते हैं कि क्लास की कीमत कितनी है?" }, { fr: "Est-ce qu’il y a des cours pour débutants ?", hi: "क्या शुरुआती लोगों के लिए क्लास है?" }, { fr: "Comment est-ce que je peux m’inscrire ?", hi: "मैं पंजीकरण कैसे कर सकता हूँ?" }],
  },
  {
    id: "eo-b2-convaincre", exam: "TEF", level: "B2", nclc: "NCLC 7", title: "Section B : convaincre un ami", minutes: 10,
    prompt: "Un ami hésite à quitter son emploi pour étudier. Vous le convainquez de ne pas le faire (ou de le faire). Donnez au moins trois arguments et répondez à ses objections.",
    prompt_hi: "आपका दोस्त नौकरी छोड़कर पढ़ने की सोच रहा है। उसे मनाएँ (या रोकें): कम से कम तीन तर्क दें और उसके एतराज़ों का जवाब दें।",
    phrases: [{ fr: "À ta place, je réfléchirais bien avant de décider.", hi: "तुम्हारी जगह होता तो फ़ैसले से पहले अच्छे से सोचता।" }, { fr: "Je comprends ton point de vue, mais il faut aussi considérer que…", hi: "मैं तुम्हारी बात समझता हूँ, पर यह भी सोचो कि…" }, { fr: "D’abord… ensuite… enfin…", hi: "पहले… फिर… अंत में…" }, { fr: "Imagine que tu aies un diplôme : tu gagnerais bien plus.", hi: "सोचो तुम्हारे पास डिग्री हो: तुम कहीं ज़्यादा कमाओगे।" }],
  },
  {
    id: "eo-c1-debat", exam: "DELF", level: "C1", nclc: "NCLC 9-10", title: "Exposé et débat", minutes: 10,
    prompt: "Présentez en 3 minutes votre position sur : « Faut-il limiter l’usage des smartphones à l’école ? » puis répondez au contradicteur.",
    prompt_hi: "3 मिनट में अपनी राय रखें: « क्या स्कूल में स्मार्टफ़ोन सीमित हों? » फिर विरोधी के सवालों के जवाब दें।",
    phrases: [{ fr: "La question qui se pose est de savoir si…", hi: "सवाल यह है कि क्या…" }, { fr: "Certes, … ; toutefois, …", hi: "माना कि…; फिर भी…" }, { fr: "Force est de reconnaître que…", hi: "यह मानना पड़ेगा कि…" }, { fr: "Pour résumer, je dirais que…", hi: "सारांश में मैं कहूँगा कि…" }],
  },
];

export const examLevelLabel: Record<Level, string> = { A1: "A1", A2: "A2", B1: "B1", B2: "B2", C1: "C1" };
