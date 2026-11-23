<?php

namespace Tests\Unit;

use App\Support\Plan;
use Carbon\CarbonImmutable;
use Tests\TestCase;

class PlanTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['tcf.start_date' => '2026-01-01', 'tcf.daily_minutes' => 120, 'tcf.wish_months' => 3]);
    }

    public function test_total_hours_and_target_day(): void
    {
        $this->assertSame(470, Plan::totalHours());
        $this->assertSame(235, Plan::targetDay()); // 470 h at 2 h a day
    }

    public function test_stage_changes_at_the_right_days(): void
    {
        $this->assertSame('A1', Plan::stageForDay(1)['level']);
        $this->assertSame('A1', Plan::stageForDay(35)['level']);   // 68 h done
        $this->assertSame('A2', Plan::stageForDay(36)['level']);   // 70 h done
        $this->assertSame('B1', Plan::stageForDay(81)['level']);   // 160 h done
        $this->assertSame('B1', Plan::stageForDay(148)['level']);  // 294 h done
        $this->assertSame('B2', Plan::stageForDay(149)['level']);  // 296 h done: B2 starts
        $this->assertSame('B2', Plan::stageForDay(500)['level']);  // stays on the last stage
    }

    public function test_three_month_wish_is_honestly_not_nclc7(): void
    {
        $s = Plan::summary(CarbonImmutable::parse('2026-01-01'));
        $this->assertSame(1, $s['day_number']);
        $this->assertSame('B1', $s['wish_checkpoint']['level_then']);
        $this->assertLessThan($s['hours_total_to_target'], $s['wish_checkpoint']['hours_then']);
        $this->assertSame('2026-08-23', $s['target_ready_date']);
    }

    public function test_day_number_follows_the_date(): void
    {
        $this->assertSame(1, Plan::dayNumber(CarbonImmutable::parse('2025-12-20'))); // before start
        $this->assertSame(10, Plan::dayNumber(CarbonImmutable::parse('2026-01-10')));
        $this->assertSame('speaking', Plan::summary(CarbonImmutable::parse('2026-01-01'))['writing_or_speaking']);
        $this->assertSame('writing', Plan::summary(CarbonImmutable::parse('2026-01-02'))['writing_or_speaking']);
    }
}
