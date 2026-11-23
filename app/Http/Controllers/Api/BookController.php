<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Support\Api;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(): JsonResponse
    {
        $rows = Book::orderByDesc('id')->get()->toArray();

        return Api::json(['success' => true, 'total' => count($rows), 'data' => array_map([Api::class, 'ints'], $rows)]);
    }

    public function show(string $id): JsonResponse
    {
        return Api::json(['success' => true, 'data' => Api::ints($this->find($id)->toArray())]);
    }

    public function store(Request $request): JsonResponse
    {
        $who = Api::requireActor($request);
        $b = Api::body($request);
        $title = Api::s($b['title'] ?? '', 250);
        if ($title === '') {
            Api::fail('Title is required.', 422);
        }
        $slug = Api::s($b['slug'] ?? '', 190);
        if ($slug === '') {
            $base = trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($title)) ?? '', '-') ?: 'book';
            $slug = substr($base, 0, 40).'-'.substr(bin2hex(random_bytes(4)), 0, 6);
        }
        $now = Api::nowIso();
        $book = Book::create([
            'slug' => $slug, 'title' => $title, 'author' => Api::s($b['author'] ?? '', 160),
            'level' => Api::s($b['level'] ?? 'A1', 40), 'category' => Api::s($b['category'] ?? 'Grammar', 80),
            'pages' => (int) ($b['pages'] ?? 0), 'cover' => Api::s($b['cover'] ?? '', 500),
            'file_url' => Api::s($b['file_url'] ?? '', 500), 'file_name' => Api::s($b['file_name'] ?? '', 250),
            'blurb' => Api::s($b['blurb'] ?? '', 4000), 'blurb_hi' => Api::s($b['blurb_hi'] ?? '', 4000),
            'created_at' => $now, 'updated_at' => $now,
        ]);
        Api::log($who, 'create', 'book', $book->id, $title);

        return Api::json(['success' => true, 'message' => 'Book uploaded successfully.', 'data' => Api::ints($book->fresh()->toArray())]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $who = Api::requireActor($request);
        $book = $this->find($id);
        $b = Api::body($request);

        $sets = [];
        foreach (['title' => 250, 'author' => 160, 'level' => 40, 'category' => 80, 'cover' => 500, 'file_url' => 500,
            'file_name' => 250, 'blurb' => 4000, 'blurb_hi' => 4000] as $f => $max) {
            if (array_key_exists($f, $b)) {
                $sets[$f] = Api::s($b[$f], $max);
            }
        }
        if (isset($b['pages'])) {
            $sets['pages'] = (int) $b['pages'];
        }
        if (! $sets) {
            Api::fail('Nothing to update.', 422);
        }
        $sets['updated_at'] = Api::nowIso();
        $book->update($sets);
        Api::log($who, 'update', 'book', $book->id, $book->title);

        return Api::json(['success' => true, 'data' => Api::ints($book->fresh()->toArray())]);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $who = Api::requireActor($request);
        $book = $this->find($id);
        $book->delete();
        Api::log($who, 'delete', 'book', $book->id, $book->title);

        return Api::json(['success' => true, 'message' => 'Book deleted.']);
    }

    private function find(string $id): Book
    {
        $book = is_numeric($id) ? Book::find((int) $id) : Book::where('slug', $id)->first();

        return $book ?? Api::fail('Book not found.', 404);
    }
}
