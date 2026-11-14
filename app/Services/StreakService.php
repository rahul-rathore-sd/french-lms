<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

class StreakService
{
    /**
     * Check if a day was missed since the last task completion.
     * If more than 1 calendar day has elapsed without any completed task, reset streak to 0.
     */
    public static function checkAndResetStreak(User $user): void
    {
        if (!$user->last_task_completed_at) {
            if ($user->current_streak > 0) {
                $user->current_streak = 0;
                $user->save();
            }
            return;
        }

        $today = Carbon::today();
        $lastCompletedDate = Carbon::parse($user->last_task_completed_at)->startOfDay();
        $diffDays = $lastCompletedDate->diffInDays($today);

        // If last completed task was before yesterday (2 or more days ago), streak is broken!
        if ($diffDays > 1) {
            if ($user->current_streak !== 0) {
                $user->current_streak = 0;
                $user->save();
            }
        }
    }

    /**
     * Record a completed task (lesson, quiz, or vocabulary word).
     * Updates streak count according to daily activity rules.
     */
    public static function recordTaskCompletion(User $user): array
    {
        $today = Carbon::today();
        $todayStr = $today->toDateString();
        
        $history = [];
        if (!empty($user->streak_history)) {
            $decoded = json_decode($user->streak_history, true);
            if (is_array($decoded)) {
                $history = $decoded;
            }
        }

        if (!in_array($todayStr, $history)) {
            $history[] = $todayStr;
        }

        $lastCompletedDate = $user->last_task_completed_at 
            ? Carbon::parse($user->last_task_completed_at)->startOfDay() 
            : null;

        $streakUpdated = false;

        if ($lastCompletedDate === null) {
            // First ever completed task
            $user->current_streak = 1;
            $streakUpdated = true;
        } elseif ($lastCompletedDate->equalTo($today)) {
            // Already completed a task today; keep current streak without duplicate increment
            if ($user->current_streak === 0) {
                $user->current_streak = 1;
                $streakUpdated = true;
            }
        } elseif ($lastCompletedDate->equalTo($today->copy()->subDay())) {
            // Consecutive day! Increment streak!
            $user->current_streak += 1;
            $streakUpdated = true;
        } else {
            // Missed one or more days; streak restarts at 1
            $user->current_streak = 1;
            $streakUpdated = true;
        }

        $user->last_task_completed_at = Carbon::now();
        $user->last_active_at = Carbon::now();
        $user->streak_history = json_encode(array_values(array_unique($history)));
        $user->save();

        return [
            'current_streak' => $user->current_streak,
            'streak_updated' => $streakUpdated,
            'activity_history' => self::getWeeklyActivity($user),
        ];
    }

    /**
     * Generate the 7-day weekly activity status for visual indicators.
     */
    public static function getWeeklyActivity(User $user): array
    {
        $today = Carbon::today();
        $history = [];
        if (!empty($user->streak_history)) {
            $decoded = json_decode($user->streak_history, true);
            if (is_array($decoded)) {
                $history = $decoded;
            }
        }

        // Calculate Monday to Sunday of current week
        $startOfWeek = $today->copy()->startOfWeek(); // Monday
        $days = [];

        for ($i = 0; $i < 7; $i++) {
            $day = $startOfWeek->copy()->addDays($i);
            $dayStr = $day->toDateString();
            $isToday = $day->equalTo($today);
            $isPast = $day->lessThan($today);
            $isFuture = $day->greaterThan($today);
            $isCompleted = in_array($dayStr, $history);

            $status = 'upcoming';
            if ($isCompleted) {
                $status = 'completed';
            } elseif ($isToday) {
                $status = 'today_pending';
            } elseif ($isPast) {
                $status = 'missed';
            }

            $days[] = [
                'date' => $dayStr,
                'day_name' => $day->format('D'), // Mon, Tue...
                'day_letter' => substr($day->format('D'), 0, 1),
                'day_number' => $day->format('d'),
                'is_today' => $isToday,
                'is_completed' => $isCompleted,
                'is_future' => $isFuture,
                'status' => $status,
            ];
        }

        return [
            'days' => $days,
            'current_streak' => $user->current_streak,
            'completed_today' => in_array($today->toDateString(), $history),
        ];
    }
}
