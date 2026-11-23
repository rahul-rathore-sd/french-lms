<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Support\Api;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public static function out(array $r, bool $withContent): array
    {
        $r = Api::ints($r);
        $r['tags'] = array_values(array_filter(array_map('trim', explode(',', (string) $r['tags']))));
        $r['url'] = Api::siteUrl().'/blog/'.$r['slug'].'/';
        if ($withContent) {
            $r['content_html'] = Api::mdToHtml((string) $r['content_md']);
        } else {
            unset($r['content_md']);
        }

        return $r;
    }

    public static function uniqueSlug(string $base, ?int $exceptId = null): string
    {
        $slug = $base;
        $n = 2;
        while (true) {
            $row = BlogPost::where('slug', $slug)->first();
            if (! $row || ($exceptId !== null && (int) $row->id === $exceptId)) {
                return $slug;
            }
            $slug = substr($base, 0, 110).'-'.$n++;
        }
    }

    /** GET /blog: public lists published posts; admin/Hermes may filter by status. */
    public function index(Request $request): JsonResponse
    {
        $isAdmin = Api::actor($request) !== null;
        $limit = max(1, min(200, (int) $request->query('limit', 20)));
        $q = BlogPost::query();

        $status = $request->query('status');
        if ($isAdmin && $status === 'all') {
            // no filter
        } elseif ($isAdmin && ($st = Api::inList($status, Api::BLOG_STATUS))) {
            $q->where('status', $st);
        } else {
            Api::wherePublic($q);
        }
        $full = $isAdmin && $request->query('full');
        $rows = $q->orderByRaw('COALESCE(publish_at, created_at) DESC')->orderByDesc('id')->limit($limit)->get()->toArray();

        return Api::json(['success' => true, 'total' => count($rows),
            'data' => array_map(fn ($r) => self::out($r, (bool) $full), $rows)]);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $isAdmin = Api::actor($request) !== null;
        $post = ctype_digit($id) ? BlogPost::find((int) $id) : BlogPost::where('slug', $id)->first();
        if (! $post || (! $isAdmin && ! Api::isPublicPost($post->toArray()))) {
            Api::fail('Post not found.', 404);
        }
        $out = self::out($post->toArray(), true);
        if ($isAdmin) {
            $out['content_md'] = $post->content_md;
        }

        return Api::json(['success' => true, 'data' => $out]);
    }

    public function store(Request $request): JsonResponse
    {
        $who = Api::requireActor($request);
        $b = Api::body($request);
        $title = Api::s($b['title'] ?? '', 250);
        $content = Api::s($b['content_md'] ?? '', 200000);
        if ($title === '' || $content === '') {
            Api::fail('title and content_md are required.', 422);
        }
        $status = Api::inList($b['status'] ?? null, Api::BLOG_STATUS, 'draft');
        $publishAt = Api::s($b['publish_at'] ?? '', 25) ?: null;
        if ($status === 'published' && ! $publishAt) {
            $publishAt = Api::nowIso();
        }
        if ($status === 'scheduled' && ! $publishAt) {
            Api::fail('publish_at is required for scheduled posts.', 422);
        }
        $tags = is_array($b['tags'] ?? null)
            ? implode(',', array_map(fn ($t) => Api::s($t, 40), $b['tags']))
            : Api::s($b['tags'] ?? '', 300);
        $explicit = isset($b['slug']) && Api::s($b['slug'], 190) !== '';
        $slug = Api::slugify($explicit ? Api::s($b['slug'], 190) : $title);

        $fields = [
            'title' => $title, 'excerpt' => Api::s($b['excerpt'] ?? '', 500), 'content_md' => $content,
            'tags' => Api::s($tags, 300), 'cover_image' => Api::s($b['cover_image'] ?? '', 500), 'status' => $status,
            'publish_at' => $publishAt, 'author' => Api::s($b['author'] ?? 'Académie de Français', 80),
            'meta_description' => Api::s($b['meta_description'] ?? '', 300), 'updated_at' => Api::nowIso(),
        ];

        // An explicit slug that already exists updates that post (idempotent for agents).
        $existing = $explicit ? BlogPost::where('slug', $slug)->first() : null;
        if ($existing) {
            $existing->update($fields);
            Api::log($who, 'update', 'blog', $existing->id, $title);
            $post = $existing->fresh();
        } else {
            $post = BlogPost::create($fields + ['slug' => self::uniqueSlug($slug), 'created_by' => $who, 'created_at' => Api::nowIso()]);
            Api::log($who, 'create', 'blog', $post->id, "$title [$status]");
            $post = $post->fresh();
        }

        return Api::json(['success' => true, 'data' => self::out($post->toArray(), true)], $existing ? 200 : 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $who = Api::requireActor($request);
        $post = BlogPost::find(Api::mustInt($id)) ?? Api::fail('Post not found.', 404);
        $b = Api::body($request);

        $sets = [];
        $map = ['title' => 250, 'excerpt' => 500, 'content_md' => 200000, 'cover_image' => 500, 'author' => 80, 'meta_description' => 300];
        foreach ($map as $f => $max) {
            if (array_key_exists($f, $b)) {
                $v = Api::s($b[$f], $max);
                if (($f === 'title' || $f === 'content_md') && $v === '') {
                    Api::fail("$f cannot be empty.", 422);
                }
                $sets[$f] = $v;
            }
        }
        if (array_key_exists('tags', $b)) {
            $sets['tags'] = is_array($b['tags'])
                ? Api::s(implode(',', array_map(fn ($t) => Api::s($t, 40), $b['tags'])), 300)
                : Api::s($b['tags'], 300);
        }
        if (isset($b['slug']) && Api::s($b['slug'], 190) !== '') {
            $sets['slug'] = self::uniqueSlug(Api::slugify(Api::s($b['slug'], 190)), $post->id);
        }
        if (isset($b['status']) && ($st = Api::inList($b['status'], Api::BLOG_STATUS))) {
            $sets['status'] = $st;
            $publishAt = array_key_exists('publish_at', $b) ? (Api::s($b['publish_at'], 25) ?: null) : $post->publish_at;
            if ($st === 'published' && ! $publishAt) {
                $publishAt = Api::nowIso();
            }
            if ($st === 'scheduled' && ! $publishAt) {
                Api::fail('publish_at is required for scheduled posts.', 422);
            }
            $sets['publish_at'] = $publishAt;
        } elseif (array_key_exists('publish_at', $b)) {
            $sets['publish_at'] = Api::s($b['publish_at'], 25) ?: null;
        }
        if (! $sets) {
            Api::fail('Nothing to update.', 422);
        }
        $sets['updated_at'] = Api::nowIso();
        $post->update($sets);
        Api::log($who, 'update', 'blog', $post->id, $post->title);

        return Api::json(['success' => true, 'data' => self::out($post->fresh()->toArray(), true)]);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $who = Api::requireActor($request);
        $post = BlogPost::find(Api::mustInt($id)) ?? Api::fail('Post not found.', 404);
        $post->delete();
        Api::log($who, 'delete', 'blog', $post->id, $post->title);

        return Api::json(['success' => true]);
    }
}
