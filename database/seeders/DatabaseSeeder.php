<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CustomWord;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonCompletion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Multiple Diverse Users
        $usersData = [
            [
                'email' => 'scholar@academie.fr',
                'name' => 'Alexandre Dumas',
                'password' => Hash::make('french123'),
                'level' => 'B1',
                'role' => 'student',
                'role_title' => 'Academy Scholar',
                'country' => 'Canada',
                'target_goal' => 'Master Victor Hugo and classical French literature',
                'bio' => 'Passionate Quebec scholar studying 19th-century French romanticism and comparative francophone literature.',
                'xp_points' => 1450,
                'current_streak' => 14,
                'theme_preference' => 'amber',
                'last_active_at' => now(),
            ],
            [
                'email' => 'eleonore@sorbonne.edu',
                'name' => 'Éléonore de Valois',
                'password' => Hash::make('french123'),
                'level' => 'C1',
                'role' => 'student',
                'role_title' => 'Senior Research Fellow',
                'country' => 'France',
                'target_goal' => 'Complete doctoral dissertation on Enlightenment philosophy',
                'bio' => 'Advanced researcher focusing on Voltaire and Montesquieu\'s dialectical writings and French administrative rhetoric.',
                'xp_points' => 3280,
                'current_streak' => 42,
                'theme_preference' => 'indigo',
                'last_active_at' => now()->subHours(1),
            ],
            [
                'email' => 'liam@montreal.ca',
                'name' => 'Liam Tremblay',
                'password' => Hash::make('french123'),
                'level' => 'A2',
                'role' => 'student',
                'role_title' => 'Conversationalist',
                'country' => 'Canada',
                'target_goal' => 'Achieve fluent Parisian bistro conversation and travel independence',
                'bio' => 'Software engineer learning bilingual French for work in Montreal and upcoming travels across France.',
                'xp_points' => 820,
                'current_streak' => 7,
                'theme_preference' => 'emerald',
                'last_active_at' => now()->subHours(3),
            ],
            [
                'email' => 'sophie@geneva.ch',
                'name' => 'Sophie Martin',
                'password' => Hash::make('french123'),
                'level' => 'A1',
                'role' => 'student',
                'role_title' => 'Novice Scholar',
                'country' => 'Switzerland',
                'target_goal' => 'Build acoustic foundations, perfect French nasal vowels and greetings',
                'bio' => 'International relations student building foundational French grammar and academic phonetics.',
                'xp_points' => 450,
                'current_streak' => 3,
                'theme_preference' => 'rose',
                'last_active_at' => now()->subDays(1),
            ],
            [
                'email' => 'instructor@academie.fr',
                'name' => 'Prof. Émile Laurent',
                'password' => Hash::make('french123'),
                'level' => 'C2',
                'role' => 'admin',
                'role_title' => 'Faculty Director & Administrator',
                'country' => 'Canada',
                'target_goal' => 'Mentor the next generation of bilingual French scholars',
                'bio' => 'Distinguished Professor of Romance Philology, author of three French grammar treatises and curriculum designer.',
                'xp_points' => 5800,
                'current_streak' => 120,
                'theme_preference' => 'slate',
                'last_active_at' => now()->subMinutes(15),
            ],
            [
                'email' => 'rrathore959910@gmail.com',
                'name' => 'Rahul Rathore',
                'password' => Hash::make('french123'),
                'level' => 'C2',
                'role' => 'admin',
                'role_title' => 'Chief Academic Chancellor',
                'country' => 'India',
                'target_goal' => 'Build high-performance bilingual classical immersion LMS',
                'bio' => 'Lead developer and system architect of Académie de Français platform.',
                'xp_points' => 9999,
                'current_streak' => 150,
                'theme_preference' => 'amber',
                'last_active_at' => now(),
            ],
        ];

        $createdUsers = [];
        foreach ($usersData as $uData) {
            $createdUsers[$uData['email']] = User::updateOrCreate(
                ['email' => $uData['email']],
                $uData
            );
        }

        $primaryUser = $createdUsers['scholar@academie.fr'];

        // 2. Courses & Detailed Lessons
        $coursesData = [
            [
                'title' => 'French 101: Foundations & Phonetics',
                'slug' => 'french-101-foundations',
                'level' => 'A1',
                'category' => 'Foundations',
                'short_desc' => 'Master pristine French pronunciation, the phonetic alphabet, essential greetings, and everyday courtesy.',
                'description' => 'A comprehensive beginner course designed by the Académie Royale to establish perfect pronunciation through IPA transcriptions, nasal vowels, and foundational conversational patterns.',
                'instructor_name' => 'Prof. Émile Laurent',
                'instructor_role' => 'Chair of French Phonetics, Sorbonne',
                'duration_weeks' => 4,
                'lessons_count' => 3,
                'badge_color' => 'amber',
                'icon' => 'sparkles',
                'is_featured' => true,
                'lessons' => [
                    [
                        'order' => 1,
                        'title' => 'The French Alphabet & Pure Vowels',
                        'section_title' => 'Module 1: Acoustic Foundations',
                        'duration_mins' => 15,
                        'french_text' => "Bienvenue à l'Académie Royale ! En français, chaque voyelle possède une clarté absolue. Les voyelles orales de base sont: a, e, i, o, u. Écoutez attentivement le son /y/ dans le mot 'lune' : arrondissez les lèvres tout en disant 'i'. Répétez avec élégance.",
                        'english_translation' => "Welcome to the Royal Academy! In French, each vowel possesses absolute clarity. The basic oral vowels are: a, e, i, o, u. Listen closely to the /y/ sound in 'lune' (moon): round your lips while pronouncing 'ee'. Repeat with elegance.",
                        'audio_phrase' => "Bienvenue à l'Académie Royale. Répétez avec élégance.",
                        'vocabulary_json' => [
                            ['fr' => 'Bienvenue', 'ipa' => '/bjɛ̃.vny/', 'en' => 'Welcome', 'type' => 'interjection'],
                            ['fr' => 'Clarté', 'ipa' => '/klaʁ.te/', 'en' => 'Clarity', 'type' => 'noun (f)'],
                            ['fr' => 'Élégance', 'ipa' => '/e.le.ɡɑ̃s/', 'en' => 'Elegance', 'type' => 'noun (f)'],
                        ],
                        'quiz_question' => "Which French vowel requires rounding your lips while pronouncing the 'ee' sound?",
                        'quiz_options_json' => ['The letter "u" (/y/)', 'The letter "a" (/a/)', 'The letter "o" (/o/)', 'The letter "e" (/ə/)'],
                        'quiz_correct_index' => 0,
                    ],
                    [
                        'order' => 2,
                        'title' => 'Formal Greetings & Courtesies',
                        'section_title' => 'Module 2: The Art of Politeness',
                        'duration_mins' => 20,
                        'french_text' => "En France, la politesse est un art de vivre. On ne commence jamais une conversation sans dire 'Bonjour'. Avec les inconnus et les aînés, le vouvoiement est impératif : 'Comment allez-vous ?' répond à la règle du respect.",
                        'english_translation' => "In France, politeness is an art of living. One never starts a conversation without saying 'Bonjour' (Good day). With strangers and elders, the formal 'vous' is imperative: 'Comment allez-vous ?' (How are you?) fulfills the rule of respect.",
                        'audio_phrase' => "Bonjour Madame, comment allez-vous aujourd'hui ?",
                        'vocabulary_json' => [
                            ['fr' => 'Bonjour', 'ipa' => '/bɔ̃.ʒuʁ/', 'en' => 'Good morning / Hello', 'type' => 'interjection'],
                            ['fr' => 'Vouvoiement', 'ipa' => '/vu.vwa.mɑ̃/', 'en' => 'Using formal "vous"', 'type' => 'noun (m)'],
                            ['fr' => 'Politesse', 'ipa' => '/pɔ.li.tɛs/', 'en' => 'Politeness / Courtesy', 'type' => 'noun (f)'],
                        ],
                        'quiz_question' => "What is the customary formal greeting used when addressing strangers in France?",
                        'quiz_options_json' => ['Comment allez-vous ?', 'Salut, ça roule ?', 'Quoi de neuf ?', 'Tu vas bien ?'],
                        'quiz_correct_index' => 0,
                    ],
                    [
                        'order' => 3,
                        'title' => 'Introducing Yourself & Academic Aspirations',
                        'section_title' => 'Module 3: Personal Identity',
                        'duration_mins' => 25,
                        'french_text' => "Je m'appelle Alexandre et j'étudie la langue française avec passion. J'habite à Paris près du Jardin des Tuileries. Mon souhait le plus cher est de lire Victor Hugo dans le texte original.",
                        'english_translation' => "My name is Alexandre and I study the French language with passion. I live in Paris near the Tuileries Garden. My dearest wish is to read Victor Hugo in the original text.",
                        'audio_phrase' => "Je m'appelle Alexandre et j'étudie la langue française avec passion.",
                        'vocabulary_json' => [
                            ['fr' => "S'appeler", 'ipa' => '/sa.ple/', 'en' => 'To be called / named', 'type' => 'verb'],
                            ['fr' => 'Passion', 'ipa' => '/pa.sjɔ̃/', 'en' => 'Passion / devotion', 'type' => 'noun (f)'],
                            ['fr' => 'Souhait', 'ipa' => '/swɛ/', 'en' => 'Wish / desire', 'type' => 'noun (m)'],
                        ],
                        'quiz_question' => "How do you say 'My name is Alexandre' in French?",
                        'quiz_options_json' => ["Je m'appelle Alexandre", "Mon nom Alexandre est", "Je suis appelé Alexandre", "Alexandre est moi"],
                        'quiz_correct_index' => 0,
                    ],
                ],
            ],

            [
                'title' => 'Café, Culture & Conversation',
                'slug' => 'cafe-culture-conversation',
                'level' => 'A2',
                'category' => 'Conversation',
                'short_desc' => 'Navigate Parisian bistros, boulangeries, train stations, and effortless social dialogues.',
                'description' => 'Immerse yourself into authentic French daily life. From ordering café au lait at a boulevard terrace to asking for directions along the banks of the Seine.',
                'instructor_name' => 'Madame Sylvie Moreau',
                'instructor_role' => 'Linguist & Paris Cultural Historian',
                'duration_weeks' => 5,
                'lessons_count' => 3,
                'badge_color' => 'emerald',
                'icon' => 'chat-bubble-left-right',
                'is_featured' => true,
                'lessons' => [
                    [
                        'order' => 1,
                        'title' => 'At the Traditional Parisian Café',
                        'section_title' => 'Module 1: Gastronomy & Terrasses',
                        'duration_mins' => 20,
                        'french_text' => "Garçon, s'il vous plaît ! Je prendrai un café serré et une carafe d'eau fraîche. Pourriez-vous également m'apporter un croissant chaud ? La terrasse est très agréable ce matin.",
                        'english_translation' => "Waiter, please! I will have an espresso and a carafe of cold tap water. Could you also bring me a warm croissant? The terrace is very pleasant this morning.",
                        'audio_phrase' => "Je prendrai un café serré et un croissant chaud, s'il vous plaît.",
                        'vocabulary_json' => [
                            ['fr' => 'Café serré', 'ipa' => '/ka.fe sɛ.ʁe/', 'en' => 'Espresso / strong coffee', 'type' => 'noun (m)'],
                            ['fr' => 'Carafe d\'eau', 'ipa' => '/ka.ʁaf do/', 'en' => 'Carafe of water (free)', 'type' => 'noun (f)'],
                            ['fr' => 'Terrasse', 'ipa' => '/tɛ.ʁas/', 'en' => 'Outdoor seating terrace', 'type' => 'noun (f)'],
                        ],
                        'quiz_question' => "What is the French phrase for requesting complimentary tap water in a French restaurant?",
                        'quiz_options_json' => ['Une carafe d\'eau', 'Une bouteille de champagne', 'Un grand verre de vin', 'De l\'eau gazeuse payante'],
                        'quiz_correct_index' => 0,
                    ],
                    [
                        'order' => 2,
                        'title' => 'Asking Directions on the Seine',
                        'section_title' => 'Module 2: Parisian Navigation',
                        'duration_mins' => 20,
                        'french_text' => "Pardonnez-moi, monsieur. Pour aller au Musée d'Orsay, faut-il traverser la passerelle Léopold-Sédar-Senghor ou longer le quai Anatole-France ? C'est à environ dix minutes à pied.",
                        'english_translation' => "Excuse me, sir. To reach the Orsay Museum, should one cross the Léopold-Sédar-Senghor footbridge or walk along Quai Anatole-France? It is about a ten-minute walk.",
                        'audio_phrase' => "Pardonnez-moi, quel est le chemin le plus court vers le musée ?",
                        'vocabulary_json' => [
                            ['fr' => 'Passerelle', 'ipa' => '/pas.ʁɛl/', 'en' => 'Footbridge', 'type' => 'noun (f)'],
                            ['fr' => 'Longer', 'ipa' => '/lɔ̃.ʒe/', 'en' => 'To walk along / flank', 'type' => 'verb'],
                            ['fr' => 'Quai', 'ipa' => '/ke/', 'en' => 'Riverbank embankment / platform', 'type' => 'noun (m)'],
                        ],
                        'quiz_question' => "What does the French verb 'longer' mean in navigation?",
                        'quiz_options_json' => ['To walk along the edge of something', 'To get lost', 'To run as fast as possible', 'To turn backward'],
                        'quiz_correct_index' => 0,
                    ],
                    [
                        'order' => 3,
                        'title' => 'Ordering at the Artisanal Boulangerie',
                        'section_title' => 'Module 3: Daily French Crafts',
                        'duration_mins' => 20,
                        'french_text' => "Bonjour Madame. Une baguette de tradition bien cuite et deux pains au chocolat, s'il vous plaît. Ce sera tout pour aujourd'hui. Combien vous dois-je ?",
                        'english_translation' => "Good morning, Madame. A well-baked traditional baguette and two chocolate croissants, please. That will be all for today. How much do I owe you?",
                        'audio_phrase' => "Une baguette de tradition bien cuite, s'il vous plaît.",
                        'vocabulary_json' => [
                            ['fr' => 'Tradition', 'ipa' => '/tʁa.di.sjɔ̃/', 'en' => 'Artisanal baguette standard', 'type' => 'noun (f)'],
                            ['fr' => 'Bien cuite', 'ipa' => '/bjɛ̃ kɥit/', 'en' => 'Well-baked / crusty', 'type' => 'adjective'],
                            ['fr' => 'Pain au chocolat', 'ipa' => '/pɛ̃ o ʃɔ.kɔ.la/', 'en' => 'Chocolate croissant', 'type' => 'noun (m)'],
                        ],
                        'quiz_question' => "If you prefer your French baguette crispy and crusty, what term do you use?",
                        'quiz_options_json' => ['Bien cuite', 'Pas trop cuite', 'Molle', 'Brûlée'],
                        'quiz_correct_index' => 0,
                    ],
                ],
            ],

            [
                'title' => 'Classical Literature: Guided Reading',
                'slug' => 'classical-literature-guided-reading',
                'level' => 'B1',
                'category' => 'Literature',
                'short_desc' => 'Read Saint-Exupéry, Victor Hugo, and Gustave Flaubert in original French with dual-language insight.',
                'description' => 'Delve directly into original 19th and 20th-century French literature. Analyze syntax, metaphors, and rich descriptive adjectives that transform you into an autonomous French reader.',
                'instructor_name' => 'Dr. Julien de Saint-Germain',
                'instructor_role' => 'Professor of Romance Literatures',
                'duration_weeks' => 6,
                'lessons_count' => 3,
                'badge_color' => 'indigo',
                'icon' => 'book-open',
                'is_featured' => true,
                'lessons' => [
                    [
                        'order' => 1,
                        'title' => "Le Petit Prince: L'Apprivoisement",
                        'section_title' => 'Module 1: Antoine de Saint-Exupéry',
                        'duration_mins' => 30,
                        'french_text' => "« Qu'est-ce que signifie apprivoiser ? » demanda le petit prince. « C'est une chose trop oubliée, dit le renard. Ça signifie créer des liens... Tu ne seras pour moi qu'un petit garçon tout semblable à cent mille petits garçons. Mais, si tu m'apprivoises, nous aurons besoin l'un de l'autre. Tu seras pour moi unique au monde. »",
                        'english_translation' => "'What does to tame mean?' asked the little prince. 'It is an act too often forgotten,' said the fox. 'It means to create ties... You will be for me nothing more than a little boy just like a hundred thousand other little boys. But if you tame me, then we shall need each other. To me, you will be unique in all the world.'",
                        'audio_phrase' => "On ne voit bien qu'avec le cœur. L'essentiel est invisible pour les yeux.",
                        'vocabulary_json' => [
                            ['fr' => 'Apprivoiser', 'ipa' => '/a.pʁi.vwa.ze/', 'en' => 'To tame / cultivate affection', 'type' => 'verb'],
                            ['fr' => 'Créer des liens', 'ipa' => '/kʁe.e de ljɛ̃/', 'en' => 'To create bonds / ties', 'type' => 'expression'],
                            ['fr' => 'Unique au monde', 'ipa' => '/y.nik o mɔ̃d/', 'en' => 'Unique in all the world', 'type' => 'expression'],
                        ],
                        'quiz_question' => "According to the Fox in Le Petit Prince, what is the profound definition of 'apprivoiser'?",
                        'quiz_options_json' => ['Créer des liens (To create bonds/ties)', 'Acheter un animal', 'Donner des ordres', 'Voyager sur une autre planète'],
                        'quiz_correct_index' => 0,
                    ],
                    [
                        'order' => 2,
                        'title' => "Les Misérables: Jean Valjean & Monseigneur Myriel",
                        'section_title' => 'Module 2: Victor Hugo',
                        'duration_mins' => 35,
                        'french_text' => "L'évêque regarda Jean Valjean avec une infinie bonté. « Jean Valjean, mon frère, vous n'appartenez plus au mal, mais au bien. C'est votre âme que j'achète ; je la retire aux pensées sombres et à l'esprit de perdition, et je la donne à Dieu. »",
                        'english_translation' => "The bishop looked upon Jean Valjean with infinite kindness. 'Jean Valjean, my brother, you belong no longer to evil, but to good. It is your soul that I buy from you; I withdraw it from dark thoughts and the spirit of perdition, and I give it to God.'",
                        'audio_phrase' => "Jean Valjean, mon frère, vous n'appartenez plus au mal, mais au bien.",
                        'vocabulary_json' => [
                            ['fr' => 'Bonté', 'ipa' => '/bɔ̃.te/', 'en' => 'Goodness / kindness', 'type' => 'noun (f)'],
                            ['fr' => 'Appartenir', 'ipa' => '/a.paʁ.tə.niʁ/', 'en' => 'To belong to', 'type' => 'verb'],
                            ['fr' => 'Perdition', 'ipa' => '/pɛʁ.di.sjɔ̃/', 'en' => 'Perdition / ruin', 'type' => 'noun (f)'],
                        ],
                        'quiz_question' => "What moral gift did the Bishop grant Jean Valjean with the silver candlesticks?",
                        'quiz_options_json' => ['Forgiveness and redemption for his soul', 'A map to escape France', 'A prison pardon certificate', 'Gold coins'],
                        'quiz_correct_index' => 0,
                    ],
                    [
                        'order' => 3,
                        'title' => "Madame Bovary: The Yearnings of Emma",
                        'section_title' => 'Module 3: Gustave Flaubert',
                        'duration_mins' => 30,
                        'french_text' => "Au fond de son âme, pourtant, elle attendait un événement. Comme les matelots en détresse, elle promenait sur la solitude de sa vie des yeux désespérés, cherchant au loin quelque voile blanche dans les brumes de l'horizon.",
                        'english_translation' => "In the depths of her soul, yet, she was waiting for something to happen. Like shipwrecked sailors in distress, she turned despairing eyes upon the loneliness of her life, searching far away for some white sail amid the mists of the horizon.",
                        'audio_phrase' => "Elle cherchait au loin quelque voile blanche dans les brumes de l'horizon.",
                        'vocabulary_json' => [
                            ['fr' => 'Solitude', 'ipa' => '/sɔ.li.tyd/', 'en' => 'Solitude / loneliness', 'type' => 'noun (f)'],
                            ['fr' => 'Détresse', 'ipa' => '/de.tʁɛs/', 'en' => 'Distress / anguish', 'type' => 'noun (f)'],
                            ['fr' => 'Brume', 'ipa' => '/bʁym/', 'en' => 'Mist / haze', 'type' => 'noun (f)'],
                        ],
                        'quiz_question' => "What poetic metaphor does Flaubert use to depict Emma Bovary's internal longing?",
                        'quiz_options_json' => ['Sailors in distress watching for a distant white sail', 'A bird trapped in a cage', 'A solitary candle in the wind', 'A barren desert landscape'],
                        'quiz_correct_index' => 0,
                    ],
                ],
            ],

            [
                'title' => 'Advanced Syntax & Subjunctive Mastery',
                'slug' => 'advanced-syntax-subjunctive',
                'level' => 'B2',
                'category' => 'Advanced Syntax',
                'short_desc' => 'Conquer the Subjunctive mood, hypothetical clauses (si + plus-que-parfait), and persuasive rhetoric.',
                'description' => 'Elevate your French from communicative to sophisticated. Learn when emotion, doubt, obligation, and necessity demand the Subjunctive mood, and master literary past tenses.',
                'instructor_name' => 'Prof. Claire Vaudoyer',
                'instructor_role' => 'Académie de Paris Fellow',
                'duration_weeks' => 7,
                'lessons_count' => 3,
                'badge_color' => 'purple',
                'icon' => 'academic-cap',
                'is_featured' => false,
                'lessons' => [
                    [
                        'order' => 1,
                        'title' => 'The Triggers of the Subjunctive Mood',
                        'section_title' => 'Module 1: Doubt, Desire & Emotion',
                        'duration_mins' => 30,
                        'french_text' => "Le subjonctif n'est pas un temps, c'est un mode. Il exprime la subjectivité, le doute, la volonté et la nécessité : « Il faut que vous veniez », « Je doute qu'il sache la vérité ». Dès que la certitude faiblit, le subjonctif s'impose.",
                        'english_translation' => "The subjunctive is not a tense, it is a mood. It expresses subjectivity, doubt, will, and necessity: 'It is necessary that you come', 'I doubt that he knows the truth'. As soon as certainty wavers, the subjunctive becomes compulsory.",
                        'audio_phrase' => "Il faut que nous apprenions les nuances de la langue française.",
                        'vocabulary_json' => [
                            ['fr' => 'Subjonctif', 'ipa' => '/syb.ʒɔ̃k.tif/', 'en' => 'Subjunctive mood', 'type' => 'noun (m)'],
                            ['fr' => 'Volonté', 'ipa' => '/vɔ.lɔ̃.te/', 'en' => 'Willpower / intent', 'type' => 'noun (f)'],
                            ['fr' => 'Nécessité', 'ipa' => '/ne.sɛ.si.te/', 'en' => 'Necessity', 'type' => 'noun (f)'],
                        ],
                        'quiz_question' => "Which of the following phrases MUST trigger the subjunctive mood in French?",
                        'quiz_options_json' => ['Il faut que...', 'Je sais que...', 'Il est certain que...', 'Je pense que...'],
                        'quiz_correct_index' => 0,
                    ],
                    [
                        'order' => 2,
                        'title' => 'Hypothetical Clauses: Si + Conditionnel',
                        'section_title' => 'Module 2: Speculative Logic',
                        'duration_mins' => 25,
                        'french_text' => "« Si j'avais su, je serais venu plus tôt. » L'irréel du passé combine la conjonction 'si' suivie du plus-que-parfait avec le conditionnel passé dans la proposition principale. Une formule incontournable dans tout discours éloquent.",
                        'english_translation' => "'If I had known, I would have come earlier.' The past hypothetical combines 'si' with the pluperfect, paired with the past conditional in the main clause. An essential formulation in eloquent speech.",
                        'audio_phrase' => "Si j'avais eu le temps, j'aurais exploré toute la bibliothèque.",
                        'vocabulary_json' => [
                            ['fr' => 'Incontournable', 'ipa' => '/ɛ̃.kɔ̃.tuʁ.nabl/', 'en' => 'Essential / unavoidable', 'type' => 'adjective'],
                            ['fr' => 'Éloquent', 'ipa' => '/e.lɔ.kɑ̃/', 'en' => 'Eloquent / moving', 'type' => 'adjective'],
                            ['fr' => 'Hypothèse', 'ipa' => '/i.pɔ.tɛz/', 'en' => 'Hypothesis / assumption', 'type' => 'noun (f)'],
                        ],
                        'quiz_question' => "In an unreal past hypothetical clause, what tense directly follows 'si'?",
                        'quiz_options_json' => ['Plus-que-parfait (Pluperfect)', 'Futur simple', 'Présent de l\'indicatif', 'Subjonctif'],
                        'quiz_correct_index' => 0,
                    ],
                    [
                        'order' => 3,
                        'title' => 'Refined Transitions & Connecteurs Logiques',
                        'section_title' => 'Module 3: Rhetorical Cohesion',
                        'duration_mins' => 30,
                        'french_text' => "Pour structurer un essai universitaire ou un débat, maniez les connecteurs de haut vol : 'Néanmoins', 'En revanche', 'D'ores et déjà', 'Nonobstant'. Ils apportent fluidité et autorité intellectuelle à votre propos.",
                        'english_translation' => "To structure an academic essay or debate, employ high-register connectors: 'Nevertheless', 'On the other hand', 'Already / as of now', 'Notwithstanding'. They confer fluidity and intellectual authority upon your argument.",
                        'audio_phrase' => "Néanmoins, il convient d'examiner les arguments contradictoires.",
                        'vocabulary_json' => [
                            ['fr' => 'Néanmoins', 'ipa' => '/ne.ɑ̃.mwɛ̃/', 'en' => 'Nevertheless / yet', 'type' => 'adverb'],
                            ['fr' => 'Nonobstant', 'ipa' => '/nɔ.nɔbs.tɑ̃/', 'en' => 'Notwithstanding / in spite of', 'type' => 'preposition'],
                            ['fr' => 'Fluidité', 'ipa' => '/flɥi.di.te/', 'en' => 'Smoothness / fluidity', 'type' => 'noun (f)'],
                        ],
                        'quiz_question' => "Which transitional adverb conveys 'nevertheless' with scholarly distinction?",
                        'quiz_options_json' => ['Néanmoins', 'Mais bon', 'Alors', 'Donc'],
                        'quiz_correct_index' => 0,
                    ],
                ],
            ],

            [
                'title' => 'Haute Culture & Intellectual Discourse',
                'slug' => 'haute-culture-intellectual-discourse',
                'level' => 'C1',
                'category' => 'Rhetoric & Philosophy',
                'short_desc' => 'Engage with Enlightenment philosophy, literary critiques, and dialectical French debate.',
                'description' => 'The pinnacle of French academic mastery. Analyze Voltaire, Rousseau, Camus, and Sartre; compose balanced dissertations in three parts (Thèse, Antithèse, Synthèse).',
                'instructor_name' => 'Prof. Henri de Montcalm',
                'instructor_role' => 'Fellow of the Académie Française',
                'duration_weeks' => 8,
                'lessons_count' => 3,
                'badge_color' => 'rose',
                'icon' => 'globe-alt',
                'is_featured' => false,
                'lessons' => [
                    [
                        'order' => 1,
                        'title' => 'Voltaire & The Spirit of the Enlightenment',
                        'section_title' => 'Module 1: Philosophical Irony',
                        'duration_mins' => 35,
                        'french_text' => "L'ironie voltairienne est une arme d'une redoutable précision. Dans 'Candide', le philosophe dénonce l'optimisme béat à travers la formule célèbre : « Tout est pour le mieux dans le meilleur des mondes possibles ». Savoir discerner le second degré est l'insigne de la maîtrise.",
                        'english_translation' => "Voltairian irony is a weapon of formidable precision. In 'Candide', the philosopher denounces blind optimism through the famous maxim: 'All is for the best in the best of possible worlds'. Discerning the second degree of irony is the hallmark of French mastery.",
                        'audio_phrase' => "Il faut cultiver notre jardin, telle est la conclusion de Candide.",
                        'vocabulary_json' => [
                            ['fr' => 'Redoutable', 'ipa' => '/ʁə.du.tabl/', 'en' => 'Formidable / fearsome', 'type' => 'adjective'],
                            ['fr' => 'Béat', 'ipa' => '/be.a/', 'en' => 'Smug / blissfully unaware', 'type' => 'adjective'],
                            ['fr' => 'Second degré', 'ipa' => '/sə.ɡɔ̃ də.ɡʁe/', 'en' => 'Irony / subtext', 'type' => 'expression'],
                        ],
                        'quiz_question' => "What is the famous philosophical takeaway at the conclusion of Voltaire's Candide?",
                        'quiz_options_json' => ['Il faut cultiver notre jardin', 'Il faut conquérir le monde', 'L\'argent fait le bonheur', 'Le destin décide de tout'],
                        'quiz_correct_index' => 0,
                    ],
                    [
                        'order' => 2,
                        'title' => "Camus: L'Étranger & The Absurd",
                        'section_title' => 'Module 2: Existential Realism',
                        'duration_mins' => 40,
                        'french_text' => "« Aujourd'hui, maman est morte. Ou peut-être hier, je ne sais pas. » Cet incipit légendaire, dépouillé de tout ornement lyrique, plonge d'emblée le lecteur dans l'indifférence métaphysique de Meursault. La clarté du style au service de la philosophie de l'absurde.",
                        'english_translation' => "'Mother died today. Or maybe yesterday, I don't know.' This legendary opening sentence, stripped of all lyrical adornment, plunges the reader immediately into Meursault's metaphysical indifference. A clarity of style at the service of the philosophy of the absurd.",
                        'audio_phrase' => "Aujourd'hui, maman est morte. Ou peut-être hier, je ne sais pas.",
                        'vocabulary_json' => [
                            ['fr' => 'Incipit', 'ipa' => '/ɛ̃.si.pit/', 'en' => 'Opening sentence of a book', 'type' => 'noun (m)'],
                            ['fr' => 'Dépouillé', 'ipa' => '/de.pu.je/', 'en' => 'Sparse / unadorned', 'type' => 'adjective'],
                            ['fr' => 'D\'emblée', 'ipa' => '/dɑ̃.ble/', 'en' => 'From the very outset', 'type' => 'adverb'],
                        ],
                        'quiz_question' => "What is the literary term for the opening sentence or passage of a literary masterpiece?",
                        'quiz_options_json' => ['Incipit', 'Épilogue', 'Dénouement', 'Chœur'],
                        'quiz_correct_index' => 0,
                    ],
                    [
                        'order' => 3,
                        'title' => 'The Classic French Dissertation Structure',
                        'section_title' => 'Module 3: Dialectical Architecture',
                        'duration_mins' => 45,
                        'french_text' => "L'exercice canonique français de la dissertation repose sur le triptyque hégélien : Thèse (l'affirmation initiale), Antithèse (la contradiction argumentée), et Synthèse (le dépassement transcendant). Ce rigoureux modèle intellectuel façonne toute la haute administration française.",
                        'english_translation' => "The canonical French essay exercise rests upon the Hegelian triptych: Thesis (the initial proposition), Antithesis (the argued contradiction), and Synthesis (the transcendent resolution). This rigorous intellectual framework shapes the entire French administrative elite.",
                        'audio_phrase' => "Thèse, antithèse et synthèse forment l'art dialectique français.",
                        'vocabulary_json' => [
                            ['fr' => 'Canonique', 'ipa' => '/ka.nɔ.nik/', 'en' => 'Canonical / standard', 'type' => 'adjective'],
                            ['fr' => 'Dépassement', 'ipa' => '/de.pas.mɑ̃/', 'en' => 'Transcendence / surpassing', 'type' => 'noun (m)'],
                            ['fr' => 'Façonner', 'ipa' => '/fa.sɔ.ne/', 'en' => 'To shape / mold', 'type' => 'verb'],
                        ],
                        'quiz_question' => "What are the three pillars of a traditional French academic dissertation?",
                        'quiz_options_json' => ['Thèse, Antithèse, Synthèse', 'Introduction, Paragraphe, Fin', 'Pour, Contre, Neutre', 'Début, Milieu, Conclusion'],
                        'quiz_correct_index' => 0,
                    ],
                ],
            ],
        ];

        foreach ($coursesData as $cData) {
            $lessons = $cData['lessons'];
            unset($cData['lessons']);

            $course = Course::updateOrCreate(['slug' => $cData['slug']], $cData);

            foreach ($lessons as $lData) {
                Lesson::updateOrCreate(
                    ['course_id' => $course->id, 'order' => $lData['order']],
                    $lData
                );
            }
        }

        // 3. Enroll Alexandre Dumas in 4 courses
        $allCourses = Course::all();
        if ($allCourses->count() >= 4) {
            // Course 1: 100% completed
            $c1 = $allCourses[0];
            Enrollment::updateOrCreate(
                ['user_id' => $primaryUser->id, 'course_id' => $c1->id],
                ['progress_percent' => 100, 'completed_lessons_count' => 3, 'status' => 'completed', 'last_accessed_at' => now()]
            );
            foreach ($c1->lessons as $les) {
                LessonCompletion::updateOrCreate(['user_id' => $primaryUser->id, 'lesson_id' => $les->id]);
            }

            // Course 2: 66% completed
            $c2 = $allCourses[1];
            Enrollment::updateOrCreate(
                ['user_id' => $primaryUser->id, 'course_id' => $c2->id],
                ['progress_percent' => 66, 'completed_lessons_count' => 2, 'status' => 'in_progress', 'last_accessed_at' => now()->subDay()]
            );
            $l2 = $c2->lessons()->take(2)->get();
            foreach ($l2 as $les) {
                LessonCompletion::updateOrCreate(['user_id' => $primaryUser->id, 'lesson_id' => $les->id]);
            }

            // Course 3: 33% completed
            $c3 = $allCourses[2];
            Enrollment::updateOrCreate(
                ['user_id' => $primaryUser->id, 'course_id' => $c3->id],
                ['progress_percent' => 33, 'completed_lessons_count' => 1, 'status' => 'in_progress', 'last_accessed_at' => now()->subHours(2)]
            );
            $l3 = $c3->lessons()->first();
            if ($l3) {
                LessonCompletion::updateOrCreate(['user_id' => $primaryUser->id, 'lesson_id' => $l3->id]);
            }

            // Course 4: 10% completed
            $c4 = $allCourses[3];
            Enrollment::updateOrCreate(
                ['user_id' => $primaryUser->id, 'course_id' => $c4->id],
                ['progress_percent' => 10, 'completed_lessons_count' => 0, 'status' => 'in_progress', 'last_accessed_at' => now()->subDays(3)]
            );

            // Enroll Éléonore de Valois in Course 3, 4, 5
            $eleonore = $createdUsers['eleonore@sorbonne.edu'];
            Enrollment::updateOrCreate(
                ['user_id' => $eleonore->id, 'course_id' => $allCourses[3]->id],
                ['progress_percent' => 100, 'completed_lessons_count' => 3, 'status' => 'completed', 'last_accessed_at' => now()]
            );
            Enrollment::updateOrCreate(
                ['user_id' => $eleonore->id, 'course_id' => $allCourses[4]->id],
                ['progress_percent' => 75, 'completed_lessons_count' => 2, 'status' => 'in_progress', 'last_accessed_at' => now()]
            );

            // Enroll Liam Tremblay in Course 1 & 2
            $liam = $createdUsers['liam@montreal.ca'];
            Enrollment::updateOrCreate(
                ['user_id' => $liam->id, 'course_id' => $allCourses[0]->id],
                ['progress_percent' => 100, 'completed_lessons_count' => 3, 'status' => 'completed', 'last_accessed_at' => now()]
            );
            Enrollment::updateOrCreate(
                ['user_id' => $liam->id, 'course_id' => $allCourses[1]->id],
                ['progress_percent' => 33, 'completed_lessons_count' => 1, 'status' => 'in_progress', 'last_accessed_at' => now()]
            );
        }

        // 4. Classical Lexicon Vocabulary with Hindi & Use Cases
        $customWords = [
            [
                'word' => 'Éphémère',
                'ipa' => '/e.fe.mɛʁ/',
                'meaning' => 'Lasting for a very short time; fleeting or momentary.',
                'meaning_hi' => 'क्षणभंगुर, अल्पकालिक (जो बहुत कम समय के लिए रहे).',
                'level' => 'B2',
                'category' => 'Philosophy & Nature',
                'example_fr' => 'La beauté des fleurs de cerisier est éphémère mais laisse un souvenir impérissable.',
                'example_en' => 'The beauty of cherry blossoms is fleeting but leaves an imperishable memory.',
                'example_hi' => 'चेरी के फूलों की सुंदरता क्षणभंगुर होती है लेकिन एक अमिट स्मृति छोड़ जाती है।',
                'use_case' => 'Used in literature, art, and philosophy to describe transient moments, emotions, seasons, or mortal beauty.',
                'source' => 'Hermes Classical Core'
            ],
            [
                'word' => 'Bienveillance',
                'ipa' => '/bjɛ̃.vɛ.jɑ̃s/',
                'meaning' => 'Disposition to do good; compassionate goodwill and active kindness.',
                'meaning_hi' => 'सद्भावना, परोपकार, कृपाशीलता (दूसरों का भला चाहने का भाव).',
                'level' => 'B1',
                'category' => 'Classical Ethics',
                'example_fr' => 'Le professeur écouta les interrogations de ses élèves avec une profonde bienveillance.',
                'example_en' => 'The professor listened to his students\' questions with profound benevolence.',
                'example_hi' => 'प्रोफेसर ने अपने छात्रों के प्रश्नों को गहरी सद्भावना के साथ सुना।',
                'use_case' => 'Used in formal, educational, and professional settings to express genuine empathy, caring leadership, and mutual respect.',
                'source' => 'Hermes Classical Core'
            ],
            [
                'word' => 'Flânerie',
                'ipa' => '/fla.nʁi/',
                'meaning' => 'The act of leisurely and aimlessly strolling to observe society and urban life.',
                'meaning_hi' => 'चहलकदमी, बिना किसी निश्चित लक्ष्य के आनंदपूर्वक घूमना.',
                'level' => 'B2',
                'category' => 'Parisian Culture',
                'example_fr' => 'Une après-midi de flânerie dans les ruelles du Marais apaise l\'esprit.',
                'example_en' => 'An afternoon of aimless strolling through the alleys of the Marais soothes the mind.',
                'example_hi' => 'मारे की गलियों में बिना किसी उद्देश्य के एक दोपहर की चहलकदमी मन को शांत करती है।',
                'use_case' => 'A quintessential 19th-century French literary concept (Baudelaire), used when walking slowly through streets, museums, or gardens with curiosity.',
                'source' => 'Hermes Classical Core'
            ],
            [
                'word' => 'Retrouvailles',
                'ipa' => '/ʁə.tʁu.vaj/',
                'meaning' => 'The deep joy and warmth of reuniting with someone after a long separation.',
                'meaning_hi' => 'लंबे समय बाद मिलन का सुख, बिछड़े हुए प्रियजनों की सुखद भेंट.',
                'level' => 'B1',
                'category' => 'Emotion & Relationships',
                'example_fr' => 'Leurs retrouvailles à la gare Montparnasse furent pleines de larmes de joie.',
                'example_en' => 'Their reunion at the Montparnasse train station was filled with tears of joy.',
                'example_hi' => 'मोंटपार्नास स्टेशन पर उनका पुनर्मिलन खुशी के आंसुओं से भरा था।',
                'use_case' => 'Used when meeting childhood friends, family, or partners after months or years apart. Untranslatable directly into English with a single word.',
                'source' => 'Hermes Classical Core'
            ],
            [
                'word' => 'Dépaysement',
                'ipa' => '/de.pɛ.iz.mɑ̃/',
                'meaning' => 'The exhilarating disorientation of being in an unfamiliar cultural or geographic setting.',
                'meaning_hi' => 'अजनबियत का सुखद अहसास, किसी नई जगह जाने पर होने वाला अनूठा अहसास.',
                'level' => 'B2',
                'category' => 'Travel & Immersion',
                'example_fr' => 'Voyager au cœur des villages provençaux procure un dépaysement revigorant.',
                'example_en' => 'Traveling through the heart of Provençal villages brings an invigorating change of scenery.',
                'example_hi' => 'प्रोवेंस के गांवों के बीच यात्रा करना अजनबियत और ताजगी का अनूठा सुखद अहसास कराता है।',
                'use_case' => 'Used when you feel completely transported out of your usual routine into a new country or landscape.',
                'source' => 'Hermes Classical Core'
            ],
            [
                'word' => 'Serein',
                'ipa' => '/sə.ʁɛ̃/',
                'meaning' => 'Untroubled, peaceful, calm, and clear-minded.',
                'meaning_hi' => 'शांत, निश्चिंत, गंभीर और स्थिर चित्त वाला.',
                'level' => 'B1',
                'category' => 'Classical State of Mind',
                'example_fr' => 'Malgré la tempête qui grondait dehors, il conserva un regard serein et confiant.',
                'example_en' => 'Despite the storm raging outside, he maintained a serene and confident gaze.',
                'example_hi' => 'बाहर गूंजते तूफान के बावजूद, उसने एक शांत और आश्वस्त दृष्टिकोण बनाए रखा।',
                'use_case' => 'Used in everyday speech and literature to describe weather, skies, or a tranquil composed person facing pressure.',
                'source' => 'Hermes Classical Core'
            ],
            [
                'word' => 'Crépuscule',
                'ipa' => '/kʁe.pys.kyl/',
                'meaning' => 'Twilight; the luminous period of gentle glowing light between sunset and complete dark.',
                'meaning_hi' => 'गोधूलि वेला, सांझ, संध्याकाल (दिन ढलने और रात होने के बीच का समय).',
                'level' => 'B1',
                'category' => 'Literary Poetry',
                'example_fr' => 'Au crépuscule, les toits de Paris s\'illuminent de reflets d\'or et de pourpre.',
                'example_en' => 'At twilight, Parisian rooftops illuminate with reflections of gold and purple.',
                'example_hi' => 'गोधूलि वेला में, पेरिस की छतें सोने और बैंगनी रंगों की आभा से चमक उठती हैं।',
                'use_case' => 'Used in romantic, descriptive, and poetic writing to evoke evening mood and the fading rays of the sun.',
                'source' => 'Hermes Classical Core'
            ],
            [
                'word' => 'Ineffable',
                'ipa' => '/i.nɛ.fabl/',
                'meaning' => 'Indescribable; too immense, sacred, or beautiful to be expressed in mere human words.',
                'meaning_hi' => 'अवर्णनीय, अकथनीय (जो शब्दों में बयां न किया जा सके).',
                'level' => 'C1',
                'category' => 'High Classical & Aesthetics',
                'example_fr' => 'La vue des Alpes enneigées au lever du soleil procure une émotion ineffable.',
                'example_en' => 'The view of the snow-capped Alps at sunrise evokes an ineffable emotion.',
                'example_hi' => 'सूर्योदय के समय बर्फ से ढके आल्प्स का दृश्य एक अवर्णनीय अनुभूति पैदा करता है।',
                'use_case' => 'Used in philosophical, spiritual, or aesthetic contexts when encountering breathtaking art, nature, or divine emotions.',
                'source' => 'Hermes Classical Core'
            ],
            [
                'word' => 'Clairvoyance',
                'ipa' => '[klɛʁ.vwa.jɑ̃s]',
                'meaning' => 'Clear-sightedness, keen perception, or astute discernment.',
                'meaning_hi' => 'सटीक दूरदर्शिता, गहरी अंतर्दृष्टि या सूक्ष्म विवेक।',
                'level' => 'B2',
                'category' => 'Classical Philosophy',
                'example_fr' => 'Sa clairvoyance remarquable a sauvé le domaine de la ruine financière.',
                'example_en' => 'His remarkable clear-sightedness saved the estate from financial ruin.',
                'example_hi' => 'उनकी असाधारण दूरदर्शिता ने रियासत को वित्तीय बर्बादी से बचा लिया।',
                'use_case' => 'Used in intellectual debates, classical literature, and strategic discussions to praise someone\'s exceptional foresight and judgment.',
                'source' => 'Hermes Daily Dispatch'
            ],
        ];

        foreach ($customWords as $cw) {
            CustomWord::updateOrCreate(
                ['user_id' => $primaryUser->id, 'word' => $cw['word']],
                $cw
            );
        }
    }
}
