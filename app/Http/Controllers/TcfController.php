<?php

namespace App\Http\Controllers;

use App\Support\Plan;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/** TCF Canada hub: the exam format, the NCLC 7 target numbers and this learner's realistic timeline. */
class TcfController extends Controller
{
    public function __invoke(Request $request): View
    {
        $p = Plan::for($request->attributes->get('learner'));
        $plan = $p->summary();
        $remaining = max(1, Plan::totalHours() - $p->credit);
        $scenarios = collect([1, 1.5, 2, 3])->map(fn ($h) => [
            'hours' => $h, 'days' => (int) ceil($remaining / $h), 'months' => round(ceil($remaining / $h) / 30, 1),
        ]);

        return view('tcf', [
            'tcf' => collect($this->data('exam-guides'))->firstWhere('id', 'tcf-canada'),
            'nclc' => $this->data('nclc'),
            'roadmap' => $this->data('roadmap'),
            'plan' => $plan,
            'scenarios' => $scenarios,
            'total' => (int) $remaining,
        ]);
    }

    private function data(string $name): array
    {
        return json_decode(file_get_contents(resource_path("data/$name.json")), true, 512, JSON_THROW_ON_ERROR);
    }
}
