<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Email;
use App\Models\Lead;
use App\Support\Api;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Mailbox credentials never live on the website. Hermes (on your PC) reads the mailbox over IMAP,
 * pushes inbound mail here, and sends the outbound mail that is in status "approved" over SMTP,
 * then marks it "sent".  inbound: unread, read, replied, archived · outbound: draft, approved, sent, failed
 */
class EmailController extends Controller
{
    private const KEYS = ['id', 'lead_id', 'reply_to_id'];

    public function index(Request $request): JsonResponse
    {
        Api::requireActor($request);
        $q = Email::query();

        if (in_array($request->query('direction'), ['in', 'out'], true)) {
            $q->where('direction', $request->query('direction'));
        }
        if (in_array($request->query('status'), array_merge(Api::EMAIL_IN, Api::EMAIL_OUT), true)) {
            $q->where('status', $request->query('status'));
        }
        if ($request->query('needs_reply')) {
            $q->where('direction', 'in')->whereIn('status', ['unread', 'read'])
                ->where(fn ($w) => $w->whereNull('hermes_draft')->orWhere('hermes_draft', ''));
        }
        $limit = max(1, min(300, (int) $request->query('limit', 100)));
        $rows = $q->orderByRaw('COALESCE(received_at, created_at) DESC')->orderByDesc('id')->limit($limit)->get()->toArray();

        $counts = [];
        foreach (Email::selectRaw('direction, status, COUNT(*) as c')->groupBy('direction', 'status')->get() as $r) {
            $counts[$r->direction.':'.$r->status] = (int) $r->c;
        }

        return Api::json(['success' => true, 'counts' => $counts, 'total' => count($rows),
            'data' => array_map(fn ($r) => Api::ints($r, self::KEYS), $rows)]);
    }

    public function store(Request $request): JsonResponse
    {
        $who = Api::requireActor($request);
        $b = Api::body($request);
        $dir = ($b['direction'] ?? 'out') === 'in' ? 'in' : 'out';
        $subject = Api::s($b['subject'] ?? '', 300);
        $text = Api::s($b['body'] ?? '', 100000);
        $now = Api::nowIso();

        if ($dir === 'in') {
            $mid = Api::s($b['message_id'] ?? '', 190);
            $from = strtolower(Api::s($b['from_addr'] ?? '', 190));
            if ($mid === '' || $from === '') {
                Api::fail('message_id and from_addr are required for inbound mail.', 422);
            }
            if ($dup = Email::where('message_id', $mid)->first()) {
                return Api::json(['success' => true, 'duplicate' => true, 'data' => ['id' => (int) $dup->id]]);
            }
            $lead = Lead::where('email', $from)->first();
            $mail = Email::create([
                'direction' => 'in', 'message_id' => $mid, 'in_reply_to' => Api::s($b['in_reply_to'] ?? '', 190),
                'from_addr' => $from, 'to_addr' => Api::s($b['to_addr'] ?? '', 190), 'subject' => $subject, 'body' => $text,
                'status' => 'unread', 'lead_id' => $lead?->id, 'received_at' => Api::s($b['received_at'] ?? '', 25) ?: $now,
                'created_by' => $who, 'created_at' => $now, 'updated_at' => $now,
            ]);
            Api::log($who, 'receive', 'email', $mail->id, "$from: $subject");

            return Api::json(['success' => true, 'data' => Api::ints($mail->fresh()->toArray(), self::KEYS)], 201);
        }

        $to = strtolower(Api::s($b['to_addr'] ?? '', 190));
        if (! preg_match(Api::EMAIL_RE, $to) || $subject === '' || $text === '') {
            Api::fail('to_addr, subject and body are required.', 422);
        }
        $replyTo = isset($b['reply_to_id']) && ctype_digit((string) $b['reply_to_id']) ? (int) $b['reply_to_id'] : null;
        $parent = $replyTo ? Email::find($replyTo) : null;
        // Hermes may only draft; sending requires an admin to approve in the dashboard.
        $status = Api::inList($b['status'] ?? null, Api::EMAIL_OUT, 'draft');
        if ($who === 'hermes' && $status !== 'draft') {
            $status = 'draft';
        }
        $mail = Email::create([
            'direction' => 'out', 'in_reply_to' => $parent ? (string) $parent->message_id : '', 'to_addr' => $to,
            'subject' => $subject, 'body' => $text, 'status' => $status, 'lead_id' => $parent?->lead_id,
            'reply_to_id' => $replyTo, 'created_by' => $who, 'created_at' => $now, 'updated_at' => $now,
        ]);
        Api::log($who, 'create', 'email', $mail->id, "to $to [$status]");

        return Api::json(['success' => true, 'data' => Api::ints($mail->fresh()->toArray(), self::KEYS)], 201);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        Api::requireActor($request);

        return Api::json(['success' => true, 'data' => Api::ints($this->find($id)->toArray(), self::KEYS)]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $who = Api::requireActor($request);
        $mail = $this->find($id);
        $b = Api::body($request);

        $sets = [];
        $allowed = $mail->direction === 'in' ? Api::EMAIL_IN : Api::EMAIL_OUT;
        if (isset($b['status']) && ($v = Api::inList($b['status'], $allowed))) {
            // Only an admin may approve outbound mail for sending.
            if ($who === 'hermes' && $mail->direction === 'out' && $v === 'approved') {
                Api::fail('Outbound mail must be approved by an admin in the dashboard.', 403);
            }
            $sets['status'] = $v;
            if ($v === 'sent') {
                $sets['sent_at'] = Api::nowIso();
            }
        }
        if (array_key_exists('hermes_draft', $b) && $mail->direction === 'in') {
            $sets['hermes_draft'] = Api::s($b['hermes_draft'], 20000);
        }
        if ($mail->direction === 'out' && $mail->status !== 'sent') {
            foreach (['subject' => 300, 'body' => 100000, 'to_addr' => 190] as $f => $max) {
                if (array_key_exists($f, $b)) {
                    $v = Api::s($b[$f], $max);
                    if ($f === 'to_addr' && ! preg_match(Api::EMAIL_RE, $v)) {
                        Api::fail('Invalid recipient.', 422);
                    }
                    if ($who === 'hermes' && $mail->status === 'approved') {
                        Api::fail('Approved mail cannot be edited by Hermes.', 403);
                    }
                    $sets[$f] = $v;
                }
            }
        }
        if (! $sets) {
            Api::fail('Nothing to update.', 422);
        }
        $sets['updated_at'] = Api::nowIso();
        $mail->update($sets);
        if ($mail->direction === 'out' && ($b['status'] ?? '') === 'sent' && $mail->reply_to_id) {
            Email::where('id', $mail->reply_to_id)->update(['status' => 'replied', 'updated_at' => Api::nowIso()]);
        }
        Api::log($who, 'update', 'email', $mail->id, $mail->subject);

        return Api::json(['success' => true, 'data' => Api::ints($mail->fresh()->toArray(), self::KEYS)]);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $who = Api::requireActor($request);
        $mail = $this->find($id);
        $mail->delete();
        Api::log($who, 'delete', 'email', $mail->id, $mail->subject);

        return Api::json(['success' => true]);
    }

    private function find(string $id): Email
    {
        return Email::find(Api::mustInt($id)) ?? Api::fail('Email not found.', 404);
    }
}
