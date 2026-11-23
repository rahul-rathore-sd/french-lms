<?php

namespace App\Http\Controllers;

use App\Support\Plan;
use Illuminate\Contracts\View\View;

/** TCF Canada hub: the exam format, the NCLC 7 target numbers and the realistic hours roadmap. */
class TcfController extends Controller
{
    public function __invoke(): View
    {
        $guides = $this->data('exam-guides');
        $total = Plan::totalHours();
        $scenarios = collect([1, 1.5, 2, 3])->map(fn ($h) => [
            'hours' => $h, 'days' => (int) ceil($total / $h), 'months' => round(ceil($total / $h) / 30, 1),
        ]);

        return view('tcf', [
            'tcf' => collect($guides)->firstWhere('id', 'tcf-canada'),
            'nclc' => $this->data('nclc'),
            'roadmap' => $this->data('roadmap'),
            'plan' => Plan::summary(),
            'scenarios' => $scenarios,
            'total' => $total,
        ]);
    }

    private function data(string $name): array
    {
        return json_decode(file_get_contents(resource_path("data/$name.json")), true, 512, JSON_THROW_ON_ERROR);
    }
}
