import type { Level, TranslationPair } from "./types";

const p = (level: Level, topic: string, en: string, fr: string): TranslationPair => ({ level, topic, en, fr });

/** Translation bank used by the Translate drill (EN → FR tiles and FR → EN choice). */
export const translationBank: TranslationPair[] = [
  // ---------------- A1 ----------------
  p("A1", "Greetings", "Hello, my name is Anna.", "Bonjour, je m’appelle Anna."),
  p("A1", "Identity", "I am a student.", "Je suis étudiant."),
  p("A1", "Greetings", "How are you?", "Comment allez-vous ?"),
  p("A1", "Home", "I live in Paris.", "J’habite à Paris."),
  p("A1", "Family", "She has two brothers.", "Elle a deux frères."),
  p("A1", "Feelings", "We are hungry.", "Nous avons faim."),
  p("A1", "Travel", "Where is the station?", "Où est la gare ?"),
  p("A1", "Food", "The coffee is hot.", "Le café est chaud."),
  p("A1", "Likes", "I like music.", "J’aime la musique."),
  p("A1", "Feelings", "He is not tired.", "Il n’est pas fatigué."),
  p("A1", "Time", "What time is it?", "Quelle heure est-il ?"),
  p("A1", "Family", "My mother is a doctor.", "Ma mère est médecin."),
  p("A1", "Food", "I would like a glass of water.", "Je voudrais un verre d’eau."),
  p("A1", "Greetings", "Thank you very much.", "Merci beaucoup."),

  // ---------------- A2 ----------------
  p("A2", "Past", "Yesterday I visited the museum.", "Hier, j’ai visité le musée."),
  p("A2", "Future", "We are going to travel next week.", "Nous allons voyager la semaine prochaine."),
  p("A2", "Daily life", "She is looking for her keys.", "Elle cherche ses clés."),
  p("A2", "Communication", "I do not understand this word.", "Je ne comprends pas ce mot."),
  p("A2", "Politeness", "Can you help me, please?", "Pouvez-vous m’aider, s’il vous plaît ?"),
  p("A2", "Past", "It was raining when we left.", "Il pleuvait quand nous sommes partis."),
  p("A2", "Routine", "He wakes up at seven every morning.", "Il se réveille à sept heures tous les matins."),
  p("A2", "Past", "I have already eaten.", "J’ai déjà mangé."),
  p("A2", "Comparison", "This restaurant is better than that one.", "Ce restaurant est meilleur que celui-là."),
  p("A2", "Directions", "Turn left at the pharmacy.", "Tournez à gauche à la pharmacie."),
  p("A2", "Shopping", "How much does this cost?", "Combien ça coûte ?"),
  p("A2", "Places", "My friends live far from here.", "Mes amis habitent loin d’ici."),

  // ---------------- B1 ----------------
  p("B1", "Conditions", "If I have time, I will come.", "Si j’ai le temps, je viendrai."),
  p("B1", "Wishes", "I would like to learn Spanish.", "Je voudrais apprendre l’espagnol."),
  p("B1", "Reported speech", "She told me that she was tired.", "Elle m’a dit qu’elle était fatiguée."),
  p("B1", "Subjunctive", "It is necessary that you come early.", "Il faut que tu viennes tôt."),
  p("B1", "Past habits", "When I was young, I played football.", "Quand j’étais jeune, je jouais au football."),
  p("B1", "Duration", "I have been living here for three years.", "J’habite ici depuis trois ans."),
  p("B1", "Pronouns", "He gave me the book yesterday.", "Il m’a donné le livre hier."),
  p("B1", "Concession", "Although it is expensive, I will buy it.", "Bien que ce soit cher, je l’achèterai."),
  p("B1", "Opinion", "I think that he is right.", "Je pense qu’il a raison."),
  p("B1", "Regret", "We should have left earlier.", "Nous aurions dû partir plus tôt."),
  p("B1", "Relative clauses", "The film that I saw was excellent.", "Le film que j’ai vu était excellent."),
  p("B1", "Reported speech", "She asked me where I worked.", "Elle m’a demandé où je travaillais."),

  // ---------------- B2 ----------------
  p("B2", "Conditions", "Had I known, I would have stayed.", "Si j’avais su, je serais resté."),
  p("B2", "Opinion", "It seems to me that the situation is getting worse.", "Il me semble que la situation empire."),
  p("B2", "Concession", "Whatever happens, I will stay with you.", "Quoi qu’il arrive, je resterai avec toi."),
  p("B2", "Proportion", "The more I read, the more I learn.", "Plus je lis, plus j’apprends."),
  p("B2", "Comparison", "He speaks as if he knew everything.", "Il parle comme s’il savait tout."),
  p("B2", "Concession", "Despite the rain, the match took place.", "Malgré la pluie, le match a eu lieu."),
  p("B2", "Relative clauses", "This is the book I was talking about.", "C’est le livre dont je parlais."),
  p("B2", "Regret", "She regrets not having come.", "Elle regrette de ne pas être venue."),
  p("B2", "Work", "We must find a solution as soon as possible.", "Nous devons trouver une solution le plus tôt possible."),
  p("B2", "Subjunctive", "I doubt that he will succeed.", "Je doute qu’il réussisse."),

  // ---------------- C1 ----------------
  p("C1", "Concession", "Even if he were to resign, nothing would change.", "Quand bien même il démissionnerait, rien ne changerait."),
  p("C1", "Formal register", "One cannot overstate the importance of this reform.", "On ne saurait exagérer l’importance de cette réforme."),
  p("C1", "Inversion", "No sooner had she arrived than the meeting began.", "À peine était-elle arrivée que la réunion commença."),
  p("C1", "Subjunctive", "Whatever his motives may be, his act remains unjustifiable.", "Quels que soient ses motifs, son acte demeure injustifiable."),
  p("C1", "Subjunctive", "He would have liked us to be present.", "Il aurait aimé que nous soyons présents."),
  p("C1", "Formal register", "It is to be feared that the crisis will deepen.", "Il est à craindre que la crise ne s’aggrave."),
  p("C1", "Rhetoric", "Far from calming the debate, his remarks inflamed it.", "Loin d’apaiser le débat, ses propos l’ont enflammé."),
  p("C1", "Formal register", "To my knowledge, the report has not been published.", "À ma connaissance, le rapport n’a pas été publié."),
];
