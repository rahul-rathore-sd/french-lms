<?php
declare(strict_types=1);

require __DIR__ . '/lib.php';

/*
 * Router for /api/v1/*
 *
 * Public:   POST leads · GET blog · GET blog/{slug} · GET vocabulary · GET status · GET sitemap
 * Admin or Hermes (session cookie / Bearer key): everything else.
 */

const CEFR = ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'];
const LEAD_STATUS = ['new', 'contacted', 'qualified', 'converted', 'lost'];
const CLIENT_STATUS = ['active', 'paused', 'completed'];
const BLOG_STATUS = ['draft', 'published', 'scheduled'];
const SOCIAL_STATUS = ['draft', 'approved', 'scheduled', 'posted', 'failed'];
const PLATFORMS = ['instagram', 'facebook', 'linkedin', 'x', 'youtube', 'whatsapp', 'other'];
const EMAIL_IN = ['unread', 'read', 'replied', 'archived'];
const EMAIL_OUT = ['draft', 'approved', 'sent', 'failed'];
const EMAIL_RE = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';

header('X-Content-Type-Options: nosniff');

try {
    route();
} catch (PDOException $e) {
    error_log('Hermes API DB error: ' . $e->getMessage());
    fail('Database error. Check the database settings in api/config.php.', 500);
} catch (Throwable $e) {
    error_log('Hermes API error: ' . $e->getMessage());
    fail('Server error.', 500);
}

/* ------------------------------------------------------------------------ */

function route()
{
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method === 'OPTIONS') {
        http_response_code(204);
        exit;
    }

    if (isset($_GET['r'])) {
        $path = (string) $_GET['r'];
    } else {
        $uri = (string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/api/index.php')), '/');
        $path = str_starts_with($uri, $base) ? substr($uri, strlen($base)) : $uri;
    }
    $path = preg_replace('#^/?v1/#', '', trim($path, '/')) ?? '';
    $seg = $path === '' ? [] : explode('/', $path);
    $res = $seg[0] ?? 'status';
    $id = $seg[1] ?? null;
    $sub = $seg[2] ?? null;

    switch ($res) {
        case 'status':
            return handle_status();
        case 'sitemap':
            return handle_sitemap();
        case 'admin':
            return handle_admin($method, $id ?? '');
        case 'leads':
        case 'admissions': // legacy alias
            return handle_leads($method, $id, $sub);
        case 'clients':
            return handle_clients($method, $id);
        case 'blog':
            return handle_blog($method, $id);
        case 'social':
            return handle_social($method, $id);
        case 'emails':
            return handle_emails($method, $id);
        case 'vocabulary':
            return handle_vocabulary($method);
        case 'books':
            return handle_books($method, $id);
        case 'activity':
            require_actor();
            $limit = max(1, min(200, (int) ($_GET['limit'] ?? 40)));
            return json_out(['success' => true, 'data' => q("SELECT * FROM activity ORDER BY id DESC LIMIT $limit")->fetchAll()]);
        case 'stats':
            require_actor();
            return json_out(['success' => true, 'data' => stats()]);
    }
    fail('Not found.', 404);
}

function must_int(?string $id): int
{
    if ($id === null || !ctype_digit($id)) {
        fail('A numeric id is required.', 422);
    }
    return (int) $id;
}

function ints(array $row, array $keys = ['id', 'lead_id', 'blog_id', 'entity_id']): array
{
    foreach ($keys as $k) {
        if (array_key_exists($k, $row) && $row[$k] !== null) {
            $row[$k] = (int) $row[$k];
        }
    }
    return $row;
}

function in_list(mixed $v, array $allowed, ?string $default = null): ?string
{
    return is_string($v) && in_array($v, $allowed, true) ? $v : $default;
}

function stats(): array
{
    $count = static fn(string $sql, array $p = []): int => (int) q($sql, $p)->fetch()['c'];
    $now = now_iso();
    return [
        'leads_new' => $count("SELECT COUNT(*) c FROM leads WHERE status = 'new'"),
        'leads_total' => $count('SELECT COUNT(*) c FROM leads'),
        'leads_needing_reply' => $count("SELECT COUNT(*) c FROM leads WHERE status = 'new' AND (hermes_reply IS NULL OR hermes_reply = '')"),
        'clients_active' => $count("SELECT COUNT(*) c FROM clients WHERE status = 'active'"),
        'clients_total' => $count('SELECT COUNT(*) c FROM clients'),
        'blog_published' => $count('SELECT COUNT(*) c FROM blog_posts WHERE ' . public_post_sql(), [$now]),
        'blog_drafts' => $count("SELECT COUNT(*) c FROM blog_posts WHERE status = 'draft'"),
        'social_queue' => $count("SELECT COUNT(*) c FROM social_posts WHERE status IN ('draft','approved','scheduled')"),
        'emails_unread' => $count("SELECT COUNT(*) c FROM emails WHERE direction = 'in' AND status = 'unread'"),
        'emails_to_send' => $count("SELECT COUNT(*) c FROM emails WHERE direction = 'out' AND status IN ('draft','approved')"),
        'social_posted' => $count("SELECT COUNT(*) c FROM social_posts WHERE status = 'posted'"),
        'vocabulary_ingested' => $count('SELECT COUNT(*) c FROM vocabulary'),
        'books_total' => $count('SELECT COUNT(*) c FROM books'),
    ];
}

/* ------------------------------------------------------------ status ---- */

function handle_status(): void
{
    $out = [
        'name' => 'Académie de Français',
        'version' => '4.0.0',
        'status' => 'operational',
        'timestamp' => now_iso(),
    ];
    if (actor() !== null) {
        $out['statistics'] = stats();
    }
    json_out($out);
}

/* ------------------------------------------------------------- admin ---- */

function handle_admin(string $method, string $action): void
{
    if ($action === 'me') {
        json_out(['success' => true, 'authenticated' => actor() === 'admin']);
    }
    if ($method !== 'POST') {
        fail('Method not allowed.', 405);
    }
    if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== 'fetch') {
        fail('Bad request.', 403);
    }
    if ($action === 'logout') {
        start_admin_session();
        $_SESSION = [];
        session_destroy();
        json_out(['success' => true]);
    }
    if ($action === 'login') {
        throttle('login', 8, 900);
        $hash = (string) cfg('admin_password_hash', '');
        $password = (string) (body()['password'] ?? '');
        if ($hash === '' || $password === '' || !password_verify($password, $hash)) {
            usleep(400_000);
            fail('Wrong password.', 401);
        }
        start_admin_session();
        session_regenerate_id(true);
        $_SESSION['admin'] = true;
        json_out(['success' => true]);
    }
    fail('Not found.', 404);
}

/* ------------------------------------------------------------- leads ---- */

function handle_leads(string $method, ?string $id, ?string $sub): void
{
    if ($method === 'POST' && $id === null) {
        create_lead();
    }
    $who = require_actor();

    if ($method === 'GET' && $id === null) {
        $where = ['1=1'];
        $params = [];
        $status = in_list($_GET['status'] ?? null, LEAD_STATUS);
        if ($status) {
            $where[] = 'status = ?';
            $params[] = $status;
        }
        if (!empty($_GET['needs_reply'])) {
            $where[] = "status = 'new' AND (hermes_reply IS NULL OR hermes_reply = '')";
        }
        if (!empty($_GET['since'])) {
            $where[] = 'created_at > ?';
            $params[] = s($_GET['since'], 25);
        }
        if (!empty($_GET['q'])) {
            $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], s($_GET['q'], 80)) . '%';
            $where[] = '(full_name LIKE ? OR email LIKE ? OR message LIKE ?)';
            array_push($params, $like, $like, $like);
        }
        $limit = max(1, min(500, (int) ($_GET['limit'] ?? 200)));
        $rows = q('SELECT * FROM leads WHERE ' . implode(' AND ', $where) . " ORDER BY id DESC LIMIT $limit", $params)->fetchAll();
        $counts = [];
        foreach (q('SELECT status, COUNT(*) c FROM leads GROUP BY status')->fetchAll() as $r) {
            $counts[$r['status']] = (int) $r['c'];
        }
        json_out(['success' => true, 'counts' => $counts, 'total' => count($rows), 'data' => array_map('ints', $rows)]);
    }

    $leadId = must_int($id);
    $lead = q('SELECT * FROM leads WHERE id = ?', [$leadId])->fetch();
    if (!$lead) {
        fail('Lead not found.', 404);
    }

    if ($method === 'GET') {
        json_out(['success' => true, 'data' => ints($lead)]);
    }

    if ($method === 'POST' && $sub === 'convert') {
        $client = q('SELECT * FROM clients WHERE email = ?', [$lead['email']])->fetch();
        if (!$client) {
            $now = now_iso();
            q('INSERT INTO clients (lead_id, full_name, email, phone, level, status, notes, started_at, created_at, updated_at)
               VALUES (?,?,?,?,?,?,?,?,?,?)',
                [$leadId, $lead['full_name'], $lead['email'], $lead['phone'], $lead['target_cefr'] === 'C2' ? 'C1' : 'A1',
                    'active', (string) $lead['notes'], $now, $now, $now]);
            $client = q('SELECT * FROM clients WHERE id = ?', [insert_id()])->fetch();
        }
        q("UPDATE leads SET status = 'converted', updated_at = ? WHERE id = ?", [now_iso(), $leadId]);
        log_activity($who, 'convert', 'lead', $leadId, $lead['full_name'] . ' became a client');
        json_out(['success' => true, 'client' => ints($client)]);
    }

    if ($method === 'PATCH') {
        $b = body();
        $sets = [];
        $params = [];
        if (isset($b['status']) && ($st = in_list($b['status'], LEAD_STATUS))) {
            $sets[] = 'status = ?';
            $params[] = $st;
        }
        foreach (['notes' => 4000, 'hermes_reply' => 6000, 'phone' => 40, 'full_name' => 160] as $f => $max) {
            if (array_key_exists($f, $b)) {
                $sets[] = "$f = ?";
                $params[] = s($b[$f], $max);
            }
        }
        if (!$sets) {
            fail('Nothing to update.', 422);
        }
        $sets[] = 'updated_at = ?';
        $params[] = now_iso();
        $params[] = $leadId;
        q('UPDATE leads SET ' . implode(', ', $sets) . ' WHERE id = ?', $params);
        log_activity($who, 'update', 'lead', $leadId, implode(',', array_keys(array_intersect_key($b, array_flip(['status', 'notes', 'hermes_reply', 'phone', 'full_name'])))));
        json_out(['success' => true, 'data' => ints(q('SELECT * FROM leads WHERE id = ?', [$leadId])->fetch())]);
    }

    if ($method === 'DELETE') {
        q('DELETE FROM leads WHERE id = ?', [$leadId]);
        log_activity($who, 'delete', 'lead', $leadId, $lead['full_name']);
        json_out(['success' => true]);
    }
    fail('Method not allowed.', 405);
}

function create_lead(): never
{
    $b = body();
    // Honeypot: real visitors never fill this hidden field.
    if (!empty($b['website'])) {
        json_out(['success' => true, 'message' => 'Thank you! We will get back to you shortly.']);
    }
    throttle('lead', 6, 3600);

    $name = s($b['full_name'] ?? '', 160);
    $email = strtolower(s($b['email'] ?? '', 190));
    $message = s($b['message'] ?? ($b['motivation'] ?? ''), 3000);
    if ($name === '' || !preg_match(EMAIL_RE, $email) || $message === '') {
        fail('Please provide your name, a valid email and a short message.', 422);
    }
    $phone = preg_replace('/[^0-9+\-\s()]/', '', s($b['phone'] ?? '', 40)) ?? '';
    $cefr = in_list($b['target_cefr'] ?? null, CEFR, 'B1');
    $bg = s($b['background'] ?? ($b['academic_background'] ?? ''), 300);
    $source = s($b['source'] ?? 'website', 120) ?: 'website';
    $now = now_iso();

    $existing = q('SELECT id, status, message FROM leads WHERE email = ?', [$email])->fetch();
    if ($existing) {
        // Repeat enquiry: keep one lead per email, append the new message.
        $merged = s($existing['message'] . "\n\n--- " . $now . " ---\n" . $message, 6000);
        $status = $existing['status'] === 'lost' ? 'new' : $existing['status'];
        q('UPDATE leads SET message = ?, status = ?, updated_at = ? WHERE id = ?', [$merged, $status, $now, $existing['id']]);
        $leadId = (int) $existing['id'];
    } else {
        q('INSERT INTO leads (full_name, email, phone, target_cefr, background, message, source, status, created_at, updated_at)
           VALUES (?,?,?,?,?,?,?,?,?,?)', [$name, $email, $phone, $cefr, $bg, $message, $source, 'new', $now, $now]);
        $leadId = insert_id();
    }
    log_activity('visitor', 'new_lead', 'lead', $leadId, "$name ($cefr) via $source");
    notify_admin("New lead: $name", "Name: $name\nEmail: $email\nPhone: $phone\nGoal: $cefr\nSource: $source\n\n$message\n\nOpen: " . site_url() . '/admin/');

    json_out(['success' => true, 'message' => 'Thank you! We will get back to you shortly.']);
}

/* ----------------------------------------------------------- clients ---- */

function handle_clients(string $method, ?string $id): void
{
    $who = require_actor();

    if ($method === 'GET' && $id === null) {
        $params = [];
        $where = '1=1';
        if ($st = in_list($_GET['status'] ?? null, CLIENT_STATUS)) {
            $where = 'status = ?';
            $params[] = $st;
        }
        $rows = q("SELECT * FROM clients WHERE $where ORDER BY id DESC LIMIT 500", $params)->fetchAll();
        json_out(['success' => true, 'total' => count($rows), 'data' => array_map('ints', $rows)]);
    }

    if ($method === 'POST' && $id === null) {
        $b = body();
        $name = s($b['full_name'] ?? '', 160);
        $email = strtolower(s($b['email'] ?? '', 190));
        if ($name === '' || !preg_match(EMAIL_RE, $email)) {
            fail('A name and a valid email are required.', 422);
        }
        if (q('SELECT id FROM clients WHERE email = ?', [$email])->fetch()) {
            fail('A client with this email already exists.', 409);
        }
        $now = now_iso();
        q('INSERT INTO clients (lead_id, full_name, email, phone, level, plan, status, notes, started_at, created_at, updated_at)
           VALUES (?,?,?,?,?,?,?,?,?,?,?)', [
            null, $name, $email, s($b['phone'] ?? '', 40), in_list($b['level'] ?? null, CEFR, 'A1'), s($b['plan'] ?? '', 80),
            in_list($b['status'] ?? null, CLIENT_STATUS, 'active'), s($b['notes'] ?? '', 4000), $now, $now, $now,
        ]);
        $newId = insert_id();
        log_activity($who, 'create', 'client', $newId, $name);
        json_out(['success' => true, 'data' => ints(q('SELECT * FROM clients WHERE id = ?', [$newId])->fetch())], 201);
    }

    $cid = must_int($id);
    $client = q('SELECT * FROM clients WHERE id = ?', [$cid])->fetch();
    if (!$client) {
        fail('Client not found.', 404);
    }
    if ($method === 'GET') {
        json_out(['success' => true, 'data' => ints($client)]);
    }
    if ($method === 'PATCH') {
        $b = body();
        $sets = [];
        $params = [];
        if (isset($b['status']) && ($v = in_list($b['status'], CLIENT_STATUS))) {
            $sets[] = 'status = ?';
            $params[] = $v;
        }
        if (isset($b['level']) && ($v = in_list($b['level'], CEFR))) {
            $sets[] = 'level = ?';
            $params[] = $v;
        }
        foreach (['plan' => 80, 'notes' => 4000, 'phone' => 40, 'full_name' => 160] as $f => $max) {
            if (array_key_exists($f, $b)) {
                $sets[] = "$f = ?";
                $params[] = s($b[$f], $max);
            }
        }
        if (!$sets) {
            fail('Nothing to update.', 422);
        }
        $sets[] = 'updated_at = ?';
        $params[] = now_iso();
        $params[] = $cid;
        q('UPDATE clients SET ' . implode(', ', $sets) . ' WHERE id = ?', $params);
        log_activity($who, 'update', 'client', $cid, $client['full_name']);
        json_out(['success' => true, 'data' => ints(q('SELECT * FROM clients WHERE id = ?', [$cid])->fetch())]);
    }
    if ($method === 'DELETE') {
        q('DELETE FROM clients WHERE id = ?', [$cid]);
        log_activity($who, 'delete', 'client', $cid, $client['full_name']);
        json_out(['success' => true]);
    }
    fail('Method not allowed.', 405);
}

/* -------------------------------------------------------------- blog ---- */

function blog_out(array $r, bool $withContent): array
{
    $r = ints($r);
    $r['tags'] = array_values(array_filter(array_map('trim', explode(',', (string) $r['tags']))));
    $r['url'] = site_url() . '/blog/' . $r['slug'] . '/';
    if ($withContent) {
        $r['content_html'] = md_to_html((string) $r['content_md']);
    } else {
        unset($r['content_md']);
    }
    return $r;
}

function unique_slug(string $base, ?int $exceptId = null): string
{
    $slug = $base;
    $n = 2;
    while (true) {
        $row = q('SELECT id FROM blog_posts WHERE slug = ?', [$slug])->fetch();
        if (!$row || ($exceptId !== null && (int) $row['id'] === $exceptId)) {
            return $slug;
        }
        $slug = substr($base, 0, 110) . '-' . $n++;
    }
}

function handle_blog(string $method, ?string $id): void
{
    $who = actor();

    if ($method === 'GET') {
        $isAdmin = $who !== null;
        if ($id === null) {
            $limit = max(1, min(200, (int) ($_GET['limit'] ?? 20)));
            $where = public_post_sql();
            $params = [now_iso()];
            if ($isAdmin && isset($_GET['status'])) {
                if ($_GET['status'] === 'all') {
                    $where = '1=1';
                    $params = [];
                } elseif ($st = in_list($_GET['status'], BLOG_STATUS)) {
                    $where = 'status = ?';
                    $params = [$st];
                }
            }
            $full = $isAdmin && !empty($_GET['full']);
            $rows = q("SELECT * FROM blog_posts WHERE $where ORDER BY COALESCE(publish_at, created_at) DESC, id DESC LIMIT $limit", $params)->fetchAll();
            json_out(['success' => true, 'total' => count($rows), 'data' => array_map(fn($r) => blog_out($r, $full), $rows)]);
        }
        $row = ctype_digit($id)
            ? q('SELECT * FROM blog_posts WHERE id = ?', [(int) $id])->fetch()
            : q('SELECT * FROM blog_posts WHERE slug = ?', [$id])->fetch();
        if (!$row || (!$isAdmin && !is_public_post($row))) {
            fail('Post not found.', 404);
        }
        $out = blog_out($row, true);
        if ($isAdmin) {
            $out['content_md'] = $row['content_md'];
        }
        json_out(['success' => true, 'data' => $out]);
    }

    $who = require_actor();

    if ($method === 'POST' && $id === null) {
        $b = body();
        $title = s($b['title'] ?? '', 250);
        $content = s($b['content_md'] ?? '', 200000);
        if ($title === '' || $content === '') {
            fail('title and content_md are required.', 422);
        }
        $status = in_list($b['status'] ?? null, BLOG_STATUS, 'draft');
        $publishAt = s($b['publish_at'] ?? '', 25) ?: null;
        if ($status === 'published' && !$publishAt) {
            $publishAt = now_iso();
        }
        if ($status === 'scheduled' && !$publishAt) {
            fail('publish_at is required for scheduled posts.', 422);
        }
        $tags = is_array($b['tags'] ?? null) ? implode(',', array_map(fn($t) => s($t, 40), $b['tags'])) : s($b['tags'] ?? '', 300);
        $explicit = isset($b['slug']) && s($b['slug'], 190) !== '';
        $slug = slugify($explicit ? s($b['slug'], 190) : $title);

        // An explicit slug that already exists updates that post (idempotent for agents).
        $existing = $explicit ? q('SELECT id FROM blog_posts WHERE slug = ?', [$slug])->fetch() : false;
        $now = now_iso();
        $fields = [
            $title, s($b['excerpt'] ?? '', 500), $content, s($tags, 300), s($b['cover_image'] ?? '', 500),
            $status, $publishAt, s($b['author'] ?? 'Académie de Français', 80), s($b['meta_description'] ?? '', 300),
        ];
        if ($existing) {
            q('UPDATE blog_posts SET title=?, excerpt=?, content_md=?, tags=?, cover_image=?, status=?, publish_at=?, author=?, meta_description=?, updated_at=? WHERE id = ?',
                [...$fields, $now, $existing['id']]);
            $postId = (int) $existing['id'];
            log_activity($who, 'update', 'blog', $postId, $title);
        } else {
            q('INSERT INTO blog_posts (title, excerpt, content_md, tags, cover_image, status, publish_at, author, meta_description, slug, created_by, created_at, updated_at)
               VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)', [...$fields, unique_slug($slug), $who, $now, $now]);
            $postId = insert_id();
            log_activity($who, 'create', 'blog', $postId, "$title [$status]");
        }
        json_out(['success' => true, 'data' => blog_out(q('SELECT * FROM blog_posts WHERE id = ?', [$postId])->fetch(), true)], $existing ? 200 : 201);
    }

    $pid = must_int($id);
    $post = q('SELECT * FROM blog_posts WHERE id = ?', [$pid])->fetch();
    if (!$post) {
        fail('Post not found.', 404);
    }

    if ($method === 'PATCH') {
        $b = body();
        $sets = [];
        $params = [];
        $map = ['title' => 250, 'excerpt' => 500, 'content_md' => 200000, 'cover_image' => 500, 'author' => 80, 'meta_description' => 300];
        foreach ($map as $f => $max) {
            if (array_key_exists($f, $b)) {
                $v = s($b[$f], $max);
                if (($f === 'title' || $f === 'content_md') && $v === '') {
                    fail("$f cannot be empty.", 422);
                }
                $sets[] = "$f = ?";
                $params[] = $v;
            }
        }
        if (array_key_exists('tags', $b)) {
            $sets[] = 'tags = ?';
            $params[] = is_array($b['tags']) ? s(implode(',', array_map(fn($t) => s($t, 40), $b['tags'])), 300) : s($b['tags'], 300);
        }
        if (isset($b['slug']) && s($b['slug'], 190) !== '') {
            $sets[] = 'slug = ?';
            $params[] = unique_slug(slugify(s($b['slug'], 190)), $pid);
        }
        if (isset($b['status']) && ($st = in_list($b['status'], BLOG_STATUS))) {
            $sets[] = 'status = ?';
            $params[] = $st;
            $publishAt = array_key_exists('publish_at', $b) ? (s($b['publish_at'], 25) ?: null) : $post['publish_at'];
            if ($st === 'published' && !$publishAt) {
                $publishAt = now_iso();
            }
            if ($st === 'scheduled' && !$publishAt) {
                fail('publish_at is required for scheduled posts.', 422);
            }
            $sets[] = 'publish_at = ?';
            $params[] = $publishAt;
        } elseif (array_key_exists('publish_at', $b)) {
            $sets[] = 'publish_at = ?';
            $params[] = s($b['publish_at'], 25) ?: null;
        }
        if (!$sets) {
            fail('Nothing to update.', 422);
        }
        $sets[] = 'updated_at = ?';
        $params[] = now_iso();
        $params[] = $pid;
        q('UPDATE blog_posts SET ' . implode(', ', $sets) . ' WHERE id = ?', $params);
        log_activity($who, 'update', 'blog', $pid, $post['title']);
        json_out(['success' => true, 'data' => blog_out(q('SELECT * FROM blog_posts WHERE id = ?', [$pid])->fetch(), true)]);
    }
    if ($method === 'DELETE') {
        q('DELETE FROM blog_posts WHERE id = ?', [$pid]);
        log_activity($who, 'delete', 'blog', $pid, $post['title']);
        json_out(['success' => true]);
    }
    fail('Method not allowed.', 405);
}

/* ------------------------------------------------------------ social ---- */

function handle_social(string $method, ?string $id): void
{
    $who = require_actor();

    if ($method === 'GET' && $id === null) {
        $where = ['1=1'];
        $params = [];
        if ($st = in_list($_GET['status'] ?? null, SOCIAL_STATUS)) {
            $where[] = 'status = ?';
            $params[] = $st;
        }
        if (!empty($_GET['blog_id']) && ctype_digit((string) $_GET['blog_id'])) {
            $where[] = 'blog_id = ?';
            $params[] = (int) $_GET['blog_id'];
        }
        $rows = q('SELECT * FROM social_posts WHERE ' . implode(' AND ', $where) . ' ORDER BY id DESC LIMIT 300', $params)->fetchAll();
        json_out(['success' => true, 'total' => count($rows), 'data' => array_map('ints', $rows)]);
    }

    if ($method === 'POST' && $id === null) {
        $b = body();
        $content = s($b['content'] ?? '', 4000);
        $platform = in_list($b['platform'] ?? null, PLATFORMS);
        if ($content === '' || !$platform) {
            fail('platform (' . implode(', ', PLATFORMS) . ') and content are required.', 422);
        }
        $status = in_list($b['status'] ?? null, SOCIAL_STATUS, 'draft');
        $sched = s($b['scheduled_at'] ?? '', 25) ?: null;
        $blogId = isset($b['blog_id']) && ctype_digit((string) $b['blog_id']) ? (int) $b['blog_id'] : null;
        $now = now_iso();
        q('INSERT INTO social_posts (platform, content, link, media_url, status, scheduled_at, blog_id, created_by, created_at, updated_at)
           VALUES (?,?,?,?,?,?,?,?,?,?)', [$platform, $content, s($b['link'] ?? '', 500), s($b['media_url'] ?? '', 500), $status, $sched, $blogId, $who, $now, $now]);
        $newId = insert_id();
        log_activity($who, 'create', 'social', $newId, "$platform [$status]");
        json_out(['success' => true, 'data' => ints(q('SELECT * FROM social_posts WHERE id = ?', [$newId])->fetch())], 201);
    }

    $sid = must_int($id);
    $post = q('SELECT * FROM social_posts WHERE id = ?', [$sid])->fetch();
    if (!$post) {
        fail('Post not found.', 404);
    }
    if ($method === 'GET') {
        json_out(['success' => true, 'data' => ints($post)]);
    }
    if ($method === 'PATCH') {
        $b = body();
        $sets = [];
        $params = [];
        if (isset($b['platform']) && ($v = in_list($b['platform'], PLATFORMS))) {
            $sets[] = 'platform = ?';
            $params[] = $v;
        }
        if (isset($b['status']) && ($v = in_list($b['status'], SOCIAL_STATUS))) {
            $sets[] = 'status = ?';
            $params[] = $v;
            if ($v === 'posted') {
                $sets[] = 'posted_at = ?';
                $params[] = now_iso();
            }
        }
        foreach (['content' => 4000, 'link' => 500, 'media_url' => 500, 'post_url' => 500] as $f => $max) {
            if (array_key_exists($f, $b)) {
                $sets[] = "$f = ?";
                $params[] = s($b[$f], $max);
            }
        }
        if (array_key_exists('scheduled_at', $b)) {
            $sets[] = 'scheduled_at = ?';
            $params[] = s($b['scheduled_at'], 25) ?: null;
        }
        if (!$sets) {
            fail('Nothing to update.', 422);
        }
        $sets[] = 'updated_at = ?';
        $params[] = now_iso();
        $params[] = $sid;
        q('UPDATE social_posts SET ' . implode(', ', $sets) . ' WHERE id = ?', $params);
        log_activity($who, 'update', 'social', $sid, $post['platform']);
        json_out(['success' => true, 'data' => ints(q('SELECT * FROM social_posts WHERE id = ?', [$sid])->fetch())]);
    }
    if ($method === 'DELETE') {
        q('DELETE FROM social_posts WHERE id = ?', [$sid]);
        log_activity($who, 'delete', 'social', $sid, $post['platform']);
        json_out(['success' => true]);
    }
    fail('Method not allowed.', 405);
}

/* ------------------------------------------------------------ emails ---- */
/*
 * Mailbox credentials never live on the website. Hermes (on your PC) reads the
 * mailbox over IMAP, pushes inbound mail here, and sends the outbound mail that
 * is in status "approved" over SMTP, then marks it "sent".
 *   inbound  statuses: unread, read, replied, archived
 *   outbound statuses: draft, approved, sent, failed
 */

function handle_emails(string $method, ?string $id): void
{
    $who = require_actor();

    if ($method === 'GET' && $id === null) {
        $where = ['1=1'];
        $params = [];
        if (in_array($_GET['direction'] ?? '', ['in', 'out'], true)) {
            $where[] = 'direction = ?';
            $params[] = $_GET['direction'];
        }
        if (in_array($_GET['status'] ?? '', array_merge(EMAIL_IN, EMAIL_OUT), true)) {
            $where[] = 'status = ?';
            $params[] = $_GET['status'];
        }
        if (!empty($_GET['needs_reply'])) {
            $where[] = "direction = 'in' AND status IN ('unread','read') AND (hermes_draft IS NULL OR hermes_draft = '')";
        }
        $limit = max(1, min(300, (int) ($_GET['limit'] ?? 100)));
        $rows = q('SELECT * FROM emails WHERE ' . implode(' AND ', $where) . " ORDER BY COALESCE(received_at, created_at) DESC, id DESC LIMIT $limit", $params)->fetchAll();
        $counts = [];
        foreach (q('SELECT direction, status, COUNT(*) c FROM emails GROUP BY direction, status')->fetchAll() as $r) {
            $counts[$r['direction'] . ':' . $r['status']] = (int) $r['c'];
        }
        json_out(['success' => true, 'counts' => $counts, 'total' => count($rows), 'data' => array_map(fn($r) => ints($r, ['id', 'lead_id', 'reply_to_id']), $rows)]);
    }

    if ($method === 'POST' && $id === null) {
        $b = body();
        $dir = ($b['direction'] ?? 'out') === 'in' ? 'in' : 'out';
        $subject = s($b['subject'] ?? '', 300);
        $text = s($b['body'] ?? '', 100000);
        $now = now_iso();
        if ($dir === 'in') {
            $mid = s($b['message_id'] ?? '', 190);
            $from = strtolower(s($b['from_addr'] ?? '', 190));
            if ($mid === '' || $from === '') {
                fail('message_id and from_addr are required for inbound mail.', 422);
            }
            $dup = q('SELECT id FROM emails WHERE message_id = ?', [$mid])->fetch();
            if ($dup) {
                json_out(['success' => true, 'duplicate' => true, 'data' => ['id' => (int) $dup['id']]]);
            }
            $lead = q('SELECT id FROM leads WHERE email = ?', [$from])->fetch();
            q('INSERT INTO emails (direction, message_id, in_reply_to, from_addr, to_addr, subject, body, status, lead_id, received_at, created_by, created_at, updated_at)
               VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)', ['in', $mid, s($b['in_reply_to'] ?? '', 190), $from, s($b['to_addr'] ?? '', 190), $subject, $text, 'unread',
                $lead ? (int) $lead['id'] : null, s($b['received_at'] ?? '', 25) ?: $now, $who, $now, $now]);
            $newId = insert_id();
            log_activity($who, 'receive', 'email', $newId, "$from: $subject");
            json_out(['success' => true, 'data' => ints(q('SELECT * FROM emails WHERE id = ?', [$newId])->fetch(), ['id', 'lead_id', 'reply_to_id'])], 201);
        }
        $to = strtolower(s($b['to_addr'] ?? '', 190));
        if (!preg_match(EMAIL_RE, $to) || $subject === '' || $text === '') {
            fail('to_addr, subject and body are required.', 422);
        }
        $replyTo = isset($b['reply_to_id']) && ctype_digit((string) $b['reply_to_id']) ? (int) $b['reply_to_id'] : null;
        $parent = $replyTo ? q('SELECT message_id, lead_id FROM emails WHERE id = ?', [$replyTo])->fetch() : false;
        // Hermes may only draft; sending requires an admin to approve in the dashboard.
        $status = in_list($b['status'] ?? null, EMAIL_OUT, 'draft');
        if ($who === 'hermes' && $status !== 'draft') {
            $status = 'draft';
        }
        q('INSERT INTO emails (direction, in_reply_to, to_addr, subject, body, status, lead_id, reply_to_id, created_by, created_at, updated_at)
           VALUES (?,?,?,?,?,?,?,?,?,?,?)', ['out', $parent ? (string) $parent['message_id'] : '', $to, $subject, $text, $status,
            $parent && $parent['lead_id'] ? (int) $parent['lead_id'] : null, $replyTo, $who, $now, $now]);
        $newId = insert_id();
        log_activity($who, 'create', 'email', $newId, "to $to [$status]");
        json_out(['success' => true, 'data' => ints(q('SELECT * FROM emails WHERE id = ?', [$newId])->fetch(), ['id', 'lead_id', 'reply_to_id'])], 201);
    }

    $eid = must_int($id);
    $mail = q('SELECT * FROM emails WHERE id = ?', [$eid])->fetch();
    if (!$mail) {
        fail('Email not found.', 404);
    }
    if ($method === 'GET') {
        json_out(['success' => true, 'data' => ints($mail, ['id', 'lead_id', 'reply_to_id'])]);
    }
    if ($method === 'PATCH') {
        $b = body();
        $sets = [];
        $params = [];
        $allowed = $mail['direction'] === 'in' ? EMAIL_IN : EMAIL_OUT;
        if (isset($b['status']) && ($v = in_list($b['status'], $allowed))) {
            // Only an admin may approve outbound mail for sending.
            if ($who === 'hermes' && $mail['direction'] === 'out' && $v === 'approved') {
                fail('Outbound mail must be approved by an admin in the dashboard.', 403);
            }
            $sets[] = 'status = ?';
            $params[] = $v;
            if ($v === 'sent') {
                $sets[] = 'sent_at = ?';
                $params[] = now_iso();
            }
        }
        if (array_key_exists('hermes_draft', $b) && $mail['direction'] === 'in') {
            $sets[] = 'hermes_draft = ?';
            $params[] = s($b['hermes_draft'], 20000);
        }
        if ($mail['direction'] === 'out' && $mail['status'] !== 'sent') {
            foreach (['subject' => 300, 'body' => 100000, 'to_addr' => 190] as $f => $max) {
                if (array_key_exists($f, $b)) {
                    $v = s($b[$f], $max);
                    if ($f === 'to_addr' && !preg_match(EMAIL_RE, $v)) {
                        fail('Invalid recipient.', 422);
                    }
                    $sets[] = "$f = ?";
                    $params[] = $v;
                    // Editing an approved mail sends it back for re-approval.
                    if ($who === 'hermes' && $mail['status'] === 'approved') {
                        fail('Approved mail cannot be edited by Hermes.', 403);
                    }
                }
            }
        }
        if (!$sets) {
            fail('Nothing to update.', 422);
        }
        $sets[] = 'updated_at = ?';
        $params[] = now_iso();
        $params[] = $eid;
        q('UPDATE emails SET ' . implode(', ', $sets) . ' WHERE id = ?', $params);
        if ($mail['direction'] === 'out' && ($b['status'] ?? '') === 'sent' && $mail['reply_to_id']) {
            q("UPDATE emails SET status = 'replied', updated_at = ? WHERE id = ?", [now_iso(), $mail['reply_to_id']]);
        }
        log_activity($who, 'update', 'email', $eid, $mail['subject']);
        json_out(['success' => true, 'data' => ints(q('SELECT * FROM emails WHERE id = ?', [$eid])->fetch(), ['id', 'lead_id', 'reply_to_id'])]);
    }
    if ($method === 'DELETE') {
        q('DELETE FROM emails WHERE id = ?', [$eid]);
        log_activity($who, 'delete', 'email', $eid, $mail['subject']);
        json_out(['success' => true]);
    }
    fail('Method not allowed.', 405);
}

/* -------------------------------------------------------- vocabulary ---- */

function handle_vocabulary(string $method): void
{
    if ($method === 'GET') {
        $rows = q('SELECT id, data FROM vocabulary ORDER BY id DESC LIMIT 2000')->fetchAll();
        $words = [];
        foreach ($rows as $r) {
            $w = json_decode($r['data'], true);
            if (is_array($w)) {
                $w['id'] = 10000 + (int) $r['id'];
                $words[] = $w;
            }
        }
        json_out(['success' => true, 'count' => count($words), 'data' => $words]);
    }
    if ($method !== 'POST') {
        fail('Method not allowed.', 405);
    }
    $who = require_actor();
    $b = body();
    $word = s($b['french_word'] ?? '', 120);
    $en = s($b['meaning_en'] ?? '', 500);
    $hi = s($b['meaning_hi'] ?? '', 500);
    $use = s($b['use_case'] ?? '', 800);
    if ($word === '' || $en === '' || $hi === '' || $use === '') {
        fail('french_word, meaning_en, meaning_hi and use_case are required.', 422);
    }
    $level = strtoupper(s($b['cefr_level'] ?? '', 2));
    $level = in_array($level, CEFR, true) ? $level : 'B2';
    $example = s($b['example_sentence'] ?? '', 500) ?: $word;
    $ipa = s($b['ipa_phonetic'] ?? '', 120) ?: '/.../';
    $entry = [
        'word' => $word, 'french_word' => $word, 'ipa' => $ipa, 'ipa_phonetic' => $ipa,
        'part_of_speech' => s($b['part_of_speech'] ?? '', 60) ?: 'nom',
        'meaning' => $en, 'meaning_en' => $en, 'meaning_hi' => $hi,
        'level' => $level, 'cefr_level' => $level, 'category' => s($b['category'] ?? '', 80) ?: 'General',
        'example_fr' => $example, 'example_sentence' => $example, 'use_case' => $use,
        'notes' => s($b['notes'] ?? '', 500) ?: 'Ingested via Hermes agent', 'source' => 'Hermes', 'created_at' => now_iso(),
    ];
    $existing = q('SELECT id FROM vocabulary WHERE french_word = ?', [$word])->fetch();
    if ($existing) {
        q('UPDATE vocabulary SET data = ? WHERE id = ?', [json_encode($entry, JSON_UNESCAPED_UNICODE), $existing['id']]);
        $wid = (int) $existing['id'];
    } else {
        q('INSERT INTO vocabulary (french_word, data, created_at) VALUES (?,?,?)', [$word, json_encode($entry, JSON_UNESCAPED_UNICODE), now_iso()]);
        $wid = insert_id();
    }
    log_activity($who, $existing ? 'update' : 'create', 'vocabulary', $wid, $word);
    json_out(['success' => true, 'message' => $existing ? 'Word updated.' : 'New word added to the lexicon.', 'data' => $entry + ['id' => 10000 + $wid]]);
}

/* ----------------------------------------------------------- sitemap ---- */

function handle_sitemap(): void
{
    $base = site_url();
    $urls = [['/', null], ['/apply/', null], ['/library/', null], ['/books/', null], ['/dashboard/', null], ['/practice/', null], ['/blog/', null]];
    foreach (q('SELECT slug, updated_at FROM blog_posts WHERE ' . public_post_sql() . ' ORDER BY id DESC LIMIT 1000', [now_iso()])->fetchAll() as $r) {
        $urls[] = ['/blog/' . $r['slug'] . '/', $r['updated_at']];
    }
    header('Content-Type: application/xml; charset=utf-8');
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    foreach ($urls as [$loc, $mod]) {
        echo '  <url><loc>' . htmlspecialchars($base . $loc, ENT_XML1) . '</loc>' . ($mod ? '<lastmod>' . htmlspecialchars($mod, ENT_XML1) . '</lastmod>' : '') . "</url>\n";
    }
    echo '</urlset>';
    exit;
}

/* ------------------------------------------------------------- books ---- */

function handle_books(string $method, ?string $id): void
{
    $who = actor();
    if ($method === 'GET') {
        if ($id === null) {
            $rows = q('SELECT * FROM books ORDER BY id DESC')->fetchAll();
            json_out(['success' => true, 'total' => count($rows), 'data' => array_map('ints', $rows)]);
        }
        $book = is_numeric($id)
            ? q('SELECT * FROM books WHERE id = ?', [(int)$id])->fetch()
            : q('SELECT * FROM books WHERE slug = ?', [$id])->fetch();
        if (!$book) {
            fail('Book not found.', 404);
        }
        json_out(['success' => true, 'data' => ints($book)]);
    }

    require_actor();

    if ($method === 'POST') {
        $b = body();
        $title = s($b['title'] ?? '', 250);
        if ($title === '') {
            fail('Title is required.', 422);
        }
        $author = s($b['author'] ?? '', 160);
        $level = s($b['level'] ?? 'A1', 40);
        $category = s($b['category'] ?? 'Grammar', 80);
        $pages = (int)($b['pages'] ?? 0);
        $cover = s($b['cover'] ?? '', 500);
        $file_url = s($b['file_url'] ?? '', 500);
        $file_name = s($b['file_name'] ?? '', 250);
        $blurb = s($b['blurb'] ?? '', 4000);
        $blurb_hi = s($b['blurb_hi'] ?? '', 4000);

        $baseSlug = preg_replace('/[^a-z0-9]+/', '-', strtolower($title));
        $baseSlug = trim($baseSlug, '-') ?: 'book';
        $slug = s($b['slug'] ?? '', 190);
        if ($slug === '') {
            $slug = substr($baseSlug, 0, 40) . '-' . substr(bin2hex(random_bytes(4)), 0, 6);
        }

        $now = now_iso();
        q('INSERT INTO books (slug, title, author, level, pages, cover, file_url, file_name, category, blurb, blurb_hi, created_at, updated_at)
           VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)',
           [$slug, $title, $author, $level, $pages, $cover, $file_url, $file_name, $category, $blurb, $blurb_hi, $now, $now]);
        $bid = insert_id();
        log_activity($who, 'create', 'book', $bid, $title);
        $created = q('SELECT * FROM books WHERE id = ?', [$bid])->fetch();
        json_out(['success' => true, 'message' => 'Book uploaded successfully.', 'data' => ints($created)]);
    }

    if ($method === 'PATCH') {
        $book = is_numeric($id)
            ? q('SELECT * FROM books WHERE id = ?', [(int)$id])->fetch()
            : q('SELECT * FROM books WHERE slug = ?', [$id])->fetch();
        if (!$book) {
            fail('Book not found.', 404);
        }
        $bid = (int)$book['id'];
        $b = body();
        $sets = [];
        $params = [];
        foreach (['title' => 250, 'author' => 160, 'level' => 40, 'category' => 80, 'cover' => 500, 'file_url' => 500, 'file_name' => 250, 'blurb' => 4000, 'blurb_hi' => 4000] as $f => $max) {
            if (array_key_exists($f, $b)) {
                $sets[] = "$f = ?";
                $params[] = s($b[$f], $max);
            }
        }
        if (isset($b['pages'])) {
            $sets[] = "pages = ?";
            $params[] = (int)$b['pages'];
        }
        if (!$sets) {
            fail('Nothing to update.', 422);
        }
        $sets[] = 'updated_at = ?';
        $params[] = now_iso();
        $params[] = $bid;
        q('UPDATE books SET ' . implode(', ', $sets) . ' WHERE id = ?', $params);
        log_activity($who, 'update', 'book', $bid, $book['title']);
        json_out(['success' => true, 'data' => ints(q('SELECT * FROM books WHERE id = ?', [$bid])->fetch())]);
    }

    if ($method === 'DELETE') {
        $book = is_numeric($id)
            ? q('SELECT * FROM books WHERE id = ?', [(int)$id])->fetch()
            : q('SELECT * FROM books WHERE slug = ?', [$id])->fetch();
        if (!$book) {
            fail('Book not found.', 404);
        }
        $bid = (int)$book['id'];
        q('DELETE FROM books WHERE id = ?', [$bid]);
        log_activity($who, 'delete', 'book', $bid, $book['title']);
        json_out(['success' => true, 'message' => 'Book deleted.']);
    }

    fail('Method not allowed.', 405);
}
