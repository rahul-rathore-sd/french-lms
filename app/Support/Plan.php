<?php

namespace App\Support;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

/**
 * The learner's day-by-day plan (config/tcf.php): which day it is, which CEFR/NCLC stage that
 * day belongs to, and how far along the ~470 hours to NCLC 7 the plan says they should be.
 * Used by the home page and by the daily automation (GET /api/v1/plan/today).
 */
class Plan
{
    public static function start(): CarbonImmutable
    {
        return CarbonImmutable::parse(config('tcf.start_date'))->startOfDay();
    }

    public static function today(): CarbonImmutable
    {
        return CarbonImmutable::now(config('app.timezone'))->startOfDay();
    }

    /** 1-based day number (day 1 = start date). Before the start date this is 1. */
    public static function dayNumber(?CarbonInterface $date = null): int
    {
        $date = ($date ?? self::today())->startOfDay();

        return max(1, (int) self::start()->diffInDays($date, false) + 1);
    }

    public static function hoursPerDay(): float
    {
        return config('tcf.daily_minutes') / 60;
    }

    public static function totalHours(): int
    {
        return array_sum(array_column(config('tcf.stages'), 'hours'));
    }

    /** Day number on which the plan reaches NCLC 7 if the learner studies every single day. */
    public static function targetDay(): int
    {
        return (int) ceil(self::totalHours() / self::hoursPerDay());
    }

    /** Stage (level, nclc, hours range) in force on a given plan day. */
    public static function stageForDay(int $day): array
    {
        $done = ($day - 1) * self::hoursPerDay();
        $from = 0;
        $stages = config('tcf.stages');
        foreach ($stages as $i => $stage) {
            $to = $from + $stage['hours'];
            if ($done < $to) {
                return $stage + ['index' => $i, 'from_hour' => $from, 'to_hour' => $to,
                    'hours_into_stage' => round($done - $from, 1), 'hours_left_in_stage' => round($to - $done, 1)];
            }
            $from = $to;
        }
        $last = array_key_last($stages);

        return $stages[$last] + ['index' => $last, 'from_hour' => $from - $stages[$last]['hours'], 'to_hour' => $from,
            'hours_into_stage' => $stages[$last]['hours'], 'hours_left_in_stage' => 0];
    }

    /** Everything the home page and the automation need about "today". */
    public static function summary(?CarbonInterface $date = null): array
    {
        $date = ($date ?? self::today())->startOfDay();
        $day = self::dayNumber($date);
        $hoursDone = round(min(($day - 1) * self::hoursPerDay(), self::totalHours()), 1);
        $targetDay = self::targetDay();
        $checkpointDay = (int) round(config('tcf.wish_months') * 30);

        return [
            'date' => $date->toDateString(),
            'day_number' => $day,
            'week_number' => (int) ceil($day / 7),
            'daily_minutes' => config('tcf.daily_minutes'),
            'hours_planned_done' => $hoursDone,
            'hours_total_to_target' => self::totalHours(),
            'progress_percent' => (int) round($hoursDone / self::totalHours() * 100),
            'target' => config('tcf.target'),
            'stage' => self::stageForDay($day),
            // alternate: even days a writing task, odd days a speaking task
            'writing_or_speaking' => $day % 2 === 0 ? 'writing' : 'speaking',
            'quota' => config('tcf.daily_quota'),
            'target_day' => $targetDay,
            'target_ready_date' => self::start()->addDays($targetDay - 1)->toDateString(),
            'wish_checkpoint' => [
                'months' => config('tcf.wish_months'),
                'day' => $checkpointDay,
                'date' => self::start()->addDays($checkpointDay - 1)->toDateString(),
                'level_then' => self::stageForDay($checkpointDay)['level'],
                'hours_then' => round(($checkpointDay - 1) * self::hoursPerDay(), 1),
            ],
        ];
    }
}
