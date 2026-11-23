<?php

namespace App\Http\Controllers;

use App\Models\Learner;
use App\Support\LearnerAuth;
use App\Support\Plan;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/** Settings of the logged-in learner: study minutes, restart the plan at another level, change the PIN. */
class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        /** @var Learner $learner */
        $learner = $request->attributes->get('learner');

        return view('profile', ['plan' => Plan::for($learner)->summary(), 'others' => Learner::where('id', '!=', $learner->id)->count()]);
    }

    public function settings(Request $request): JsonResponse
    {
        $learner = $request->attributes->get('learner');
        $learner->update([
            'daily_minutes' => max(30, min(300, (int) $request->input('minutes', $learner->daily_minutes))),
            'wish_months' => max(1, min(24, (int) $request->input('wish_months', $learner->wish_months))),
        ]);

        return response()->json(['ok' => true, 'message' => 'Saved.']);
    }

    /** "Start my plan again from today at level X" (e.g. after the level check shows you are further ahead). */
    public function restart(Request $request): JsonResponse
    {
        $learner = $request->attributes->get('learner');
        $level = (string) $request->input('level', '');
        if (! array_key_exists($level, Learner::CREDIT)) {
            return response()->json(['ok' => false, 'error' => 'Choose a level.'], 422);
        }
        $learner->update(['start_date' => Plan::today()->toDateString(), 'credit_hours' => Learner::CREDIT[$level]]);

        return response()->json(['ok' => true, 'message' => "Plan restarted today at $level.", 'redirect' => '/']);
    }

    public function pin(Request $request): JsonResponse
    {
        $learner = $request->attributes->get('learner');
        if (! Hash::check((string) $request->input('current', ''), $learner->pin_hash)) {
            return response()->json(['ok' => false, 'error' => 'Current PIN is wrong.'], 422);
        }
        $new = (string) $request->input('new', '');
        if (! preg_match('/^\d{4,8}$/', $new)) {
            return response()->json(['ok' => false, 'error' => 'New PIN: 4 to 8 digits.'], 422);
        }
        $learner->update(['pin_hash' => Hash::make($new)]);
        // every other device must log in again with the new PIN
        $learner->tokens()->where('token_hash', '!=', LearnerAuth::currentHash($request))->delete();

        return response()->json(['ok' => true, 'message' => 'PIN changed. Other devices were logged out.']);
    }
}
