<?php

/*
 | Personal study plan for TCF Canada, target NCLC 7 (about CEFR B2).
 | Hours per stage are the mid-points of the ranges in resources/data/roadmap.json
 | (A1 60-80 h, A2 80-100 h, B1 120-150 h, B2 150-200 h  =>  about 470 h from zero).
 */
return [
    // Day 1 of the plan (YYYY-MM-DD). Set TCF_START_DATE in .env.
    'start_date' => env('TCF_START_DATE', '2026-11-23'),

    // Study minutes per day the learner can really give.
    'daily_minutes' => (int) env('TCF_DAILY_MINUTES', 120),

    // What the learner would like to reach by when (informational, shown on the home page).
    'wish_months' => (int) env('TCF_WISH_MONTHS', 3),

    'target' => 'NCLC 7',

    'stages' => [
        ['level' => 'A1', 'nclc' => 'NCLC 1-3', 'hours' => 70],
        ['level' => 'A2', 'nclc' => 'NCLC 4', 'hours' => 90],
        ['level' => 'B1', 'nclc' => 'NCLC 5-6', 'hours' => 135],
        ['level' => 'B2', 'nclc' => 'NCLC 7-8', 'hours' => 175],
    ],

    // How many new items the daily automation adds each day.
    'daily_quota' => ['vocab' => 10, 'grammar' => 1, 'listening' => 1, 'reading' => 1, 'writing_or_speaking' => 1],
];
