<?php
declare(strict_types=1);

/**
 * Shared helpers for the Hermes site API (PHP 8+, MySQL or SQLite via PDO).
 * Works on ordinary shared hosting: no Composer, no extensions beyond pdo_mysql.
 */

const SCHEMA_VERSION = '3';

function cfg(string $key, mixed $default = null): mixed
{
    static $config = null;
    if ($config === null) {
        $file = __DIR__ . '/config.php';
        $config = is_file($file) ? (require $file) : [];
    }
    return $config[$key] ?? $default;
}

function now_iso(): string
{
    return gmdate('Y-m-d\TH:i:s\Z');
}

/* ------------------------------------------------------------------ DB --- */

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }
    $driver = cfg('db_driver', 'mysql');
    if ($driver === 'sqlite') {
        $path = cfg('sqlite_path', __DIR__ . '/../data/hermes.sqlite');
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0750, true);
        }
        $pdo = new PDO('sqlite:' . $path);
        $pdo->exec('PRAGMA journal_mode=WAL');
    } else {
        $dsn = 'mysql:host=' . cfg('db_host', 'localhost') . ';dbname=' . cfg('db_name', '') . ';charset=utf8mb4';
        $pdo = new PDO($dsn, (string) cfg('db_user', ''), (string) cfg('db_pass', ''));
    }
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    migrate($pdo, $driver);
    return $pdo;
}

function migrate(PDO $pdo, string $driver): void
{
    try {
        $row = $pdo->query("SELECT v FROM meta WHERE k = 'schema'")->fetch();
        if ($row && $row['v'] === SCHEMA_VERSION) {
            return;
        }
    } catch (Throwable $e) {
        // meta table missing: first run
    }

    $id = $driver === 'sqlite' ? 'INTEGER PRIMARY KEY AUTOINCREMENT' : 'INT AUTO_INCREMENT PRIMARY KEY';
    $long = $driver === 'sqlite' ? 'TEXT' : 'MEDIUMTEXT';
    $tail = $driver === 'sqlite' ? '' : ' ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';

    $tables = [
        "CREATE TABLE IF NOT EXISTS meta (k VARCHAR(40) PRIMARY KEY, v VARCHAR(100) NOT NULL)$tail",
        "CREATE TABLE IF NOT EXISTS leads (
            id $id,
            full_name VARCHAR(160) NOT NULL,
            email VARCHAR(190) NOT NULL,
            phone VARCHAR(40) NOT NULL DEFAULT '',
            target_cefr VARCHAR(4) NOT NULL DEFAULT 'B1',
            background VARCHAR(300) NOT NULL DEFAULT '',
            message $long NOT NULL,
            source VARCHAR(120) NOT NULL DEFAULT 'website',
            status VARCHAR(20) NOT NULL DEFAULT 'new',
            notes $long NULL,
            hermes_reply $long NULL,
            created_at VARCHAR(25) NOT NULL,
            updated_at VARCHAR(25) NOT NULL,
            UNIQUE (email)
        )$tail",
        "CREATE TABLE IF NOT EXISTS clients (
            id $id,
            lead_id INT NULL,
            full_name VARCHAR(160) NOT NULL,
            email VARCHAR(190) NOT NULL,
            phone VARCHAR(40) NOT NULL DEFAULT '',
            level VARCHAR(4) NOT NULL DEFAULT 'A1',
            plan VARCHAR(80) NOT NULL DEFAULT '',
            status VARCHAR(20) NOT NULL DEFAULT 'active',
            notes $long NULL,
            started_at VARCHAR(25) NOT NULL,
            created_at VARCHAR(25) NOT NULL,
            updated_at VARCHAR(25) NOT NULL,
            UNIQUE (email)
        )$tail",
        "CREATE TABLE IF NOT EXISTS blog_posts (
            id $id,
            slug VARCHAR(190) NOT NULL,
            title VARCHAR(250) NOT NULL,
            excerpt VARCHAR(500) NOT NULL DEFAULT '',
            content_md $long NOT NULL,
            tags VARCHAR(300) NOT NULL DEFAULT '',
            cover_image VARCHAR(500) NOT NULL DEFAULT '',
            status VARCHAR(20) NOT NULL DEFAULT 'draft',
            publish_at VARCHAR(25) NULL,
            author VARCHAR(80) NOT NULL DEFAULT 'Académie de Français',
            meta_description VARCHAR(300) NOT NULL DEFAULT '',
            created_by VARCHAR(20) NOT NULL DEFAULT 'admin',
            created_at VARCHAR(25) NOT NULL,
            updated_at VARCHAR(25) NOT NULL,
            UNIQUE (slug)
        )$tail",
        "CREATE TABLE IF NOT EXISTS social_posts (
            id $id,
            platform VARCHAR(20) NOT NULL,
            content $long NOT NULL,
            link VARCHAR(500) NOT NULL DEFAULT '',
            media_url VARCHAR(500) NOT NULL DEFAULT '',
            status VARCHAR(20) NOT NULL DEFAULT 'draft',
            scheduled_at VARCHAR(25) NULL,
            posted_at VARCHAR(25) NULL,
            post_url VARCHAR(500) NOT NULL DEFAULT '',
            blog_id INT NULL,
            created_by VARCHAR(20) NOT NULL DEFAULT 'admin',
            created_at VARCHAR(25) NOT NULL,
            updated_at VARCHAR(25) NOT NULL
        )$tail",
        "CREATE TABLE IF NOT EXISTS emails (
            id $id,
            direction VARCHAR(3) NOT NULL,
            message_id VARCHAR(190) NULL,
            in_reply_to VARCHAR(190) NOT NULL DEFAULT '',
            from_addr VARCHAR(190) NOT NULL DEFAULT '',
            to_addr VARCHAR(190) NOT NULL DEFAULT '',
            subject VARCHAR(300) NOT NULL DEFAULT '',
            body $long NOT NULL,
            status VARCHAR(20) NOT NULL,
            hermes_draft $long NULL,
            lead_id INT NULL,
            reply_to_id INT NULL,
            received_at VARCHAR(25) NULL,
            sent_at VARCHAR(25) NULL,
            created_by VARCHAR(20) NOT NULL DEFAULT 'admin',
            created_at VARCHAR(25) NOT NULL,
            updated_at VARCHAR(25) NOT NULL,
            UNIQUE (message_id)
        )$tail",
        "CREATE TABLE IF NOT EXISTS vocabulary (
            id $id,
            french_word VARCHAR(190) NOT NULL,
            data $long NOT NULL,
            created_at VARCHAR(25) NOT NULL,
            UNIQUE (french_word)
        )$tail",
        "CREATE TABLE IF NOT EXISTS activity (
            id $id,
            actor VARCHAR(20) NOT NULL,
            action VARCHAR(40) NOT NULL,
            entity VARCHAR(30) NOT NULL,
            entity_id INT NULL,
            detail VARCHAR(500) NOT NULL DEFAULT '',
            created_at VARCHAR(25) NOT NULL
        )$tail",
        "CREATE TABLE IF NOT EXISTS throttle (
            id $id,
            bucket VARCHAR(40) NOT NULL,
            ip VARCHAR(64) NOT NULL,
            ts INT NOT NULL
        )$tail",
        "CREATE TABLE IF NOT EXISTS books (
            id $id,
            slug VARCHAR(190) NOT NULL,
            title VARCHAR(250) NOT NULL,
            author VARCHAR(160) NOT NULL DEFAULT '',
            level VARCHAR(40) NOT NULL DEFAULT 'All',
            pages INT NOT NULL DEFAULT 0,
            cover VARCHAR(500) NOT NULL DEFAULT '',
            file_url VARCHAR(500) NOT NULL DEFAULT '',
            file_name VARCHAR(250) NOT NULL DEFAULT '',
            category VARCHAR(80) NOT NULL DEFAULT 'Grammar',
            blurb $long NOT NULL,
            blurb_hi $long NOT NULL DEFAULT '',
            created_at VARCHAR(25) NOT NULL,
            updated_at VARCHAR(25) NOT NULL,
            UNIQUE (slug)
        )$tail",
    ];
    foreach ($tables as $sql) {
        $pdo->exec($sql);
    }
    // Seed default books if table is empty
    try {
        $cnt = (int) $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn();
        if ($cnt === 0) {
            $now = now_iso();
            $seed1 = [
                'grammaire-progressive-du-fran-ais-a2-b1-',
                'Grammaire progressive du français – Intermédiaire (A2/B1)',
                'Maïa Grégoire, Odile Thiévenaz (CLE International)',
                'A2–B1',
                282,
                '/books/grammaire-progressive-du-fran-ais-a2-b1-.jpg',
                '/books/Grammaire%20progressive%20du%20fran%C3%A7ais%20A2-B1%20Niveau%20interm%C3%A9diaire.pdf',
                'Grammaire progressive du français A2-B1 Niveau intermédiaire.pdf',
                'Grammar',
                '52 short units with rules, exercises and tests, from être to the subjunctive. Our grammar topics follow its order.',
                '52 छोटे यूनिट: नियम, अभ्यास और टेस्ट — être से subjonctif तक। हमारे grammar topics इसी क्रम पर हैं।',
                $now, $now
            ];
            $seed2 = [
                'pdfcoffee-com-english-grammar-for-studen',
                'English Grammar for Students of French',
                'Jacqueline Morton (Olivia and Hill Press)',
                'A1–B2',
                191,
                '/books/pdfcoffee-com-english-grammar-for-studen.jpg',
                '/books/pdfcoffee.com_english-grammar-for-students-of-french-1993-morton-pdf-free.pdf',
                'pdfcoffee.com_english-grammar-for-students-of-french-1993-morton-pdf-free.pdf',
                'Grammar',
                'Explains grammar terms (gender, tense, mood, pronouns) in English first, then shows how French works. Ideal if the terminology confuses you.',
                'पहले अंग्रेज़ी में grammar के शब्द समझाती है, फिर फ़्रेंच से तुलना करती है।',
                $now, $now
            ];
            $ins = $pdo->prepare("INSERT OR IGNORE INTO books (slug, title, author, level, pages, cover, file_url, file_name, category, blurb, blurb_hi, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
            if ($driver === 'mysql') {
                $ins = $pdo->prepare("INSERT IGNORE INTO books (slug, title, author, level, pages, cover, file_url, file_name, category, blurb, blurb_hi, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
            }
            $ins->execute($seed1);
            $ins->execute($seed2);
        }
    } catch (Throwable $e) {
        // ignore seed error
    }
    $stmt = $pdo->prepare($driver === 'sqlite'
        ? "INSERT OR REPLACE INTO meta (k, v) VALUES ('schema', ?)"
        : "REPLACE INTO meta (k, v) VALUES ('schema', ?)");
    $stmt->execute([SCHEMA_VERSION]);
}

/** Runs a prepared statement and returns it. */
function q(string $sql, array $params = []): PDOStatement
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

function insert_id(): int
{
    return (int) db()->lastInsertId();
}

/* --------------------------------------------------------------- HTTP --- */

function json_out(mixed $data, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    header('X-Content-Type-Options: nosniff');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

function fail(string $message, int $status = 400): never
{
    json_out(['success' => false, 'error' => $message], $status);
}

function body(): array
{
    static $parsed = null;
    if ($parsed !== null) {
        return $parsed;
    }
    $raw = file_get_contents('php://input', false, null, 0, 1_500_000);
    if ($raw === false || $raw === '') {
        return $parsed = [];
    }
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        fail('Invalid JSON body.', 400);
    }
    return $parsed = $data;
}

/** Trimmed, length-limited string from untrusted input. */
function s(mixed $value, int $max): string
{
    if (!is_string($value) && !is_numeric($value)) {
        return '';
    }
    $value = trim((string) $value);
    return function_exists('mb_substr') ? mb_substr($value, 0, $max, 'UTF-8') : substr($value, 0, $max);
}

function client_ip(): string
{
    return s($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0', 64);
}

function https_on(): bool
{
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
}

function site_url(): string
{
    $url = (string) cfg('site_url', '');
    if ($url !== '') {
        return rtrim($url, '/');
    }
    return (https_on() ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
}

/** Allows `limit` hits per `window` seconds for this IP and bucket. */
function throttle(string $bucket, int $limit, int $window): void
{
    $ip = client_ip();
    $since = time() - $window;
    q('DELETE FROM throttle WHERE ts < ?', [time() - 86400]);
    $count = (int) q('SELECT COUNT(*) AS c FROM throttle WHERE bucket = ? AND ip = ? AND ts > ?', [$bucket, $ip, $since])->fetch()['c'];
    if ($count >= $limit) {
        fail('Too many requests. Please try again later.', 429);
    }
    q('INSERT INTO throttle (bucket, ip, ts) VALUES (?, ?, ?)', [$bucket, $ip, time()]);
}

/* --------------------------------------------------------------- Auth --- */

function bearer_token(): string
{
    $h = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
    if ($h === '' && function_exists('getallheaders')) {
        foreach (getallheaders() as $k => $v) {
            if (strtolower($k) === 'authorization') {
                $h = $v;
            }
        }
    }
    if (stripos($h, 'Bearer ') === 0) {
        return trim(substr($h, 7));
    }
    // Some shared hosts strip Authorization; accept a dedicated header too.
    return trim($_SERVER['HTTP_X_HERMES_KEY'] ?? '');
}

function start_admin_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    session_name('hs_admin');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => https_on(),
        'httponly' => true,
        'samesite' => 'Strict',
    ]);
    session_start();
}

/** Returns 'hermes' (Bearer key) or 'admin' (browser session), or null. */
function actor(): ?string
{
    $key = (string) cfg('hermes_api_key', '');
    $token = bearer_token();
    if ($token !== '') {
        return strlen($key) >= 24 && hash_equals($key, $token) ? 'hermes' : null;
    }
    if (isset($_COOKIE['hs_admin'])) {
        start_admin_session();
        if (!empty($_SESSION['admin'])) {
            return 'admin';
        }
    }
    return null;
}

function require_actor(): string
{
    $who = actor();
    if ($who === null) {
        fail('Unauthorized.', 401);
    }
    if ($who === 'admin' && !in_array($_SERVER['REQUEST_METHOD'], ['GET', 'HEAD'], true)) {
        // CSRF defence on top of SameSite=Strict: custom header + same-origin Origin.
        if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== 'fetch') {
            fail('Bad request.', 403);
        }
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        $host = preg_replace('/:\d+$/', '', (string) ($_SERVER['HTTP_HOST'] ?? ''));
        if ($origin !== '' && parse_url($origin, PHP_URL_HOST) !== $host) {
            fail('Bad origin.', 403);
        }
    }
    return $who;
}

function log_activity(string $actor, string $action, string $entity, ?int $entityId, string $detail = ''): void
{
    q('INSERT INTO activity (actor, action, entity, entity_id, detail, created_at) VALUES (?,?,?,?,?,?)',
        [$actor, $action, $entity, $entityId, s($detail, 500), now_iso()]);
}

/* --------------------------------------------------------------- Misc ---- */

function slugify(string $text): string
{
    $text = function_exists('iconv') ? (string) @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text) : $text;
    $text = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $text) ?? '');
    $text = trim($text, '-');
    return $text !== '' ? substr($text, 0, 120) : 'post';
}

function mail_safe(string $v): string
{
    return trim(preg_replace('/[\r\n]+/', ' ', $v) ?? '');
}

function notify_admin(string $subject, string $text): void
{
    $to = (string) cfg('notify_email', '');
    if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return;
    }
    $host = preg_replace('/^www\./', '', (string) parse_url(site_url(), PHP_URL_HOST));
    $headers = "From: Hermes <noreply@{$host}>\r\nContent-Type: text/plain; charset=utf-8";
    @mail($to, '=?UTF-8?B?' . base64_encode(mail_safe($subject)) . '?=', $text, $headers);
}

function is_public_post(array $row): bool
{
    if ($row['status'] === 'published') {
        return true;
    }
    return $row['status'] === 'scheduled' && !empty($row['publish_at']) && $row['publish_at'] <= now_iso();
}

/** SQL fragment selecting posts visible to the public. */
function public_post_sql(): string
{
    return "(status = 'published' OR (status = 'scheduled' AND publish_at IS NOT NULL AND publish_at <= ?))";
}

/** Minimal, safe Markdown to HTML (input is escaped first, so raw HTML never passes). */
function md_to_html(string $md): string
{
    $md = str_replace(["\r\n", "\r"], "\n", $md);
    $esc = static fn(string $t): string => htmlspecialchars($t, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

    $inline = static function (string $t) use ($esc): string {
        $t = $esc($t);
        $t = preg_replace_callback('/`([^`]+)`/', fn($m) => '<code>' . $m[1] . '</code>', $t) ?? $t;
        $t = preg_replace('/\*\*([^*]+)\*\*/', '<strong>$1</strong>', $t) ?? $t;
        $t = preg_replace('/(?<![\w*])\*([^*\n]+)\*(?![\w*])/', '<em>$1</em>', $t) ?? $t;
        $t = preg_replace_callback('/!\[([^\]]*)\]\((https?:\/\/[^\s)]+|\/[^\s)]*)\)/',
            fn($m) => '<img src="' . $m[2] . '" alt="' . $m[1] . '" loading="lazy">', $t) ?? $t;
        $t = preg_replace_callback('/\[([^\]]+)\]\((https?:\/\/[^\s)]+|\/[^\s)]*|mailto:[^\s)]+)\)/',
            fn($m) => '<a href="' . $m[2] . '" rel="noopener' . (str_starts_with($m[2], '/') ? '' : ' nofollow') . '">' . $m[1] . '</a>', $t) ?? $t;
        return $t;
    };

    $lines = explode("\n", $md);
    $out = [];
    $para = [];
    $list = null; // 'ul' | 'ol'
    $inCode = false;
    $code = [];

    $flushPara = function () use (&$para, &$out, $inline) {
        if ($para) {
            $out[] = '<p>' . $inline(implode(' ', $para)) . '</p>';
            $para = [];
        }
    };
    $closeList = function () use (&$list, &$out) {
        if ($list) {
            $out[] = "</$list>";
            $list = null;
        }
    };

    foreach ($lines as $line) {
        if (preg_match('/^```/', $line)) {
            if ($inCode) {
                $out[] = '<pre><code>' . $esc(implode("\n", $code)) . '</code></pre>';
                $code = [];
                $inCode = false;
            } else {
                $flushPara();
                $closeList();
                $inCode = true;
            }
            continue;
        }
        if ($inCode) {
            $code[] = $line;
            continue;
        }
        if (trim($line) === '') {
            $flushPara();
            $closeList();
            continue;
        }
        if (preg_match('/^(#{1,4})\s+(.+)$/', $line, $m)) {
            $flushPara();
            $closeList();
            $level = min(4, strlen($m[1]) + 1); // page title is the h1
            $out[] = "<h$level>" . $inline($m[2]) . "</h$level>";
            continue;
        }
        if (preg_match('/^(-{3,}|\*{3,})$/', trim($line))) {
            $flushPara();
            $closeList();
            $out[] = '<hr>';
            continue;
        }
        if (preg_match('/^>\s?(.*)$/', $line, $m)) {
            $flushPara();
            $closeList();
            $out[] = '<blockquote>' . $inline($m[1]) . '</blockquote>';
            continue;
        }
        if (preg_match('/^\s*[-*+]\s+(.+)$/', $line, $m)) {
            $flushPara();
            if ($list !== 'ul') {
                $closeList();
                $out[] = '<ul>';
                $list = 'ul';
            }
            $out[] = '<li>' . $inline($m[1]) . '</li>';
            continue;
        }
        if (preg_match('/^\s*\d+[.)]\s+(.+)$/', $line, $m)) {
            $flushPara();
            if ($list !== 'ol') {
                $closeList();
                $out[] = '<ol>';
                $list = 'ol';
            }
            $out[] = '<li>' . $inline($m[1]) . '</li>';
            continue;
        }
        $closeList();
        $para[] = trim($line);
    }
    if ($inCode) {
        $out[] = '<pre><code>' . $esc(implode("\n", $code)) . '</code></pre>';
    }
    $flushPara();
    $closeList();
    return implode("\n", $out);
}
