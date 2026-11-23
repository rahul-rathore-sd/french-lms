<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

/** Quick self-assessment: can-do checklists for A1 and A2 plus a short mixed quiz. */
class LevelCheckController extends Controller
{
    public function __invoke(): View
    {
        $q = fn (string $q, array $o, int $c, string $e) => ['q' => $q, 'options' => $o, 'correct' => $c, 'explain' => $e];

        return view('level-check', [
            'a1' => [
                'I can introduce myself (name, age, country, job) in French.',
                'I can count to 100 and say the time and the days.',
                'I can ask for something with "Je voudrais…" (a coffee, a ticket).',
                'I understand simple questions like "Comment vous appelez-vous ?" when spoken slowly.',
                'I know about 500 to 700 words (family, food, places, numbers).',
                'I can fill in a simple form with my details.',
            ],
            'a2' => [
                'I can tell what I did yesterday in the passé composé.',
                'I can shop, order in a restaurant or ask for directions.',
                'I can describe my daily routine and my family in 5 to 6 sentences.',
                'I understand short notices, posters and simple messages.',
                'I can write a short message to a friend or a simple email.',
                'I know about 1,000 to 1,500 words.',
            ],
            'quiz' => [
                $q('Complétez : « Je ___ Rahul. »', ['suis', 'm\'appelle', 'ai', 'habite'], 1, '« Je m\'appelle… » is how we say our name.'),
                $q('Quelle est la bonne phrase ?', ['Il a vingt ans.', 'Il est vingt ans.', 'Il fait vingt ans.'], 0, 'Age uses avoir: il a vingt ans.'),
                $q('« Où habitez-vous ? » means:', ['What is your job?', 'Where do you live?', 'How are you?'], 1, '« Où » = where, « habiter » = to live.'),
                $q('Choisissez : « Je voudrais ___ café, s\'il vous plaît. »', ['un', 'une', 'des'], 0, '« café » is masculine: un café.'),
                $q('« Hier, j\'___ au marché. »', ['vais', 'suis allé', 'irai'], 1, 'Past (passé composé) of aller uses être: je suis allé.'),
                $q('Quel mot est féminin ?', ['le livre', 'la maison', 'le café'], 1, '« la maison » is feminine.'),
                $q('« Nous ___ français depuis un an. »', ['étudions', 'étudie', 'étudiez'], 0, 'nous + -ons: nous étudions.'),
                $q('« Il faut que tu ___ ton travail. » (subjonctif)', ['finis', 'finisses', 'finiras'], 1, 'After « il faut que » we use the subjunctive: que tu finisses.'),
            ],
        ]);
    }
}
