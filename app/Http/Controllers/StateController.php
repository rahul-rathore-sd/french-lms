<?php

namespace App\Http\Controllers;

use App\Models\LearnerState;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** Progress that follows the learner across devices (progress, card schedule). PUT /me/state/{key}. */
class StateController extends Controller
{
    public const KEYS = ['progress', 'srs', 'srs.new'];

    public function put(Request $request, string $key): JsonResponse
    {
        abort_unless(in_array($key, self::KEYS, true), 404);

        $raw = $request->getContent();
        if (strlen($raw) > 900_000) {
            return response()->json(['ok' => false, 'error' => 'Too large.'], 413);
        }
        // decoded as objects (not arrays) so an empty {} stays {} instead of turning into []
        $data = json_decode($raw);
        if (! is_object($data) || ! property_exists($data, 'value') || ! is_object($data->value)) {
            return response()->json(['ok' => false, 'error' => 'Expected {"value": {...}}.'], 422);
        }

        $learner = $request->attributes->get('learner');
        LearnerState::updateOrCreate(
            ['learner_id' => $learner->id, 'key' => $key],
            ['value' => json_encode($data->value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 'updated_at' => now()],
        );

        return response()->json(['ok' => true]);
    }
}
