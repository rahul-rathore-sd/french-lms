<?php
declare(strict_types=1);
require __DIR__ . '/../api/lib.php';

function e($t): string
{
    return htmlspecialchars((string) $t, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function page(string $title, string $desc, string $canonical, string $inner, ?string $ogImage = null, string $extraHead = ''): never
{
    header('Content-Type: text/html; charset=utf-8');
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
        . '<title>' . e($title) . '</title><meta name="description" content="' . e($desc) . '">'
        . '<link rel="canonical" href="' . e($canonical) . '">'
        . '<meta property="og:title" content="' . e($title) . '"><meta property="og:description" content="' . e($desc) . '"><meta property="og:type" content="article">'
        . ($ogImage ? '<meta property="og:image" content="' . e($ogImage) . '">' : '') . $extraHead
        . '<style>
:root{--bg:#faf9f6;--surface:#fff;--line:#e4e0d7;--fg:#141826;--muted:#646a7a;--accent:#2c52d6}
@media(prefers-color-scheme:dark){:root{--bg:#090b12;--surface:#10131e;--line:#282e42;--fg:#edeff6;--muted:#969eb4;--accent:#6e8cff}}
*{box-sizing:border-box}body{margin:0;background:var(--bg);color:var(--fg);font:17px/1.7 system-ui,-apple-system,Segoe UI,Roboto,sans-serif}
a{color:var(--accent)}header{border-bottom:1px solid var(--line);background:var(--bg)}
.wrap{max-width:46rem;margin:0 auto;padding:0 20px}.bar{display:flex;justify-content:space-between;align-items:center;height:64px}
.logo{font:600 1.1rem Georgia,serif;color:var(--fg);text-decoration:none;display:flex;gap:10px;align-items:center}
.logo b{background:var(--accent);color:#fff;border-radius:10px;width:34px;height:34px;display:grid;place-items:center}
nav a{margin-left:18px;text-decoration:none;color:var(--muted);font-size:.95rem}
h1{font:500 clamp(2rem,5vw,2.8rem)/1.15 Georgia,serif;margin:2.2rem 0 .6rem}h2,h3{font-family:Georgia,serif;margin-top:2rem}
.meta{color:var(--muted);font-size:.9rem}.card{display:block;background:var(--surface);border:1px solid var(--line);border-radius:18px;padding:22px;margin:16px 0;text-decoration:none;color:inherit}
.card:hover{border-color:var(--accent)}.card h2{margin:.2rem 0 .4rem;font-size:1.4rem}.card p{margin:0;color:var(--muted)}
.tag{display:inline-block;font-size:.75rem;border:1px solid var(--line);border-radius:99px;padding:2px 10px;margin-right:6px;color:var(--muted)}
article img{max-width:100%;border-radius:14px}blockquote{margin:1.2rem 0;padding:.2rem 1rem;border-left:3px solid var(--accent);color:var(--muted)}
pre{background:var(--surface);border:1px solid var(--line);padding:14px;border-radius:12px;overflow:auto}code{font-size:.9em}
.cta{margin:3rem 0;background:var(--surface);border:1px solid var(--line);border-radius:20px;padding:26px;text-align:center}
.cta a{display:inline-block;background:var(--accent);color:#fff;border-radius:99px;padding:12px 26px;text-decoration:none;margin-top:10px}
footer{border-top:1px solid var(--line);margin-top:4rem;padding:24px 0;color:var(--muted);font-size:.9rem}</style></head><body>'
        . '<header><div class="wrap bar"><a class="logo" href="/"><b>É</b>Académie de Français</a><nav><a href="/dashboard/">Learn</a><a href="/library/">Library</a><a href="/blog/">Blog</a><a href="/apply/">Contact</a></nav></div></header>'
        . '<main class="wrap">' . $inner . '</main><footer><div class="wrap">© ' . date('Y') . ' Académie de Français</div></footer></body></html>';
    exit;
}

$slug = isset($_GET['slug']) ? (string) $_GET['slug'] : '';

try {
    $base = site_url();
    if ($slug === '') {
        $rows = q('SELECT * FROM blog_posts WHERE ' . public_post_sql() . ' ORDER BY COALESCE(publish_at, created_at) DESC LIMIT 50', [now_iso()])->fetchAll();
        $html = '<h1>The French learning blog</h1><p class="meta">Tips, grammar and exam guidance for learners of French.</p>';
        if (!$rows) {
            $html .= '<p>New articles are coming soon.</p>';
        }
        foreach ($rows as $r) {
            $tags = '';
            foreach (array_filter(array_map('trim', explode(',', (string) $r['tags']))) as $t) {
                $tags .= '<span class="tag">' . e($t) . '</span>';
            }
            $html .= '<a class="card" href="/blog/' . e($r['slug']) . '/"><div class="meta">' . e(substr((string) ($r['publish_at'] ?? $r['created_at']), 0, 10)) . '</div><h2>'
                . e($r['title']) . '</h2><p>' . e($r['excerpt']) . '</p><div style="margin-top:10px">' . $tags . '</div></a>';
        }
        page('Blog · Académie de Français', 'Tips, grammar and exam guidance for learners of French.', $base . '/blog/', $html);
    }

    $row = q('SELECT * FROM blog_posts WHERE slug = ?', [$slug])->fetch();
    if (!$row || !is_public_post($row)) {
        http_response_code(404);
        page('Not found', 'Post not found', $base . '/blog/', '<h1>Post not found</h1><p><a href="/blog/">← Back to the blog</a></p>');
    }
    $desc = $row['meta_description'] ?: $row['excerpt'];
    $date = substr((string) ($row['publish_at'] ?? $row['created_at']), 0, 10);
    $ld = '<script type="application/ld+json">' . json_encode([
        '@context' => 'https://schema.org', '@type' => 'BlogPosting', 'headline' => $row['title'], 'description' => $desc,
        'datePublished' => $row['publish_at'] ?? $row['created_at'], 'dateModified' => $row['updated_at'],
        'author' => ['@type' => 'Organization', 'name' => $row['author']], 'mainEntityOfPage' => $base . '/blog/' . $row['slug'] . '/',
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) . '</script>';
    $cover = $row['cover_image'] && preg_match('#^(https?://|/)#', $row['cover_image']) ? $row['cover_image'] : null;
    $inner = '<p class="meta" style="margin-top:2rem"><a href="/blog/">← Blog</a> · ' . e($date) . ' · ' . e($row['author']) . '</p><article><h1>'
        . e($row['title']) . '</h1>' . ($cover ? '<img src="' . e($cover) . '" alt="">' : '')
        . md_to_html((string) $row['content_md']) . '</article>'
        . '<div class="cta"><strong>Ready to speak French with confidence?</strong><br><span class="meta">Talk to us for a free consultation.</span><br><a href="/apply/">Get in touch</a></div>';
    page($row['title'] . ' · Académie de Français', (string) $desc, $base . '/blog/' . $row['slug'] . '/', $inner, $cover, $ld);
} catch (Throwable $ex) {
    error_log('Blog error: ' . $ex->getMessage());
    http_response_code(500);
    page('Error', 'Error', '/blog/', '<h1>Something went wrong</h1><p>Please try again later.</p>');
}
