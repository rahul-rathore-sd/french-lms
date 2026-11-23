<?php

namespace App\Support;

use App\Models\Learner;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

/**
 * One learner's day-by-day plan: which day it is, which CEFR/NCLC stage that day belongs to, and how far
 * along the ~470 hours to NCLC 7 the plan says they should be (config/tcf.php holds the stage hours).
 * Each learner has their own start date, minutes per day and "credit" (hours already mastered when starting,
 * e.g. 70 for someone who starts at A2). Used by the pages and by the daily automation.
 */
class Plan
{
    public function __construct(
        public CarbonImmutable $start,
        public int $dailyMinutes,
        public int $wishMonths,
        public float $credit = 0,
    ) {}

    /** The plan from config/tcf.php (used before anyone has registered). */
    public static function default(): self
    {
        return new self(CarbonImmutable::parse(config('tcf.start_date'))->startOfDay(), config('tcf.daily_minutes'), config('tcf.wish_months'), 0);
    }

    public static function for(?Learner $learner): self
    {
        if (! $learner) {
            return self::default();
        }

        return new self(CarbonImmutable::parse($learner->start_date)->startOfDay(), $learner->daily_minutes, $learner->wish_months, (float) $learner->credit_hours);
    }

    public static function today(): CarbonImmutable
    {
        return CarbonImmutable::now(config('app.timezone'))->startOfDay();
    }

    public static function totalHours(): int
    {
        return array_sum(array_column(config('tcf.stages'), 'hours'));
    }

    public function hoursPerDay(): float
    {
        return max(0.25, $this->dailyMinutes / 60);
    }

    /** 1-based day number (day 1 = start date). Before the start date this is 1. */
    public function dayNumber(?CarbonInterface $date = null): int
    {
        $date = ($date ?? self::today())->startOfDay();

        return max(1, (int) $this->start->diffInDays($date, false) + 1);
    }

    /** Hours counted as mastered at the start of a plan day (credit + planned study so far), capped at the total. */
    public function hoursDoneAt(int $day): float
    {
        return min((float) self::totalHours(), $this->credit + ($day - 1) * $this->hoursPerDay());
    }

    /** Day number on which the plan reaches NCLC 7 if the learner studies every single day. */
    public function targetDay(): int
    {
        return max(1, (int) ceil(max(0, self::totalHours() - $this->credit) / $this->hoursPerDay()));
    }

    /** Stage (level, nclc, hours range) in force on a given plan day. */
    public function stageForDay(int $day): array
    {
        $done = $this->hoursDoneAt($day);
        $stages = config('tcf.stages');
        $from = 0;
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

    /** Everything the pages and the automation need about a date (default: today). */
    public function summary(?CarbonInterface $date = null): array
    {
        $date = ($date ?? self::today())->startOfDay();
        $day = $this->dayNumber($date);
        $done = round($this->hoursDoneAt($day), 1);
        $targetDay = $this->targetDay();
        $checkpointDay = (int) round($this->wishMonths * 30);

        return [
            'date' => $date->toDateString(),
            'day_number' => $day,
            'week_number' => (int) ceil($day / 7),
            'daily_minutes' => $this->dailyMinutes,
            'credit_hours' => $this->credit,
            'hours_planned_done' => $done,
            'hours_total_to_target' => self::totalHours(),
            'progress_percent' => (int) round($done / self::totalHours() * 100),
            'target' => config('tcf.target'),
            'stage' => $this->stageForDay($day),
            // everyone gets the same kind on the same date: odd day of the year = speaking, even = writing
            'writing_or_speaking' => $date->dayOfYear % 2 === 1 ? 'speaking' : 'writing',
            'quota' => config('tcf.daily_quota'),
            'target_day' => $targetDay,
            'target_ready_date' => $this->start->addDays($targetDay - 1)->toDateString(),
            'wish_checkpoint' => [
                'months' => $this->wishMonths,
                'day' => $checkpointDay,
                'date' => $this->start->addDays($checkpointDay - 1)->toDateString(),
                'level_then' => $this->stageForDay($checkpointDay)['level'],
                'hours_then' => round(($checkpointDay - 1) * $this->hoursPerDay(), 1),
            ],
        ];
    }
}
