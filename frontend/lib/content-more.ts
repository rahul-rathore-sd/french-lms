import type { Row } from "./content-core";

/** Additional vocabulary: A2 daily life, B1 society, B2 argument and literature, C1 refined French. */
export const moreRows: Row[] = [
  // ---- Home, city & daily life (A2) ----
  ["Le logement", "/lə lɔʒ.mɑ̃/", "nom masculin", "Housing; accommodation", "आवास; रहने की जगह", "A2", "Home", "Le logement est cher à Paris.", "Housing is expensive in Paris.", "पेरिस में आवास महँगा है।"],
  ["Le quartier", "/lə kaʁ.tje/", "nom masculin", "Neighbourhood", "मोहल्ला; इलाका", "A2", "City", "J’adore mon quartier, il est très animé.", "I love my neighbourhood, it is very lively.", "मुझे अपना मोहल्ला बहुत पसंद है, वहाँ बहुत रौनक रहती है।"],
  ["Le voisin", "/lə vwa.zɛ̃/", "nom masculin", "Neighbour", "पड़ोसी", "A2", "Home", "Mon voisin m’a aidé à déménager.", "My neighbour helped me move.", "मेरे पड़ोसी ने मुझे घर बदलने में मदद की।"],
  ["Déménager", "/de.me.na.ʒe/", "verbe", "To move house", "घर बदलना", "A2", "Home", "Nous allons déménager en juin.", "We are going to move in June.", "हम जून में घर बदलने वाले हैं।"],
  ["Le travail", "/lə tʁa.vaj/", "nom masculin", "Work; job", "काम; नौकरी", "A2", "Work", "Je cherche un nouveau travail.", "I am looking for a new job.", "मैं नई नौकरी ढूँढ रहा हूँ।"],
  ["La réunion", "/la ʁe.y.njɔ̃/", "nom féminin", "Meeting", "बैठक", "A2", "Work", "La réunion commence à dix heures.", "The meeting starts at ten o’clock.", "बैठक दस बजे शुरू होती है।"],
  ["Le rendez-vous", "/lə ʁɑ̃.de.vu/", "nom masculin", "Appointment; date", "मुलाक़ात का समय", "A2", "Work", "J’ai un rendez-vous chez le médecin.", "I have an appointment at the doctor’s.", "मेरी डॉक्टर के पास मुलाक़ात का समय है।"],
  ["La santé", "/la sɑ̃.te/", "nom féminin", "Health", "स्वास्थ्य", "A2", "Health", "À votre santé !", "Cheers! (to your health)", "आपके स्वास्थ्य के नाम!"],
  ["Avoir mal à", "/a.vwaʁ mal a/", "locution verbale", "To have a pain in", "में दर्द होना", "A2", "Health", "J’ai mal à la tête.", "I have a headache.", "मेरे सिर में दर्द है।"],
  ["Le temps", "/lə tɑ̃/", "nom masculin", "Weather; time", "मौसम; समय", "A2", "Weather", "Quel temps fait-il aujourd’hui ?", "What is the weather like today?", "आज मौसम कैसा है?"],
  ["Il pleut", "/il plø/", "locution verbale", "It is raining", "बारिश हो रही है", "A2", "Weather", "Il pleut depuis ce matin.", "It has been raining since this morning.", "आज सुबह से बारिश हो रही है।"],
  ["Acheter", "/aʃ.te/", "verbe", "To buy", "ख़रीदना", "A2", "Shopping", "J’achète du pain tous les matins.", "I buy bread every morning.", "मैं हर सुबह रोटी ख़रीदता हूँ।"],
  ["Cher", "/ʃɛʁ/", "adjectif", "Expensive; dear", "महँगा; प्रिय", "A2", "Shopping", "Ce manteau est trop cher.", "This coat is too expensive.", "यह कोट बहुत महँगा है।"],
  ["Gratuit", "/ɡʁa.tɥi/", "adjectif", "Free (of charge)", "मुफ़्त", "A2", "Shopping", "L’entrée du musée est gratuite le dimanche.", "Museum entry is free on Sundays.", "रविवार को संग्रहालय में प्रवेश मुफ़्त है।"],

  // ---- Opinions, society & abstract ideas (B1) ----
  ["Un avis", "/œ̃.n‿a.vi/", "nom masculin", "Opinion", "राय", "B1", "Opinions", "À mon avis, tu as raison.", "In my opinion, you are right.", "मेरी राय में, तुम सही हो।"],
  ["Un argument", "/œ̃.n‿aʁ.ɡy.mɑ̃/", "nom masculin", "Argument", "तर्क", "B1", "Opinions", "Son argument est très convaincant.", "His argument is very convincing.", "उसका तर्क बहुत विश्वसनीय है।"],
  ["Prouver", "/pʁu.ve/", "verbe", "To prove", "साबित करना", "B1", "Opinions", "Il faut prouver ce que l’on affirme.", "You must prove what you claim.", "जो कहा जाए उसे साबित करना पड़ता है।"],
  ["Remarquer", "/ʁə.maʁ.ke/", "verbe", "To notice", "ध्यान देना; गौर करना", "B1", "Verbs", "As-tu remarqué son nouveau sourire ?", "Have you noticed her new smile?", "क्या तुमने उसकी नई मुस्कान पर गौर किया?"],
  ["S’habituer à", "/sa.bi.tɥe a/", "verbe pronominal", "To get used to", "आदत पड़ना", "B1", "Verbs", "Je m’habitue au climat du Canada.", "I am getting used to Canada’s climate.", "मुझे कनाडा के मौसम की आदत पड़ रही है।"],
  ["Se débrouiller", "/sə de.bʁu.je/", "verbe pronominal", "To manage; to get by", "जैसे-तैसे निपटा लेना", "B1", "Verbs", "Je me débrouille en français.", "I can get by in French.", "मैं फ़्रेंच में काम चला लेता हूँ।"],
  ["Un souvenir", "/œ̃ suv.niʁ/", "nom masculin", "Memory; souvenir", "याद; स्मृति-चिह्न", "B1", "Feelings", "J’ai de merveilleux souvenirs de ce voyage.", "I have wonderful memories of this trip.", "इस यात्रा की मेरे पास अद्भुत यादें हैं।"],
  ["Surprenant", "/syʁ.pʁə.nɑ̃/", "adjectif", "Surprising", "आश्चर्यजनक", "B1", "Feelings", "Ce résultat est vraiment surprenant.", "This result is truly surprising.", "यह नतीजा सचमुच आश्चर्यजनक है।"],
  ["Inquiet", "/ɛ̃.kjɛ/", "adjectif", "Worried", "चिंतित", "B1", "Feelings", "Elle est inquiète pour son examen.", "She is worried about her exam.", "वह अपनी परीक्षा को लेकर चिंतित है।"],
  ["L’environnement", "/lɑ̃.vi.ʁɔn.mɑ̃/", "nom masculin", "Environment", "पर्यावरण", "B1", "Society", "Il faut protéger l’environnement.", "We must protect the environment.", "हमें पर्यावरण की रक्षा करनी चाहिए।"],
  ["Le gouvernement", "/lə ɡu.vɛʁ.nə.mɑ̃/", "nom masculin", "Government", "सरकार", "B1", "Society", "Le gouvernement annonce de nouvelles mesures.", "The government is announcing new measures.", "सरकार नए उपायों की घोषणा कर रही है।"],
  ["Un emploi", "/œ̃.n‿ɑ̃.plwa/", "nom masculin", "Job; employment", "रोज़गार", "B1", "Work", "Le taux d’emploi a augmenté.", "The employment rate has risen.", "रोज़गार दर बढ़ी है।"],
  ["Un défi", "/œ̃ de.fi/", "nom masculin", "Challenge", "चुनौती", "B1", "Society", "Apprendre une langue est un beau défi.", "Learning a language is a great challenge.", "भाषा सीखना एक बढ़िया चुनौती है।"],

  // ---- Nuance, argument & literature (B2) ----
  ["Néanmoins", "/ne.ɑ̃.mwɛ̃/", "adverbe", "Nevertheless", "तथापि; फिर भी", "B2", "Connectors", "Le projet est risqué ; néanmoins, il vaut la peine.", "The project is risky; nevertheless, it is worth it.", "परियोजना जोखिम भरी है; फिर भी, यह करने लायक है।"],
  ["Autrement dit", "/o.tʁə.mɑ̃ di/", "locution", "In other words", "दूसरे शब्दों में", "B2", "Connectors", "Autrement dit, nous devons recommencer.", "In other words, we have to start again.", "दूसरे शब्दों में, हमें फिर से शुरू करना होगा।"],
  ["Davantage de", "/da.vɑ̃.taʒ də/", "locution", "More (of)", "अधिक", "B2", "Connectors", "Il faudrait davantage de temps.", "More time would be needed.", "और अधिक समय की ज़रूरत होगी।"],
  ["Mettre en évidence", "/mɛtʁ ɑ̃.n‿e.vi.dɑ̃s/", "locution verbale", "To highlight; to bring out", "उजागर करना", "B2", "Academic", "L’étude met en évidence un problème majeur.", "The study highlights a major problem.", "अध्ययन एक बड़ी समस्या को उजागर करता है।"],
  ["Remettre en question", "/ʁə.mɛtʁ ɑ̃ kɛs.tjɔ̃/", "locution verbale", "To call into question", "पर सवाल उठाना", "B2", "Academic", "Il remet en question toutes nos certitudes.", "He calls all our certainties into question.", "वह हमारे सभी यक़ीनों पर सवाल उठाता है।"],
  ["Tenir compte de", "/tə.niʁ kɔ̃t də/", "locution verbale", "To take into account", "को ध्यान में रखना", "B2", "Academic", "Il faut tenir compte des différences culturelles.", "Cultural differences must be taken into account.", "सांस्कृतिक अंतरों को ध्यान में रखना ज़रूरी है।"],
  ["Un enjeu", "/œ̃.n‿ɑ̃.ʒø/", "nom masculin", "Stake; issue at stake", "दाँव पर लगी बात; मुद्दा", "B2", "Society", "L’enjeu de cette élection est considérable.", "The stakes of this election are considerable.", "इस चुनाव का दाँव बहुत बड़ा है।"],
  ["Nuancer", "/nɥɑ̃.se/", "verbe", "To qualify; to add nuance to", "बारीक़ी से कहना; नरम करना", "B2", "Opinions", "Il faut nuancer ce jugement.", "This judgement needs qualifying.", "इस निर्णय में बारीक़ी लानी होगी।"],
  ["Plaider pour", "/ple.de puʁ/", "verbe", "To argue for; to plead for", "की पैरवी करना", "B2", "Opinions", "Elle plaide pour une réforme profonde.", "She argues for deep reform.", "वह गहरे सुधार की पैरवी करती है।"],
  ["Un paradoxe", "/œ̃ pa.ʁa.dɔks/", "nom masculin", "Paradox", "विरोधाभास", "B2", "Literature", "C’est un paradoxe que l’on retrouve chez Pascal.", "It is a paradox found in Pascal.", "यह एक विरोधाभास है जो पास्कल में मिलता है।"],
  ["Une métaphore", "/yn me.ta.fɔʁ/", "nom féminin", "Metaphor", "रूपक", "B2", "Literature", "La mer est une métaphore du temps.", "The sea is a metaphor for time.", "समुद्र समय का रूपक है।"],
  ["Un récit", "/œ̃ ʁe.si/", "nom masculin", "Narrative; story", "वृत्तांत; कथा", "B2", "Literature", "Le récit est raconté à la première personne.", "The story is told in the first person.", "कथा उत्तम पुरुष (मैं) में सुनाई गई है।"],
  ["Évoquer", "/e.vɔ.ke/", "verbe", "To evoke; to mention", "याद दिलाना; ज़िक्र करना", "B2", "Literature", "Ce parfum évoque mon enfance.", "This scent evokes my childhood.", "यह ख़ुशबू मेरे बचपन की याद दिलाती है।"],
  ["Se méfier de", "/sə me.fje də/", "verbe pronominal", "To distrust; to beware of", "पर भरोसा न करना; से सावधान रहना", "B2", "Verbs", "Méfiez-vous des apparences.", "Beware of appearances.", "दिखावे से सावधान रहिए।"],

  // ---- Refined & literary (C1) ----
  ["Foncièrement", "/fɔ̃.sjɛʁ.mɑ̃/", "adverbe", "Fundamentally; thoroughly", "मूलतः; पूरी तरह", "C1", "Nuance", "C’est un homme foncièrement honnête.", "He is a fundamentally honest man.", "वह मूलतः ईमानदार आदमी है।"],
  ["Pallier", "/pa.lje/", "verbe", "To make up for; to mitigate", "की भरपाई करना; कम करना", "C1", "Academic", "Il faut pallier le manque de moyens.", "The lack of resources must be offset.", "संसाधनों की कमी की भरपाई करनी होगी।"],
  ["Entraver", "/ɑ̃.tʁa.ve/", "verbe", "To hinder", "बाधा डालना", "C1", "Academic", "La bureaucratie entrave le progrès.", "Bureaucracy hinders progress.", "नौकरशाही प्रगति में बाधा डालती है।"],
  ["Équivoque", "/e.ki.vɔk/", "adjectif", "Ambiguous", "द्विअर्थी; अस्पष्ट", "C1", "Nuance", "Sa réponse reste équivoque.", "His answer remains ambiguous.", "उसका जवाब अस्पष्ट बना हुआ है।"],
  ["Mettre à profit", "/mɛtʁ a pʁɔ.fi/", "locution verbale", "To make the most of", "का भरपूर लाभ उठाना", "C1", "Idioms", "Mettons à profit ce temps libre.", "Let us make the most of this free time.", "आइए इस खाली समय का भरपूर लाभ उठाएँ।"],
  ["Tomber sous le sens", "/tɔ̃.be su lə sɑ̃s/", "locution verbale", "To be self-evident", "स्वतः स्पष्ट होना", "C1", "Idioms", "Cela tombe sous le sens.", "That is self-evident.", "यह बात अपने आप में स्पष्ट है।"],
  ["Faire fi de", "/fɛʁ fi də/", "locution verbale", "To disregard; to scorn", "की उपेक्षा करना", "C1", "Idioms", "Il a fait fi de tous les conseils.", "He disregarded all advice.", "उसने सारी सलाहों को नज़रअंदाज़ कर दिया।"],
  ["Le dénouement", "/lə de.nu.mɑ̃/", "nom masculin", "Outcome; resolution (of a plot)", "परिणति; कथा का अंत", "C1", "Literature", "Le dénouement du roman est inattendu.", "The novel’s resolution is unexpected.", "उपन्यास का अंत अप्रत्याशित है।"],
  ["Un chef-d’œuvre", "/œ̃ ʃɛ.dœvʁ/", "nom masculin", "Masterpiece", "उत्कृष्ट कृति", "C1", "Literature", "Candide est un chef-d’œuvre de l’ironie.", "Candide is a masterpiece of irony.", "कैंडिड व्यंग्य की एक उत्कृष्ट कृति है।"],
  ["Une réminiscence", "/yn ʁe.mi.ni.sɑ̃s/", "nom féminin", "Reminiscence; faint memory", "धुँधली याद", "C1", "Literature", "Ce poème est plein de réminiscences antiques.", "This poem is full of ancient echoes.", "यह कविता प्राचीन स्मृतियों की गूँज से भरी है।"],
];
