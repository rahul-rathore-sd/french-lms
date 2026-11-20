<?php
// Local testing only: php -S localhost:8080 -t dist/public_html server/dev-router.php
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$root = $_SERVER['DOCUMENT_ROOT'];
if ($uri === '/sitemap.xml') { $_GET['r'] = 'sitemap'; require $root . '/api/index.php'; return true; }
if (preg_match('#^/api(/|$)#', $uri)) { $_SERVER['SCRIPT_NAME'] = '/api/index.php'; require $root . '/api/index.php'; return true; }
if (preg_match('#^/blog/?$#', $uri)) { require $root . '/blog/index.php'; return true; }
if (preg_match('#^/blog/([A-Za-z0-9-]+)/?$#', $uri, $m)) { $_GET['slug'] = $m[1]; require $root . '/blog/index.php'; return true; }
if (is_file($root . $uri)) return false;
if (is_file($root . rtrim($uri, '/') . '/index.html')) { readfile($root . rtrim($uri, '/') . '/index.html'); return true; }
http_response_code(404); echo is_file($root . '/404.html') ? file_get_contents($root . '/404.html') : 'Not found';
