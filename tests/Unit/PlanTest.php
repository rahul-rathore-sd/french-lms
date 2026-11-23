<?php

namespace Tests\Unit;

use App\Support\Plan;
use Carbon\CarbonImmutable;
use Tests\TestCase;

class PlanTest extends TestCase
{
    private function plan(float $credit = 0, int $minutes = 120): Plan
    {
        return new Plan(CarbonImmutable::parse('2026-01-01'), $minutes, 3, $credit);
    }

    public function test_total_hours_and_target_day(): void
    {
        $this->assertSame(470, Plan::totalHours());
        $this->assertSame(235, $this->plan()->targetDay());           // 470 h at 2 h a day
        $this->assertSame(188, $this->plan(0, 150)->targetDay());     // 470 h at 2.5 h a day
    }

    public function test_stage_changes_at_the_right_days(): void
    {
        $p = $this->plan();
        $this->assertSame('A1', $p->stageForDay(1)['level']);
        $this->assertSame('A1', $p->stageForDay(35)['level']);   // 68 h done
        $this->assertSame('A2', $p->stageForDay(36)['level']);   // 70 h done
        $this->assertSame('B1', $p->stageForDay(81)['level']);   // 160 h done
        $this->assertSame('B1', $p->stageForDay(148)['level']);  // 294 h done
        $this->assertSame('B2', $p->stageForDay(149)['level']);  // 296 h done: B2 starts
        $this->assertSame('B2', $p->stageForDay(500)['level']);  // stays on the last stage
    }

    public function test_starting_credit_skips_the_early_stages(): void
    {
        $a2 = $this->plan(70);                                   // starts at A2
        $this->assertSame('A2', $a2->stageForDay(1)['level']);
        $this->assertSame(200, $a2->targetDay());                // 400 h left at 2 h a day
        $this->assertSame('B1', $this->plan(160)->stageForDay(1)['level']);
        $this->assertSame('B2', $this->plan(295)->stageForDay(1)['level']);
        $this->assertSame(15, $a2->summary(CarbonImmutable::parse('2026-01-01'))['progress_percent']);
    }

    public function test_three_month_wish_is_honestly_not_nclc7(): void
    {
        $s = $this->plan()->summary(CarbonImmutable::parse('2026-01-01'));
        $this->assertSame(1, $s['day_number']);
        $this->assertSame('B1', $s['wish_checkpoint']['level_then']);
        $this->assertLessThan($s['hours_total_to_target'], $s['wish_checkpoint']['hours_then']);
        $this->assertSame('2026-08-23', $s['target_ready_date']);
    }

    public function test_day_number_and_daily_kind_follow_the_date(): void
    {
        $p = $this->plan();
        $this->assertSame(1, $p->dayNumber(CarbonImmutable::parse('2025-12-20'))); // before start
        $this->assertSame(10, $p->dayNumber(CarbonImmutable::parse('2026-01-10')));
        // day of year: 1 Jan is odd -> speaking, 2 Jan is even -> writing (same for every learner)
        $this->assertSame('speaking', $p->summary(CarbonImmutable::parse('2026-01-01'))['writing_or_speaking']);
        $this->assertSame('writing', $p->summary(CarbonImmutable::parse('2026-01-02'))['writing_or_speaking']);
        $this->assertSame($p->summary(CarbonImmutable::parse('2026-03-05'))['writing_or_speaking'],
            $this->plan(160)->summary(CarbonImmutable::parse('2026-03-05'))['writing_or_speaking']);
    }
}
