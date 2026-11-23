<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Lead;
use App\Support\Api;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    /** POST /leads: public enquiry form. */
    public function store(Request $request): JsonResponse
    {
        $b = Api::body($request);
        $thanks = ['success' => true, 'message' => 'Thank you! We will get back to you shortly.'];

        // Honeypot: real visitors never fill this hidden field.
        if (! empty($b['website'])) {
            return Api::json($thanks);
        }
        Api::throttle($request, 'lead', 6, 3600);

        $name = Api::s($b['full_name'] ?? '', 160);
        $email = strtolower(Api::s($b['email'] ?? '', 190));
        $message = Api::s($b['message'] ?? ($b['motivation'] ?? ''), 3000);
        if ($name === '' || ! preg_match(Api::EMAIL_RE, $email) || $message === '') {
            Api::fail('Please provide your name, a valid email and a short message.', 422);
        }
        $phone = preg_replace('/[^0-9+\-\s()]/', '', Api::s($b['phone'] ?? '', 40)) ?? '';
        $cefr = Api::inList($b['target_cefr'] ?? null, Api::CEFR, 'B1');
        $bg = Api::s($b['background'] ?? ($b['academic_background'] ?? ''), 300);
        $source = Api::s($b['source'] ?? 'website', 120) ?: 'website';
        $now = Api::nowIso();

        $existing = Lead::where('email', $email)->first();
        if ($existing) {
            // Repeat enquiry: keep one lead per email, append the new message.
            $existing->message = Api::s($existing->message."\n\n--- ".$now." ---\n".$message, 6000);
            if ($existing->status === 'lost') {
                $existing->status = 'new';
            }
            $existing->updated_at = $now;
            $existing->save();
            $leadId = $existing->id;
        } else {
            $leadId = Lead::create([
                'full_name' => $name, 'email' => $email, 'phone' => $phone, 'target_cefr' => $cefr, 'background' => $bg,
                'message' => $message, 'source' => $source, 'status' => 'new', 'created_at' => $now, 'updated_at' => $now,
            ])->id;
        }
        Api::log('visitor', 'new_lead', 'lead', $leadId, "$name ($cefr) via $source");
        Api::notifyAdmin("New lead: $name", "Name: $name\nEmail: $email\nPhone: $phone\nGoal: $cefr\nSource: $source\n\n$message\n\nOpen: ".Api::siteUrl().'/admin/');

        return Api::json($thanks);
    }

    public function index(Request $request): JsonResponse
    {
        Api::requireActor($request);
        $q = Lead::query();

        if ($status = Api::inList($request->query('status'), Api::LEAD_STATUS)) {
            $q->where('status', $status);
        }
        if ($request->query('needs_reply')) {
            $q->where('status', 'new')->where(fn ($w) => $w->whereNull('hermes_reply')->orWhere('hermes_reply', ''));
        }
        if ($request->query('since')) {
            $q->where('created_at', '>', Api::s($request->query('since'), 25));
        }
        if ($request->query('q')) {
            $like = '%'.str_replace(['%', '_'], ['\\%', '\\_'], Api::s($request->query('q'), 80)).'%';
            $q->where(fn ($w) => $w->where('full_name', 'like', $like)->orWhere('email', 'like', $like)->orWhere('message', 'like', $like));
        }
        $limit = max(1, min(500, (int) $request->query('limit', 200)));
        $rows = $q->orderByDesc('id')->limit($limit)->get()->toArray();

        $counts = [];
        foreach (Lead::selectRaw('status, COUNT(*) as c')->groupBy('status')->get() as $r) {
            $counts[$r->status] = (int) $r->c;
        }

        return Api::json(['success' => true, 'counts' => $counts, 'total' => count($rows), 'data' => array_map([Api::class, 'ints'], $rows)]);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        Api::requireActor($request);

        return Api::json(['success' => true, 'data' => Api::ints($this->find($id)->toArray())]);
    }

    public function convert(Request $request, string $id): JsonResponse
    {
        $who = Api::requireActor($request);
        $lead = $this->find($id);

        $client = Client::where('email', $lead->email)->first();
        if (! $client) {
            $now = Api::nowIso();
            $client = Client::create([
                'lead_id' => $lead->id, 'full_name' => $lead->full_name, 'email' => $lead->email, 'phone' => $lead->phone,
                'level' => $lead->target_cefr === 'C2' ? 'C1' : 'A1', 'status' => 'active', 'notes' => (string) $lead->notes,
                'started_at' => $now, 'created_at' => $now, 'updated_at' => $now,
            ]);
        }
        $lead->update(['status' => 'converted', 'updated_at' => Api::nowIso()]);
        Api::log($who, 'convert', 'lead', $lead->id, $lead->full_name.' became a client');

        return Api::json(['success' => true, 'client' => Api::ints($client->fresh()->toArray())]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $who = Api::requireActor($request);
        $lead = $this->find($id);
        $b = Api::body($request);

        $sets = [];
        if (isset($b['status']) && ($st = Api::inList($b['status'], Api::LEAD_STATUS))) {
            $sets['status'] = $st;
        }
        foreach (['notes' => 4000, 'hermes_reply' => 6000, 'phone' => 40, 'full_name' => 160] as $f => $max) {
            if (array_key_exists($f, $b)) {
                $sets[$f] = Api::s($b[$f], $max);
            }
        }
        if (! $sets) {
            Api::fail('Nothing to update.', 422);
        }
        $sets['updated_at'] = Api::nowIso();
        $lead->update($sets);
        Api::log($who, 'update', 'lead', $lead->id, implode(',', array_diff(array_keys($sets), ['updated_at'])));

        return Api::json(['success' => true, 'data' => Api::ints($lead->fresh()->toArray())]);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $who = Api::requireActor($request);
        $lead = $this->find($id);
        $lead->delete();
        Api::log($who, 'delete', 'lead', $lead->id, $lead->full_name);

        return Api::json(['success' => true]);
    }

    private function find(string $id): Lead
    {
        return Lead::find(Api::mustInt($id)) ?? Api::fail('Lead not found.', 404);
    }
}
