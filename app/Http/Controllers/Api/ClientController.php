<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Support\Api;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        Api::requireActor($request);
        $q = Client::query();
        if ($st = Api::inList($request->query('status'), Api::CLIENT_STATUS)) {
            $q->where('status', $st);
        }
        $rows = $q->orderByDesc('id')->limit(500)->get()->toArray();

        return Api::json(['success' => true, 'total' => count($rows), 'data' => array_map([Api::class, 'ints'], $rows)]);
    }

    public function store(Request $request): JsonResponse
    {
        $who = Api::requireActor($request);
        $b = Api::body($request);
        $name = Api::s($b['full_name'] ?? '', 160);
        $email = strtolower(Api::s($b['email'] ?? '', 190));
        if ($name === '' || ! preg_match(Api::EMAIL_RE, $email)) {
            Api::fail('A name and a valid email are required.', 422);
        }
        if (Client::where('email', $email)->exists()) {
            Api::fail('A client with this email already exists.', 409);
        }
        $now = Api::nowIso();
        $client = Client::create([
            'lead_id' => null, 'full_name' => $name, 'email' => $email, 'phone' => Api::s($b['phone'] ?? '', 40),
            'level' => Api::inList($b['level'] ?? null, Api::CEFR, 'A1'), 'plan' => Api::s($b['plan'] ?? '', 80),
            'status' => Api::inList($b['status'] ?? null, Api::CLIENT_STATUS, 'active'), 'notes' => Api::s($b['notes'] ?? '', 4000),
            'started_at' => $now, 'created_at' => $now, 'updated_at' => $now,
        ]);
        Api::log($who, 'create', 'client', $client->id, $name);

        return Api::json(['success' => true, 'data' => Api::ints($client->fresh()->toArray())], 201);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        Api::requireActor($request);

        return Api::json(['success' => true, 'data' => Api::ints($this->find($id)->toArray())]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $who = Api::requireActor($request);
        $client = $this->find($id);
        $b = Api::body($request);

        $sets = [];
        if (isset($b['status']) && ($v = Api::inList($b['status'], Api::CLIENT_STATUS))) {
            $sets['status'] = $v;
        }
        if (isset($b['level']) && ($v = Api::inList($b['level'], Api::CEFR))) {
            $sets['level'] = $v;
        }
        foreach (['plan' => 80, 'notes' => 4000, 'phone' => 40, 'full_name' => 160] as $f => $max) {
            if (array_key_exists($f, $b)) {
                $sets[$f] = Api::s($b[$f], $max);
            }
        }
        if (! $sets) {
            Api::fail('Nothing to update.', 422);
        }
        $sets['updated_at'] = Api::nowIso();
        $client->update($sets);
        Api::log($who, 'update', 'client', $client->id, $client->full_name);

        return Api::json(['success' => true, 'data' => Api::ints($client->fresh()->toArray())]);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $who = Api::requireActor($request);
        $client = $this->find($id);
        $client->delete();
        Api::log($who, 'delete', 'client', $client->id, $client->full_name);

        return Api::json(['success' => true]);
    }

    private function find(string $id): Client
    {
        return Client::find(Api::mustInt($id)) ?? Api::fail('Client not found.', 404);
    }
}
