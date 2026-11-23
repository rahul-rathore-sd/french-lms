import { ex, rule, q, tb, type Topic } from "./gh.ts";

/** B2 (NCLC 7-8) and C1 (NCLC 9-10) grammar. */
export const grammarC: Topic[] = [
  {
    slug: "subjonctif-usages", level: "B2", title: "Subjunctive: when to use it", title_hi: "subjonctif कब लगाएँ",
    summary: "After wishes, emotions, doubt, necessity and certain conjunctions (bien que, pour que, avant que, à condition que).",
    summary_hi: "इच्छा, भावना, संदेह, ज़रूरत और कुछ conjunctions (bien que, pour que, avant que) के बाद।",
    rules: [
      rule("Need, wish, order: il faut que, vouloir que, exiger que.", "ज़रूरत/इच्छा/आदेश।", ex("Il faut que tu viennes.", "You must come.", "तुम्हें आना ही होगा।"), ex("Je veux que vous soyez à l’heure.", "I want you to be on time.", "मैं चाहता हूँ कि आप समय पर हों।")),
      rule("Emotion and doubt: être content que, regretter que, douter que.", "भावना और संदेह।", ex("Je regrette qu’il soit parti.", "I’m sorry he left.", "मुझे अफ़सोस है कि वह चला गया।"), ex("Je doute qu’elle réussisse.", "I doubt she’ll succeed.", "मुझे शक है कि वह सफल होगी।")),
      rule("Conjunctions: pour que, afin que, bien que, quoique, avant que, à moins que, jusqu’à ce que.", "ये conjunctions हमेशा subjonctif लेते हैं।", ex("Bien qu’il soit fatigué, il travaille.", "Although tired, he works.", "थका होने के बावजूद वह काम करता है।")),
    ],
    tables: [tb("Irregular subjonctif", ["Verb", "que je…", "que nous…"], ["être", "sois", "soyons"], ["avoir", "aie", "ayons"], ["aller", "aille", "allions"], ["faire", "fasse", "fassions"], ["pouvoir", "puisse", "puissions"], ["savoir", "sache", "sachions"])],
    tips: ["Same subject in both clauses → use the infinitive: Je veux partir (not que je parte).", "après que (fact) takes the indicative; avant que takes the subjunctive."],
    tips_hi: ["दोनों हिस्सों का कर्ता एक हो तो infinitive: Je veux partir।", "après que + indicatif; avant que + subjonctif।"],
    quiz: [q("Bien qu’il ___ riche, il est avare.", ["est", "soit", "sera"], 1, "bien que → subjonctif."), q("Je pense qu’il ___ raison.", ["ait", "a", "aurait"], 1, "penser (affirmative) → indicatif.")],
  },
  {
    slug: "relatifs-composes", level: "B2", title: "Compound relatives: lequel, auquel, duquel", title_hi: "संयुक्त संबंधवाचक: lequel, auquel, duquel",
    summary: "Used after prepositions when the antecedent is a thing: avec lequel, pour laquelle, auquel.",
    summary_hi: "preposition के बाद वस्तु के लिए: avec lequel, pour laquelle, auquel।",
    rules: [
      rule("preposition + lequel/laquelle/lesquels/lesquelles (agree with the noun).", "preposition + lequel… (संज्ञा से मेल)।", ex("Le stylo avec lequel j’écris.", "The pen I write with.", "जिस कलम से मैं लिखता हूँ।"), ex("La raison pour laquelle je suis venu.", "The reason why I came.", "वह कारण जिसकी वजह से मैं आया।")),
      rule("à → auquel / à laquelle / auxquels; de → duquel / de laquelle / desquels. For people, qui is possible.", "à और de के साथ रूप बदलते हैं।", ex("Le projet auquel je pense.", "The project I’m thinking of.", "जिस प्रोजेक्ट के बारे में मैं सोच रहा हूँ।"), ex("L’ami avec qui je voyage.", "The friend I travel with.", "जिस दोस्त के साथ मैं यात्रा करता हूँ।")),
    ],
    tips: ["'dont' is simpler where possible; use duquel only with a preposition of place (à côté duquel)."],
    tips_hi: ["जहाँ हो सके dont लगाएँ; duquel सिर्फ़ जगह वाले prepositions (à côté de) के साथ।"],
    quiz: [q("La table sur ___ j’ai posé le livre.", ["laquelle", "lequel", "laquel"], 0, "table is feminine."), q("Le problème ___ je réfléchis.", ["auquel", "dont", "que"], 0, "réfléchir à → auquel.")],
  },
  {
    slug: "futur-anterieur-cond-passe", level: "B2", title: "Futur antérieur and conditionnel passé", title_hi: "futur antérieur और conditionnel passé",
    summary: "Compound tenses: an action finished before a future moment, or a regret/missed possibility.",
    summary_hi: "भविष्य के किसी क्षण से पहले पूरा हो चुका काम; या पछतावा/छूटा हुआ अवसर।",
    rules: [
      rule("futur antérieur = future of avoir/être + participle.", "futur antérieur = avoir/être का futur + participe passé।", ex("Quand tu arriveras, j’aurai fini.", "By the time you arrive, I’ll have finished.", "जब तुम आओगे तब मैं खत्म कर चुका हूँगा।"), ex("Dès qu’il sera parti, nous mangerons.", "As soon as he has left we’ll eat.", "जैसे ही वह जाएगा हम खाएँगे।")),
      rule("conditionnel passé = conditionnel of avoir/être + participle.", "conditionnel passé = avoir/être का conditionnel + participe।", ex("J’aurais aimé venir.", "I would have liked to come.", "मैं आना चाहता, पर…"), ex("Tu aurais dû me prévenir.", "You should have warned me.", "तुम्हें मुझे बताना चाहिए था।")),
    ],
    tips: ["Great for opinion essays: « À mon avis, on aurait dû agir plus tôt »."],
    tips_hi: ["राय वाले निबंध में बहुत काम आता है।"],
    quiz: [q("Dès qu’elle ___ , je partirai.", ["aura mangé", "mangera", "mange"], 0, "action completed first → futur antérieur."), q("Il ___ pu m’aider.", ["aurait", "avait", "a"], 0, "il aurait pu.")],
  },
  {
    slug: "doubles-pronoms-mise-en-relief", level: "B2", title: "Emphasis: c’est … qui / que, ce qui / ce que", title_hi: "ज़ोर देना: c’est … qui/que, ce qui/ce que",
    summary: "Highlight the important idea and talk about unnamed things.",
    summary_hi: "ज़रूरी बात पर ज़ोर और अनाम चीज़ों के बारे में बात करना।",
    rules: [
      rule("c’est + element + qui (subject) / que (other).", "c’est + तत्व + qui/que।", ex("C’est Marie qui a gagné.", "It’s Marie who won.", "मैरी ही जीती है।"), ex("C’est demain que je pars.", "It’s tomorrow that I leave.", "मैं कल ही जा रहा हूँ।")),
      rule("ce qui (subject) / ce que (object) / ce dont (de) / ce à quoi (à) = what.", "ce qui/ce que = जो (बात)।", ex("Ce qui m’intéresse, c’est la musique.", "What interests me is music.", "जो मुझे दिलचस्प लगता है वह संगीत है।"), ex("Je ne comprends pas ce que tu dis.", "I don’t understand what you’re saying.", "तुम जो कह रहे हो वह मुझे समझ नहीं आया।")),
    ],
    tips: ["Perfect for speaking Task 3 (opinion): « Ce qui me semble essentiel, c’est… »"],
    tips_hi: ["बोलने की परीक्षा में राय देने के लिए बेहतरीन: « Ce qui me semble essentiel, c’est… »"],
    quiz: [q("___ me plaît, c’est la liberté.", ["Ce qui", "Ce que", "Ce dont"], 0, "subject → ce qui."), q("Dis-moi ___ tu veux.", ["ce qui", "ce que", "ce dont"], 1, "object → ce que.")],
  },
  {
    slug: "expression-concession-opposition", level: "B2", title: "Concession, cause and consequence (advanced)", title_hi: "रियायत, कारण, परिणाम (उन्नत)",
    summary: "Nuanced linking: malgré, même si, quoique, tellement … que, si bien que, d’autant que.",
    summary_hi: "बारीक जोड़: malgré, même si, tellement… que, d’autant que।",
    rules: [
      rule("Concession: malgré + noun, bien que + subj., même si + indicative.", "रियायत: malgré + संज्ञा, bien que + subjonctif, même si + indicatif।", ex("Malgré la pluie, nous sommes sortis.", "Despite the rain we went out.", "बारिश के बावजूद हम निकले।"), ex("Même s’il pleut, je viendrai.", "Even if it rains I’ll come.", "बारिश भी हुई तो मैं आऊँगा।")),
      rule("Consequence: si/tellement … que, si bien que, de sorte que.", "परिणाम: इतना … कि।", ex("Il a tellement travaillé qu’il est épuisé.", "He worked so much that he’s exhausted.", "उसने इतना काम किया कि वह थक गया।")),
      rule("Added cause: d’autant plus que, étant donné que, sous prétexte que.", "अतिरिक्त कारण।", ex("Il est content, d’autant plus qu’il a réussi.", "He’s happy, all the more as he succeeded.", "वह खुश है, खासकर इसलिए कि वह सफल हुआ।")),
    ],
    tips: ["Use at least one concession in every opinion essay: it shows B2+ control."],
    tips_hi: ["हर निबंध में कम से कम एक concession डालें — इससे B2+ स्तर दिखता है।"],
    quiz: [q("___ le froid, il est sorti en t-shirt.", ["Malgré", "Bien que", "Parce que"], 0, "followed by a noun → malgré."), q("Bien que tu ___ raison, je refuse.", ["as", "aies", "auras"], 1, "bien que → subjonctif.")],
  },
  {
    slug: "participe-present-adjectif", level: "B2", title: "Present participle and verbal adjective", title_hi: "participe présent और verbal adjective",
    summary: "-ant forms: participe présent (invariable, replaces qui + verb) vs adjectif verbal (agrees).",
    summary_hi: "-ant रूप: participe présent (नहीं बदलता) बनाम adjectif verbal (लिंग/वचन से बदलता है)।",
    rules: [
      rule("Participe présent: invariable, written style, replaces 'qui + verb' or cause.", "participe présent: अपरिवर्तनीय, लिखित शैली।", ex("Les élèves travaillant dur réussissent.", "Students who work hard succeed.", "मेहनत करने वाले छात्र सफल होते हैं।"), ex("Ayant fini, il est parti.", "Having finished, he left.", "खत्म करके वह चला गया।")),
      rule("Adjectif verbal: agrees like an adjective.", "adjectif verbal: विशेषण की तरह मेल।", ex("Des histoires fascinantes.", "Fascinating stories.", "रोचक कहानियाँ।")),
    ],
    tips: ["Spelling traps: fatigant (adj.) vs fatiguant (participle); différent vs différant."],
    tips_hi: ["वर्तनी का जाल: fatigant (विशेषण) बनाम fatiguant (क्रिया)।"],
    quiz: [q("Une femme ___ (charmer).", ["charmant", "charmante", "charmantes"], 1, "adjective agrees: charmante."), q("___ peur, il a couru.", ["Ayant", "Ayante", "En ayant"], 0, "participe présent, written cause.")],
  },
  {
    slug: "nominalisation-style-ecrit", level: "B2", title: "Nominalisation and formal writing", title_hi: "भाववाचक रूप और औपचारिक लेखन",
    summary: "Turn verbs into nouns to sound formal: décider → la décision.",
    summary_hi: "औपचारिक लेखन के लिए क्रिया को संज्ञा बनाना: décider → la décision।",
    rules: [
      rule("Replace subordinate clauses with a noun phrase.", "उपवाक्य की जगह संज्ञा-पदबंध।", ex("Quand il est arrivé… → À son arrivée…", "On his arrival…", "उसके आने पर…"), ex("Parce que le prix augmente → En raison de la hausse des prix", "Due to the rise in prices", "कीमतें बढ़ने के कारण")),
    ],
    tables: [tb("Useful pairs", ["Verb", "Noun", "Hindi"], ["augmenter", "l’augmentation", "बढ़ोतरी"], ["diminuer", "la diminution", "कमी"], ["développer", "le développement", "विकास"], ["améliorer", "l’amélioration", "सुधार"], ["échouer", "l’échec", "असफलता"], ["réussir", "la réussite", "सफलता"])],
    tips: ["Formal letters (TEF writing B): use 'En raison de', 'Suite à', 'Afin de'."],
    tips_hi: ["औपचारिक पत्र में 'En raison de', 'Suite à', 'Afin de' लिखें।"],
    quiz: [q("‘augmenter’ → ", ["l’augmentation", "l’augmente", "l’augmentement"], 0, "-ation noun."), q("Nominal form of ‘échouer’ :", ["l’échec", "l’échouement", "l’échouer"], 0, "l’échec.")],
  },
  {
    slug: "inversion-style-soutenu", level: "C1", title: "Stylistic inversion and elevated register", title_hi: "शैलीगत उलटफेर और उच्च भाषा-स्तर",
    summary: "Inversion after adverbs (peut-être, sans doute, à peine, aussi) and in subordinates, found in editorials and DALF.",
    summary_hi: "समाचार-संपादकीय और DALF में मिलने वाला उलटफेर।",
    rules: [
      rule("Inversion after à peine, peut-être que / peut-être + inversion, sans doute, aussi (= therefore).", "इन क्रिया-विशेषणों के बाद क्रिया-कर्ता उलटते हैं।", ex("À peine était-il arrivé qu’il est reparti.", "Hardly had he arrived when he left.", "वह मुश्किल से पहुँचा ही था कि फिर चला गया।"), ex("Peut-être a-t-il oublié.", "Maybe he forgot.", "शायद वह भूल गया।")),
      rule("Fixed formulas: Ainsi, Toujours est-il que…, Reste à savoir si…", "स्थिर वाक्यांश।", ex("Toujours est-il que le projet a échoué.", "The fact remains that the project failed.", "सच यही है कि प्रोजेक्ट विफल रहा।")),
    ],
    tips: ["Use sparingly: one or two stylish inversions per essay lifts the score to C1."],
    tips_hi: ["कम इस्तेमाल करें — एक-दो सुंदर inversion निबंध को C1 बना देते हैं।"],
    quiz: [q("Peut-être ___ -il raison.", ["a", "a-t", "as"], 1, "inversion with t: a-t-il."), q("À peine ___ -elle sortie…", ["est", "était", "a"], 1, "à peine + plus-que-parfait (inversion).")],
  },
  {
    slug: "passe-simple-litteraire", level: "C1", title: "Passé simple and literary tenses", title_hi: "passé simple (साहित्यिक भूतकाल)",
    summary: "The narrative past of novels and news essays. Learn to recognise it; use mainly 3rd person.",
    summary_hi: "उपन्यासों का भूतकाल। पहचानना ज़रूरी, लिखना कम।",
    rules: [
      rule("Endings: -a/-èrent for -er verbs; -it/-irent for -ir/-re; -ut/-urent for -oir/-ire irregulars.", "3rd person रूप सबसे ज़्यादा मिलते हैं।", ex("Il ouvrit la porte et entra.", "He opened the door and entered.", "उसने दरवाज़ा खोला और अंदर गया।"), ex("Ils partirent à l’aube.", "They left at dawn.", "वे भोर में निकले।")),
    ],
    tables: [tb("Common irregulars (il / ils)", ["Infinitive", "il", "ils"], ["être", "fut", "furent"], ["avoir", "eut", "eurent"], ["faire", "fit", "firent"], ["venir", "vint", "vinrent"], ["voir", "vit", "virent"], ["dire", "dit", "dirent"])],
    tips: ["Imparfait (description) + passé simple (event) in a story, like imparfait + passé composé in speech."],
    tips_hi: ["कहानी में imparfait वर्णन के लिए और passé simple घटना के लिए।"],
    quiz: [q("Il ___ la lettre. (lire)", ["lit", "lut", "lisait"], 1, "lire → il lut."), q("Ils ___ au village. (aller)", ["allèrent", "allaient", "iront"], 0, "ils allèrent.")],
  },
  {
    slug: "argumentation-c1", level: "C1", title: "Argumentation toolbox", title_hi: "तर्क करने का टूलबॉक्स",
    summary: "Structure a persuasive text or talk: thesis, arguments, counter-arguments, conclusion.",
    summary_hi: "प्रभावी निबंध/भाषण की संरचना: थीसिस, तर्क, प्रतितर्क, निष्कर्ष।",
    rules: [
      rule("Introduce a thesis and give your position.", "थीसिस और अपनी राय।", ex("Il est indéniable que… Pour ma part, je soutiens que…", "It is undeniable that… For my part, I maintain that…", "यह निर्विवाद है कि… मैं तो मानता हूँ कि…")),
      rule("Add, contrast, concede.", "जोड़ना, विरोध करना, मानना।", ex("En outre / De surcroît / Certes… mais… / Il n’en reste pas moins que…", "Moreover / Admittedly… but…", "इसके अलावा / माना कि… लेकिन…")),
      rule("Conclude.", "निष्कर्ष।", ex("En définitive, force est de constater que…", "Ultimately, one has to admit that…", "अंततः मानना पड़ेगा कि…")),
    ],
    tips: ["Plan: Introduction (accroche + problématique) → 2-3 arguments with examples → conclusion + ouverture."],
    tips_hi: ["योजना: भूमिका → 2-3 तर्क + उदाहरण → निष्कर्ष।"],
    quiz: [q("‘En outre’ means:", ["moreover", "however", "therefore"], 0, "adds an argument."), q("‘Il n’en reste pas moins que’ introduces:", ["a conclusion despite objections", "a question", "a cause"], 0, "'nevertheless the fact is'.")],
  },
];
