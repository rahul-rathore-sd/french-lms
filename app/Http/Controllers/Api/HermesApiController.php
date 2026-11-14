<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomWord;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HermesApiController extends Controller
{
    /**
     * Verify Hermes API Key
     */
    protected function authenticateHermes(Request $request): bool
    {
        $expectedKey = env('HERMES_API_KEY');
        if (empty($expectedKey)) {
            return false;
        }

        $providedKey = $request->header('X-Hermes-API-Key') 
                    ?? $request->bearerToken() 
                    ?? $request->query('api_key');

        return !empty($providedKey) && hash_equals($expectedKey, $providedKey);
    }

    /**
     * System & Sync Status for Hermes
     * GET /api/v1/status
     */
    public function status(Request $request): JsonResponse
    {
        if (!$this->authenticateHermes($request)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized: Invalid or missing Hermes API Key. Provide via X-Hermes-API-Key header or Bearer token.',
            ], 401);
        }

        $totalWords = CustomWord::count();
        $latestWord = CustomWord::latest()->first();

        return response()->json([
            'status' => 'success',
            'platform' => 'Académie de Français LMS',
            'version' => '2.0.0-Bilingual',
            'vocabulary_total' => $totalWords,
            'latest_entry' => $latestWord ? [
                'word' => $latestWord->word,
                'created_at' => $latestWord->created_at->toIso8601String(),
                'source' => $latestWord->source,
            ] : null,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * List Vocabulary entries
     * GET /api/v1/vocabulary
     */
    public function index(Request $request): JsonResponse
    {
        if (!$this->authenticateHermes($request)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized: Invalid or missing Hermes API Key.',
            ], 401);
        }

        $query = CustomWord::query();

        if ($request->filled('level')) {
            $query->where('level', strtoupper($request->level));
        }

        if ($request->filled('category')) {
            $query->where('category', 'like', "%{$request->category}%");
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('word', 'like', "%{$search}%")
                  ->orWhere('meaning', 'like', "%{$search}%")
                  ->orWhere('meaning_hi', 'like', "%{$search}%");
            });
        }

        $words = $query->latest()->get()->map(function($w) {
            $arr = $w->toArray();
            $arr['meaning_en'] = $w->meaning;
            return $arr;
        });

        return response()->json([
            'status' => 'success',
            'count' => $words->count(),
            'data' => $words,
        ]);
    }

    /**
     * Ingest Single or Batch Vocabulary Words from Hermes Daily Daemon
     * POST /api/v1/vocabulary
     */
    public function ingest(Request $request): JsonResponse
    {
        if (!$this->authenticateHermes($request)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized: Invalid or missing Hermes API Key. Set header: X-Hermes-API-Key.',
            ], 401);
        }

        // Get system user ID to associate words with
        $systemUser = User::where('role', 'admin')->first() ?? User::first();
        $userId = $systemUser ? $systemUser->id : 1;

        // Determine if batch (array in 'words' key or array root) or single word
        $input = $request->all();
        $items = [];

        if (isset($input['words']) && is_array($input['words'])) {
            $items = $input['words'];
        } elseif (isset($input[0]) && is_array($input[0])) {
            $items = $input;
        } else {
            $items = [$input];
        }

        $insertedCount = 0;
        $updatedCount = 0;
        $processedWords = [];
        $errors = [];

        foreach ($items as $idx => $item) {
            // Support intuitive aliases from Hermes
            if (!isset($item['meaning']) && isset($item['meaning_en'])) {
                $item['meaning'] = $item['meaning_en'];
            }
            if (!isset($item['ipa']) && isset($item['pronunciation'])) {
                $item['ipa'] = $item['pronunciation'];
            }

            $validator = Validator::make($item, [
                'word' => 'required|string|max:100',
                'meaning' => 'required|string|max:255',
                'meaning_hi' => 'nullable|string|max:255',
                'ipa' => 'nullable|string|max:100',
                'level' => 'nullable|in:A1,A2,B1,B2,C1,C2',
                'category' => 'nullable|string|max:100',
                'example_fr' => 'nullable|string',
                'example_en' => 'nullable|string',
                'example_hi' => 'nullable|string',
                'use_case' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                $errors[] = [
                    'index' => $idx,
                    'word' => $item['word'] ?? 'Unknown',
                    'errors' => $validator->errors()->all()
                ];
                continue;
            }

            $wordClean = trim($item['word']);
            $existing = CustomWord::whereRaw('LOWER(word) = ?', [strtolower($wordClean)])->first();

            $data = [
                'user_id' => $userId,
                'word' => $wordClean,
                'ipa' => !empty($item['ipa']) ? trim($item['ipa']) : ('/' . strtolower($wordClean) . '/'),
                'meaning' => trim($item['meaning']),
                'meaning_hi' => !empty($item['meaning_hi']) ? trim($item['meaning_hi']) : null,
                'level' => !empty($item['level']) ? strtoupper($item['level']) : 'B1',
                'category' => !empty($item['category']) ? trim($item['category']) : 'Hermes Daily Feed',
                'example_fr' => !empty($item['example_fr']) ? trim($item['example_fr']) : "{$wordClean} est une expression classique.",
                'example_en' => !empty($item['example_en']) ? trim($item['example_en']) : "{$wordClean} is a classical expression.",
                'example_hi' => !empty($item['example_hi']) ? trim($item['example_hi']) : null,
                'use_case' => !empty($item['use_case']) ? trim($item['use_case']) : null,
                'source' => $item['source'] ?? 'Hermes Daily AI',
            ];

            if ($existing) {
                $existing->update($data);
                $updatedCount++;
                $processedWords[] = ['word' => $wordClean, 'action' => 'updated'];
            } else {
                CustomWord::create($data);
                $insertedCount++;
                $processedWords[] = ['word' => $wordClean, 'action' => 'inserted'];
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => "Hermes ingestion complete. {$insertedCount} words inserted, {$updatedCount} words updated.",
            'inserted' => $insertedCount,
            'updated' => $updatedCount,
            'total_processed' => count($processedWords),
            'words' => $processedWords,
            'errors' => $errors,
        ], $insertedCount > 0 ? 201 : 200);
    }
}
