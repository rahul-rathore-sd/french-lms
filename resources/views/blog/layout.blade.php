<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>{{ $title }}</title>
<meta name="description" content="{{ $desc }}">
@if (config('french.noindex'))<meta name="robots" content="noindex, nofollow, noarchive">@endif
<link rel="canonical" href="{{ $canonical }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $desc }}">
<meta property="og:type" content="article">
@isset($ogImage)<meta property="og:image" content="{{ $ogImage }}">@endisset
{!! $extraHead ?? '' !!}
<style>
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
footer{border-top:1px solid var(--line);margin-top:4rem;padding:24px 0;color:var(--muted);font-size:.9rem}
</style>
</head>
<body>
<header><div class="wrap bar"><a class="logo" href="/"><b>É</b>Académie de Français</a><nav><a href="/dashboard/">Learn</a><a href="/library/">Library</a><a href="/blog/">Blog</a><a href="/apply/">Contact</a></nav></div></header>
<main class="wrap">
@yield('content')
</main>
<footer><div class="wrap">© {{ date('Y') }} Académie de Français</div></footer>
</body>
</html>
