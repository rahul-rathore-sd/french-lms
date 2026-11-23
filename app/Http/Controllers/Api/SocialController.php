<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SocialPost;
use App\Support\Api;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SocialController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        Api::requireActor($request);
        $q = SocialPost::query();
        if ($st = Api::inList($request->query('status'), Api::SOCIAL_STATUS)) {
            $q->where('status', $st);
        }
        $blogId = (string) $request->query('blog_id', '');
        if ($blogId !== '' && ctype_digit($blogId)) {
            $q->where('blog_id', (int) $blogId);
        }
        $rows = $q->orderByDesc('id')->limit(300)->get()->toArray();

        return Api::json(['success' => true, 'total' => count($rows), 'data' => array_map([Api::class, 'ints'], $rows)]);
    }

    public function store(Request $request): JsonResponse
    {
        $who = Api::requireActor($request);
        $b = Api::body($request);
        $content = Api::s($b['content'] ?? '', 4000);
        $platform = Api::inList($b['platform'] ?? null, Api::PLATFORMS);
        if ($content === '' || ! $platform) {
            Api::fail('platform ('.implode(', ', Api::PLATFORMS).') and content are required.', 422);
        }
        $status = Api::inList($b['status'] ?? null, Api::SOCIAL_STATUS, 'draft');
        $now = Api::nowIso();
        $post = SocialPost::create([
            'platform' => $platform, 'content' => $content, 'link' => Api::s($b['link'] ?? '', 500),
            'media_url' => Api::s($b['media_url'] ?? '', 500), 'status' => $status,
            'scheduled_at' => Api::s($b['scheduled_at'] ?? '', 25) ?: null,
            'blog_id' => isset($b['blog_id']) && ctype_digit((string) $b['blog_id']) ? (int) $b['blog_id'] : null,
            'created_by' => $who, 'created_at' => $now, 'updated_at' => $now,
        ]);
        Api::log($who, 'create', 'social', $post->id, "$platform [$status]");

        return Api::json(['success' => true, 'data' => Api::ints($post->fresh()->toArray())], 201);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        Api::requireActor($request);

        return Api::json(['success' => true, 'data' => Api::ints($this->find($id)->toArray())]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $who = Api::requireActor($request);
        $post = $this->find($id);
        $b = Api::body($request);

        $sets = [];
        if (isset($b['platform']) && ($v = Api::inList($b['platform'], Api::PLATFORMS))) {
            $sets['platform'] = $v;
        }
        if (isset($b['status']) && ($v = Api::inList($b['status'], Api::SOCIAL_STATUS))) {
            $sets['status'] = $v;
            if ($v === 'posted') {
                $sets['posted_at'] = Api::nowIso();
            }
        }
        foreach (['content' => 4000, 'link' => 500, 'media_url' => 500, 'post_url' => 500] as $f => $max) {
            if (array_key_exists($f, $b)) {
                $sets[$f] = Api::s($b[$f], $max);
            }
        }
        if (array_key_exists('scheduled_at', $b)) {
            $sets['scheduled_at'] = Api::s($b['scheduled_at'], 25) ?: null;
        }
        if (! $sets) {
            Api::fail('Nothing to update.', 422);
        }
        $sets['updated_at'] = Api::nowIso();
        $post->update($sets);
        Api::log($who, 'update', 'social', $post->id, $post->platform);

        return Api::json(['success' => true, 'data' => Api::ints($post->fresh()->toArray())]);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $who = Api::requireActor($request);
        $post = $this->find($id);
        $post->delete();
        Api::log($who, 'delete', 'social', $post->id, $post->platform);

        return Api::json(['success' => true]);
    }

    private function find(string $id): SocialPost
    {
        return SocialPost::find(Api::mustInt($id)) ?? Api::fail('Post not found.', 404);
    }
}
