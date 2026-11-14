<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CustomWord;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonCompletion;
use App\Services\StreakService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Enforce streak reset if a day was missed
        StreakService::checkAndResetStreak($user);
        $user->refresh();
        $weeklyActivity = StreakService::getWeeklyActivity($user);

        // User's enrollments with course details
        $enrollments = Enrollment::with(['course.lessons'])
            ->where('user_id', $user->id)
            ->get();

        $enrolledCourseIds = $enrollments->pluck('course_id')->toArray();

        // All courses
        $allCourses = Course::withCount('lessons')->get();

        // Calculate Stats
        $enrolledCount = $enrollments->count();
        $completedLessonsTotal = LessonCompletion::where('user_id', $user->id)->count();
        $avgProgress = $enrolledCount > 0 ? round($enrollments->avg('progress_percent')) : 0;
        $totalXp = $user->xp_points;
        $streak = $user->current_streak;

        // Next lesson to continue
        $activeEnrollment = $enrollments->where('status', 'in_progress')->first() ?? $enrollments->first();
        $nextLesson = null;
        if ($activeEnrollment) {
            $completedLessonIds = LessonCompletion::where('user_id', $user->id)
                ->pluck('lesson_id')
                ->toArray();
            $nextLesson = $activeEnrollment->course->lessons
                ->whereNotIn('id', $completedLessonIds)
                ->first();
            if (!$nextLesson) {
                $nextLesson = $activeEnrollment->course->lessons->first();
            }
        }

        // Custom vocabulary words & Classical Lexicon
        $customWords = CustomWord::where('user_id', $user->id)
            ->latest()
            ->get();

        $allVocabulary = CustomWord::orderBy('id', 'desc')->get();

        // Classical Books list
        $books = $this->getBooksData();

        // Daily Sentences
        $sentences = $this->getSentencesData();

        // Dictionary core words
        $coreDictionary = $this->getCoreDictionaryData();

        // Active Theme
        $activeTheme = session('theme_preference', $user->theme_preference ?? 'amber');

        // All Scholars in User Database
        $allScholars = \App\Models\User::withCount(['enrollments', 'completions'])
            ->orderBy('xp_points', 'desc')
            ->get();

        return view('dashboard', compact(
            'user',
            'enrollments',
            'enrolledCourseIds',
            'allCourses',
            'enrolledCount',
            'completedLessonsTotal',
            'avgProgress',
            'totalXp',
            'streak',
            'nextLesson',
            'activeEnrollment',
            'customWords',
            'books',
            'sentences',
            'coreDictionary',
            'activeTheme',
            'allScholars',
            'weeklyActivity',
            'allVocabulary'
        ));
    }

    public function updateTheme(Request $request)
    {
        $theme = $request->input('theme', 'amber');
        $validThemes = ['amber', 'emerald', 'indigo', 'rose', 'slate'];
        if (!in_array($theme, $validThemes)) {
            $theme = 'amber';
        }

        $user = Auth::user();
        if ($user) {
            $user->theme_preference = $theme;
            $user->save();
        }

        session(['theme_preference' => $theme]);

        return response()->json(['status' => 'ok', 'theme' => $theme]);
    }

    private function getBooksData(): array
    {
        return [
            [
                'id' => 'petit-prince',
                'title' => 'Le Petit Prince',
                'author' => 'Antoine de Saint-Exupéry',
                'year' => '1943',
                'level' => 'A2–B1',
                'badge' => 'Philosophical Tale',
                'frenchExcerpt' => "« On ne voit bien qu'avec le cœur. L'essentiel est invisible pour les yeux. » C'est le temps que tu as perdu pour ta rose qui fait ta rose si importante. Les hommes ont oublié cette vérité.",
                'englishExcerpt' => "'It is only with the heart that one can see rightly; what is essential is invisible to the eye.' It is the time you have wasted for your rose that makes your rose so important. Men have forgotten this truth."
            ],
            [
                'id' => 'miserables',
                'title' => 'Les Misérables',
                'author' => 'Victor Hugo',
                'year' => '1862',
                'level' => 'B2–C1',
                'badge' => 'Epic Masterpiece',
                'frenchExcerpt' => "Tant qu'il existera, par le fait des lois et des mœurs, une damnation sociale créant artificiellement, en pleine civilisation, des enfers, et compliquant d'une fatalité humaine la destinée qui est divine...",
                'englishExcerpt' => "So long as there shall exist, by reason of law and manners, a social condemnation, which, in the midst of civilization, creates artificial hells, and complicates destiny which is divine with human fatality..."
            ],
            [
                'id' => 'madame-bovary',
                'title' => 'Madame Bovary',
                'author' => 'Gustave Flaubert',
                'year' => '1857',
                'level' => 'B2–C1',
                'badge' => 'Realist Romance',
                'frenchExcerpt' => "Elle souhaitait à la fois mourir et habiter Paris. Chaque matin, à son réveil, elle espérait que ce jour-là lui apporterait une lettre ou une visite qui changerait le cours de sa vie morne.",
                'englishExcerpt' => "She wished at the same time to die and to live in Paris. Every morning, on waking, she hoped that that very day would bring her a letter or a visit that would change the course of her dreary life."
            ],
            [
                'id' => 'etranger',
                'title' => "L'Étranger",
                'author' => 'Albert Camus',
                'year' => '1942',
                'level' => 'B1–B2',
                'badge' => 'Existential Classic',
                'frenchExcerpt' => "Aujourd'hui, maman est morte. Ou peut-être hier, je ne sais pas. J'ai reçu un télégramme de l'asile : « Mère décédée. Enterrement demain. Sentiments distingués. » Cela ne veut rien dire. C'était peut-être hier.",
                'englishExcerpt' => "Mother died today. Or maybe yesterday; I can't be sure. The telegram from the home says: 'Mother deceased. Funeral tomorrow. Deep sympathy.' This does not mean anything. Perhaps it was yesterday."
            ],
            [
                'id' => 'verne',
                'title' => 'Vingt Mille Lieues sous les mers',
                'author' => 'Jules Verne',
                'year' => '1870',
                'level' => 'B1–B2',
                'badge' => 'Scientific Adventure',
                'frenchExcerpt' => "« La mer est tout ! Elle couvre les sept dixièmes du globe terrestre. Son souffle est pur et sain. C'est l'immense désert où l'homme n'est jamais seul, car il sent frémir la vie à ses côtés. »",
                'englishExcerpt' => "'The sea is everything! It covers seven-tenths of the terrestrial globe. Its breath is pure and healthy. It is an immense desert where man is never lonely, for he feels life stirring on all sides.'"
            ]
        ];
    }

    private function getCoreDictionaryData(): array
    {
        return [
            ['fr' => 'Éphémère', 'ipa' => '/e.fe.mɛʁ/', 'typeLabel' => 'adjective', 'level' => 'B2', 'en' => 'Lasting for a very short time; fleeting.', 'exFr' => 'La beauté des roses sauvages est éphémère mais éternelle en souvenir.', 'exEn' => 'The beauty of wild roses is fleeting yet eternal in memory.'],
            ['fr' => 'Serein', 'ipa' => '/sə.ʁɛ̃/', 'typeLabel' => 'adjective', 'level' => 'B1', 'en' => 'Calm, untroubled, and peaceful.', 'exFr' => 'Il conserva un esprit serein malgré le tumulte de la foule.', 'exEn' => 'He maintained a serene mind despite the uproar of the crowd.'],
            ['fr' => 'Crépuscule', 'ipa' => '/kʁe.pys.kyl/', 'typeLabel' => 'noun (m)', 'level' => 'B1', 'en' => 'Twilight; the gentle soft light before total nightfall.', 'exFr' => 'Au crépuscule, le ciel de Paris se teinte d\'or et de violet profond.', 'exEn' => 'At twilight, the Parisian sky turns shades of gold and deep violet.'],
            ['fr' => 'Ineffable', 'ipa' => '/i.nɛ.fabl/', 'typeLabel' => 'adjective', 'level' => 'C1', 'en' => 'Too great or beautiful to be expressed in human words.', 'exFr' => 'La splendeur de la cathédrale procure une émotion ineffable.', 'exEn' => 'The cathedral\'s splendor evokes an ineffable emotion.'],
            ['fr' => 'Nostalgie', 'ipa' => '/nɔs.tal.ʒi/', 'typeLabel' => 'noun (f)', 'level' => 'B1', 'en' => 'A sentimental longing or wistful affection for the past.', 'exFr' => 'Une douce nostalgie s\'empare de lui en relisant ces vieux manuscrits.', 'exEn' => 'A gentle nostalgia sweeps over him when rereading these old manuscripts.'],
            ['fr' => 'Bienveillance', 'ipa' => '/bjɛ̃.vɛ.jɑ̃s/', 'typeLabel' => 'noun (f)', 'level' => 'B2', 'en' => 'Goodwill, kindness, and compassionate charity.', 'exFr' => 'Le sage écouta le jeune voyageur avec une infinie bienveillance.', 'exEn' => 'The sage listened to the young traveler with infinite kindness.'],
            ['fr' => 'Clairvoyance', 'ipa' => '/klɛʁ.vwa.jɑ̃s/', 'typeLabel' => 'noun (f)', 'level' => 'B2', 'en' => 'Keen discernment and acute intellectual perception.', 'exFr' => 'Son analyse politique fait preuve d\'une remarquable clairvoyance.', 'exEn' => 'His political analysis demonstrates remarkable insight.'],
            ['fr' => 'Sublime', 'ipa' => '/sy.blim/', 'typeLabel' => 'adjective', 'level' => 'B2', 'en' => 'Of such excellence or beauty as to inspire awe.', 'exFr' => 'Cette symphonie classique atteint des sommets sublimes.', 'exEn' => 'This classical symphony reaches sublime heights.'],
        ];
    }

    private function getSentencesData(): array
    {
        return [
            ['id' => 1, 'cat' => 'Greetings', 'fr' => 'C\'est un honneur absolu de faire votre connaissance.', 'ipa' => '/sɛt œ̃n ɔ.nœʁ ab.sɔ.ly də fɛʁ vɔtʁ kɔ.nɛ.sɑ̃s/', 'en' => 'It is an absolute honor to make your acquaintance.'],
            ['id' => 2, 'cat' => 'Greetings', 'fr' => 'Je vous en prie, donnez-vous la peine d\'entrer.', 'ipa' => '/ʒə vu zɑ̃ pʁi dɔ.ne vu la pɛn dɑ̃.tʁe/', 'en' => 'Please, pray come in and make yourself comfortable.'],
            ['id' => 3, 'cat' => 'Daily Life', 'fr' => 'Je prendrai un café serré et un croissant pur beurre.', 'ipa' => '/ʒə vudʁɛ œ̃ ka.fe sɛ.ʁe e œ̃ kʁwa.sɑ̃ pyʁ bœʁ/', 'en' => 'I would like an espresso and an all-butter croissant.'],
            ['id' => 4, 'cat' => 'Daily Life', 'fr' => 'L\'atmosphère de ce café parisien est merveilleusement paisible.', 'ipa' => '/lat.mɔs.fɛʁ də sə ka.fe pa.ʁi.zjɛ̃ ɛ mɛʁ.vɛ.jøz.mɑ̃ pɛ.zibl/', 'en' => 'The atmosphere of this Parisian café is wonderfully peaceful.'],
            ['id' => 5, 'cat' => 'Travel', 'fr' => 'Quel est le quai de départ pour le train à grande vitesse ?', 'ipa' => '/kɛl ɛ lə kɛ də de.paʁ puʁ lə tʁɛ̃ a ɡʁɑ̃d vi.tɛs/', 'en' => 'Which platform does the high-speed train depart from?'],
            ['id' => 6, 'cat' => 'Intellect', 'fr' => 'Le véritable voyage de découverte ne consiste pas à chercher de nouveaux paysages.', 'ipa' => '/lə ve.ʁi.tabl vwa.jaʒ də de.ku.vɛʁt nə kɔ̃.sist pa a ʃɛʁ.ʃe də nu.vo pe.i.zaʒ/', 'en' => 'The real voyage of discovery consists not in seeking new landscapes, but in having new eyes.'],
        ];
    }
}
