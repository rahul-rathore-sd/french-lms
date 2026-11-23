<?php

namespace App\Http\Controllers;

use App\Support\Tts;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

/**
 * POST /tts {text, voice?, rate?} -> {ok, parts:[url, ...]}
 * Returns the audio files (a dialogue is several, one per line) for the text. Files are generated once,
 * cached in public/audio and then served as static files.
 */
class TtsController extends Controller
{
    public function __invoke(Request $request, Tts $tts): JsonResponse
    {
        if (! config('tts.enabled')) {
            return $this->err('Server voices are turned off.', 503);
        }
        $learner = $request->attributes->get('learner');

        $text = Tts::normalize((string) $request->input('text', ''));
        $voice = (string) $request->input('voice', $learner->voice);
        $rate = (string) $request->input('rate', '+0%');
        if ($text === '' || mb_strlen($text) > config('tts.max_chars') || ! preg_match('/\pL/u', $text)) {
            return $this->err('Text must be 1 to '.config('tts.max_chars').' characters.', 422);
        }
        if (! Tts::isChoice($voice) || $voice === 'browser') {
            return $this->err('Unknown voice.', 422);
        }
        if (! in_array($rate, config('tts.rates'), true)) {
            return $this->err('Unknown speed.', 422);
        }

        // generous per-person limit (flashcards ask for the next few words ahead of time)
        $limitKey = 'tts:'.$learner->id;
        if (RateLimiter::tooManyAttempts($limitKey, config('tts.per_minute') * 2)) {
            return $this->err('Too many audio requests. Wait a moment.', 429);
        }
        RateLimiter::hit($limitKey, 60);

        // files that already exist are served without waiting; only generation is serialised
        if ($tts->pending($text, $voice, $rate) === 0) {
            return response()->json(['ok' => true, 'parts' => $tts->parts($text, $voice, $rate)]);
        }

        $lock = Cache::lock('tts-render', 90);
        try {
            $lock->block(25);
        } catch (LockTimeoutException) {
            return $this->err('The voice service is busy. Try again.', 503);
        }
        try {
            $urls = $tts->parts($text, $voice, $rate);
        } finally {
            $lock->release();
        }

        return $urls === null ? $this->err('The voice service is not available right now.', 502) : response()->json(['ok' => true, 'parts' => $urls]);
    }

    private function err(string $message, int $status): JsonResponse
    {
        return response()->json(['ok' => false, 'error' => $message], $status);
    }
}
