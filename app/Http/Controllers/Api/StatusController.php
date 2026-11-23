<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\BlogPost;
use App\Models\Book;
use App\Models\Client;
use App\Models\Email;
use App\Models\Lead;
use App\Models\SocialPost;
use App\Models\Vocabulary;
use App\Support\Api;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    public function status(Request $request): JsonResponse
    {
        $out = [
            'name' => 'Académie de Français',
            'version' => '5.0.0',
            'status' => 'operational',
            'timestamp' => Api::nowIso(),
        ];
        if (Api::actor($request) !== null) {
            $out['statistics'] = self::computeStats();
        }

        return Api::json($out);
    }

    public function stats(Request $request): JsonResponse
    {
        Api::requireActor($request);

        return Api::json(['success' => true, 'data' => self::computeStats()]);
    }

    public function activity(Request $request): JsonResponse
    {
        Api::requireActor($request);
        $limit = max(1, min(200, (int) $request->query('limit', 40)));

        return Api::json(['success' => true, 'data' => Activity::orderByDesc('id')->limit($limit)->get()->toArray()]);
    }

    public static function computeStats(): array
    {
        return [
            'leads_new' => Lead::where('status', 'new')->count(),
            'leads_total' => Lead::count(),
            'leads_needing_reply' => Lead::where('status', 'new')
                ->where(fn ($q) => $q->whereNull('hermes_reply')->orWhere('hermes_reply', ''))->count(),
            'clients_active' => Client::where('status', 'active')->count(),
            'clients_total' => Client::count(),
            'blog_published' => Api::wherePublic(BlogPost::query())->count(),
            'blog_drafts' => BlogPost::where('status', 'draft')->count(),
            'social_queue' => SocialPost::whereIn('status', ['draft', 'approved', 'scheduled'])->count(),
            'emails_unread' => Email::where('direction', 'in')->where('status', 'unread')->count(),
            'emails_to_send' => Email::where('direction', 'out')->whereIn('status', ['draft', 'approved'])->count(),
            'social_posted' => SocialPost::where('status', 'posted')->count(),
            'vocabulary_ingested' => Vocabulary::count(),
            'books_total' => Book::count(),
        ];
    }
}
