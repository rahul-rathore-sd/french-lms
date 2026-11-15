import type { GrammarTopic } from "./types";

export const grammar: GrammarTopic[] = [
  {
    slug: "etre-avoir",
    level: "A1",
    title: "Être and avoir (to be, to have)",
    summary: "The two most important verbs in French. They are irregular and also act as auxiliaries for past tenses.",
    rules: [
      { rule: "Être describes who or what someone is: identity, job, nationality, state.", examples: [{ fr: "Je suis étudiant.", en: "I am a student." }, { fr: "Elle est française.", en: "She is French." }] },
      { rule: "Avoir expresses possession, and also age and some feelings (faim, soif, peur, chaud, froid).", examples: [{ fr: "J’ai vingt ans.", en: "I am twenty." }, { fr: "Nous avons faim.", en: "We are hungry." }] },
    ],
    tables: [
      { caption: "Présent", headers: ["Pronoun", "être", "avoir"], rows: [["je / j’", "suis", "ai"], ["tu", "es", "as"], ["il / elle / on", "est", "a"], ["nous", "sommes", "avons"], ["vous", "êtes", "avez"], ["ils / elles", "sont", "ont"]] },
    ],
    tips: ["Age uses avoir: « J’ai 25 ans », never « je suis 25 ans ».", "Liaison: « vous avez » is pronounced vou-z-avez."],
  },
  {
    slug: "articles-gender",
    level: "A1",
    title: "Articles and gender",
    summary: "Every French noun is masculine or feminine, and the article tells you which.",
    rules: [
      { rule: "Definite articles (the): le (m), la (f), l’ (before a vowel), les (plural).", examples: [{ fr: "le livre, la table, l’ami, les enfants", en: "the book, the table, the friend, the children" }] },
      { rule: "Indefinite articles (a/an/some): un (m), une (f), des (plural).", examples: [{ fr: "Un café et une baguette, s’il vous plaît.", en: "A coffee and a baguette, please." }] },
      { rule: "Partitive (some, an amount of): du, de la, de l’.", examples: [{ fr: "Je bois du café et de l’eau.", en: "I drink (some) coffee and water." }] },
    ],
    tips: ["Learn each noun with its article: la maison, le jardin.", "Many nouns ending in -tion, -té, -ie are feminine; those ending in -age, -ment, -eau are often masculine."],
  },
  {
    slug: "present-er-verbs",
    level: "A1",
    title: "Présent of regular -er verbs",
    summary: "About 90% of French verbs end in -er and follow one pattern.",
    rules: [
      { rule: "Remove -er from the infinitive and add the endings -e, -es, -e, -ons, -ez, -ent.", examples: [{ fr: "Je parle français.", en: "I speak French." }, { fr: "Nous habitons à Lyon.", en: "We live in Lyon." }] },
    ],
    tables: [
      { caption: "parler (to speak)", headers: ["Pronoun", "Form"], rows: [["je", "parle"], ["tu", "parles"], ["il / elle / on", "parle"], ["nous", "parlons"], ["vous", "parlez"], ["ils / elles", "parlent"]] },
    ],
    tips: ["The endings -e, -es, -ent are silent: je parle, tu parles and ils parlent sound the same.", "Verbs in -ger and -cer add a letter before -ons: nous mangeons, nous commençons."],
  },
  {
    slug: "negation-questions",
    level: "A1",
    title: "Negation and questions",
    summary: "Wrap the verb with ne… pas, and ask questions with est-ce que, intonation or inversion.",
    rules: [
      { rule: "Negation: ne + verb + pas (ne becomes n’ before a vowel).", examples: [{ fr: "Je ne parle pas allemand.", en: "I do not speak German." }, { fr: "Il n’est pas fatigué.", en: "He is not tired." }] },
      { rule: "Questions: raise your voice, add est-ce que, or invert the verb and pronoun (more formal).", examples: [{ fr: "Tu viens ?", en: "Are you coming?" }, { fr: "Est-ce que tu viens ?", en: "Are you coming?" }, { fr: "Venez-vous ?", en: "Are you coming? (formal)" }] },
    ],
    tips: ["In speech, ne is often dropped: « Je sais pas ». Keep it in writing.", "Question words: qui, que, où, quand, comment, pourquoi, combien."],
  },
  {
    slug: "irregular-aller-faire",
    level: "A1",
    title: "Aller, faire, venir, prendre",
    summary: "Four irregular verbs you will use every day.",
    rules: [
      { rule: "Aller (to go) also builds the near future: aller + infinitive.", examples: [{ fr: "Je vais à l’école.", en: "I go to school." }, { fr: "Nous allons partir.", en: "We are going to leave." }] },
    ],
    tables: [
      { caption: "Présent", headers: ["Pronoun", "aller", "faire", "venir", "prendre"], rows: [["je", "vais", "fais", "viens", "prends"], ["tu", "vas", "fais", "viens", "prends"], ["il / elle / on", "va", "fait", "vient", "prend"], ["nous", "allons", "faisons", "venons", "prenons"], ["vous", "allez", "faites", "venez", "prenez"], ["ils / elles", "vont", "font", "viennent", "prennent"]] },
    ],
    tips: ["Faire appears in weather (il fait beau), activities (faire du sport) and chores (faire les courses)."],
  },
  {
    slug: "passe-compose",
    level: "A2",
    title: "Passé composé",
    summary: "The main tense for completed past actions: auxiliary (avoir or être) + past participle.",
    rules: [
      { rule: "Most verbs use avoir: j’ai mangé, tu as fini, nous avons parlé.", examples: [{ fr: "Hier, j’ai visité le musée.", en: "Yesterday I visited the museum." }] },
      { rule: "Verbs of motion and reflexive verbs use être, and the participle agrees with the subject.", examples: [{ fr: "Elle est arrivée à huit heures.", en: "She arrived at eight." }, { fr: "Nous nous sommes levés tôt.", en: "We got up early." }] },
    ],
    tables: [
      { caption: "Irregular participles", headers: ["Infinitive", "Participle"], rows: [["avoir", "eu"], ["être", "été"], ["faire", "fait"], ["prendre", "pris"], ["voir", "vu"], ["pouvoir", "pu"], ["vouloir", "voulu"], ["écrire", "écrit"]] },
      { caption: "Verbs that take être (DR MRS VANDERTRAMP)", headers: ["Group", "Examples"], rows: [["Motion", "aller, venir, arriver, partir, entrer, sortir"], ["Up / down", "monter, descendre, tomber"], ["Life", "naître, mourir, rester, devenir"], ["Return", "rentrer, retourner"]] },
    ],
    tips: ["With être, agree the participle: elles sont parties.", "Negation wraps the auxiliary: je n’ai pas mangé."],
  },
  {
    slug: "futur-proche-pronouns",
    level: "A2",
    title: "Near future and direct object pronouns",
    summary: "Talk about what you are about to do, and avoid repetition with le, la, les.",
    rules: [
      { rule: "Futur proche: aller (présent) + infinitive.", examples: [{ fr: "Il va pleuvoir.", en: "It is going to rain." }] },
      { rule: "Direct object pronouns: me, te, le/la, nous, vous, les — placed before the conjugated verb.", examples: [{ fr: "Je le vois.", en: "I see him / it." }, { fr: "Je ne les connais pas.", en: "I do not know them." }, { fr: "Je vais la voir demain.", en: "I am going to see her tomorrow." }] },
    ],
    tips: ["In the passé composé, the pronoun goes before the auxiliary and the participle agrees: « Je l’ai vue »."],
  },
  {
    slug: "imparfait",
    level: "B1",
    title: "Imparfait vs passé composé",
    summary: "Imparfait paints the scene and habits; passé composé tells the events.",
    rules: [
      { rule: "Imparfait: stem of nous (présent) minus -ons + -ais, -ais, -ait, -ions, -iez, -aient.", examples: [{ fr: "Quand j’étais petit, nous allions à la mer.", en: "When I was little, we used to go to the sea." }] },
      { rule: "Use imparfait for description, habits and ongoing background; use passé composé for a precise event that interrupts.", examples: [{ fr: "Il pleuvait quand nous sommes sortis.", en: "It was raining when we went out." }] },
    ],
    tables: [
      { caption: "être, avoir, faire à l’imparfait", headers: ["Pronoun", "être", "avoir", "faire"], rows: [["je", "étais", "avais", "faisais"], ["tu", "étais", "avais", "faisais"], ["il / elle", "était", "avait", "faisait"], ["nous", "étions", "avions", "faisions"], ["vous", "étiez", "aviez", "faisiez"], ["ils / elles", "étaient", "avaient", "faisaient"]] },
    ],
    tips: ["Trigger words for imparfait: souvent, toujours, d’habitude, chaque jour, à cette époque."],
  },
  {
    slug: "futur-conditionnel",
    level: "B1",
    title: "Futur simple and conditionnel",
    summary: "Both are built on the infinitive (or irregular stem). Futur states facts to come; conditionnel states hypotheticals and politeness.",
    rules: [
      { rule: "Futur: infinitive + -ai, -as, -a, -ons, -ez, -ont.", examples: [{ fr: "Demain, nous partirons tôt.", en: "Tomorrow we will leave early." }] },
      { rule: "Conditionnel: the same stem + imparfait endings (-ais, -ait…). Used for polite requests and hypotheses.", examples: [{ fr: "Je voudrais un café.", en: "I would like a coffee." }, { fr: "Si j’avais le temps, je voyagerais.", en: "If I had time, I would travel." }] },
    ],
    tables: [
      { caption: "Irregular stems (futur and conditionnel)", headers: ["Verb", "Stem", "Example"], rows: [["être", "ser-", "je serai / je serais"], ["avoir", "aur-", "j’aurai / j’aurais"], ["aller", "ir-", "j’irai / j’irais"], ["faire", "fer-", "je ferai / je ferais"], ["pouvoir", "pourr-", "je pourrai / je pourrais"], ["venir", "viendr-", "je viendrai / je viendrais"], ["voir", "verr-", "je verrai / je verrais"]] },
    ],
    tips: ["If-clauses: si + présent → futur; si + imparfait → conditionnel."],
  },
  {
    slug: "subjonctif-present",
    level: "B1",
    title: "Subjonctif présent",
    summary: "The mood of doubt, wish, necessity and emotion, used after que.",
    rules: [
      { rule: "Triggers: il faut que, vouloir que, bien que, pour que, avant que, douter que, être content que…", examples: [{ fr: "Il faut que tu viennes.", en: "You must come." }, { fr: "Bien qu’il soit fatigué, il travaille.", en: "Although he is tired, he is working." }] },
      { rule: "Formation: ils-stem of the présent + -e, -es, -e, -ions, -iez, -ent.", examples: [{ fr: "que je parle, que nous finissions", en: "that I speak, that we finish" }] },
    ],
    tables: [
      { caption: "Irregular subjunctives", headers: ["Pronoun", "être", "avoir", "faire", "aller"], rows: [["que je", "sois", "aie", "fasse", "aille"], ["que tu", "sois", "aies", "fasses", "ailles"], ["qu’il / elle", "soit", "ait", "fasse", "aille"], ["que nous", "soyons", "ayons", "fassions", "allions"], ["que vous", "soyez", "ayez", "fassiez", "alliez"], ["qu’ils / elles", "soient", "aient", "fassent", "aillent"]] },
    ],
    tips: ["No subjunctive when the subject is the same: « Je veux partir » (not « que je parte »).", "Certainty (je pense que, je sais que) → indicatif; doubt (je doute que) → subjonctif."],
  },
  {
    slug: "pronoms-relatifs",
    level: "B2",
    title: "Relative pronouns: qui, que, dont, où, lequel",
    summary: "Link clauses elegantly. The choice depends on the role of the noun in the relative clause.",
    rules: [
      { rule: "qui = subject; que = direct object; où = place or time; dont = replaces de + noun.", examples: [{ fr: "L’homme qui parle est mon oncle.", en: "The man who is speaking is my uncle." }, { fr: "Le livre que je lis est passionnant.", en: "The book I am reading is gripping." }, { fr: "C’est le livre dont je parlais.", en: "It is the book I was talking about." }] },
      { rule: "After a preposition, use lequel / laquelle / lesquels / lesquelles (auquel, duquel…).", examples: [{ fr: "La raison pour laquelle il est parti reste inconnue.", en: "The reason why he left remains unknown." }] },
    ],
    tips: ["If the verb after the pronoun takes « de » (parler de, avoir besoin de), use dont.", "That agreement: « la lettre que j’ai écrite » — the participle agrees with the preceding object."],
  },
  {
    slug: "plus-que-parfait-hypotheses",
    level: "B2",
    title: "Plus-que-parfait and past hypotheses",
    summary: "Talk about what happened before another past event, and imagine different pasts.",
    rules: [
      { rule: "Plus-que-parfait: imparfait of avoir/être + participle.", examples: [{ fr: "Quand je suis arrivé, il était déjà parti.", en: "When I arrived, he had already left." }] },
      { rule: "Unreal past: si + plus-que-parfait → conditionnel passé.", examples: [{ fr: "Si j’avais su, je serais resté.", en: "If I had known, I would have stayed." }] },
    ],
    tips: ["Conditionnel passé = conditionnel of avoir/être + participle: « j’aurais dû », « nous serions partis »."],
  },
  {
    slug: "concordance-passe-simple",
    level: "C1",
    title: "Passé simple and sequence of tenses",
    summary: "The literary narrative past, and how tenses line up in formal writing.",
    rules: [
      { rule: "Passé simple is used in literature and history for completed actions. -er verbs: -ai, -as, -a, -âmes, -âtes, -èrent.", examples: [{ fr: "Il entra, regarda autour de lui et sourit.", en: "He came in, looked around and smiled." }] },
      { rule: "Sequence: the main verb’s tense decides whether you use the présent or the imparfait subjunctive (rare today).", examples: [{ fr: "Il fallait qu’il partît.", en: "He had to leave. (literary imparfait du subjonctif)" }] },
    ],
    tables: [
      { caption: "Passé simple of common verbs (il / ils)", headers: ["Infinitive", "il / elle", "ils / elles"], rows: [["être", "fut", "furent"], ["avoir", "eut", "eurent"], ["faire", "fit", "firent"], ["aller", "alla", "allèrent"], ["venir", "vint", "vinrent"], ["dire", "dit", "dirent"]] },
    ],
    tips: ["You will meet the passé simple mostly in the 3rd person. Learn to recognise it before producing it.", "Modern writing replaces the imparfait du subjonctif with the présent: « Il fallait qu’il parte »."],
  },
  {
    slug: "registre-soutenu",
    level: "C1",
    title: "Formal register and stylistic devices",
    summary: "Inversion, ne explétif and concessive structures that give your French an educated tone.",
    rules: [
      { rule: "Inversion after adverbs: à peine, peut-être, sans doute, aussi (therefore).", examples: [{ fr: "À peine était-elle arrivée que la réunion commença.", en: "No sooner had she arrived than the meeting began." }] },
      { rule: "Concession with quel que soit / quoi que + subjunctive, and loin de + infinitive.", examples: [{ fr: "Quels que soient ses motifs, son acte demeure injustifiable.", en: "Whatever his motives, his act remains unjustifiable." }] },
      { rule: "On ne saurait + infinitive = “one cannot”: a formal alternative to ne pas pouvoir.", examples: [{ fr: "On ne saurait exagérer l’importance de cette réforme.", en: "One cannot overstate the importance of this reform." }] },
    ],
    tips: ["Ne explétif: after craindre que, avoir peur que, avant que (formal): « Je crains qu’il ne vienne ». It is not a negation."],
  },
];
