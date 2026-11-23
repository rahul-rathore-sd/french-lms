@extends('blog.layout')

@section('content')
<h1>The French learning blog</h1>
<p class="meta">Tips, grammar and exam guidance for learners of French.</p>
@forelse ($posts as $p)
<a class="card" href="/blog/{{ $p->slug }}/">
    <div class="meta">{{ substr((string) ($p->publish_at ?? $p->created_at), 0, 10) }}</div>
    <h2>{{ $p->title }}</h2>
    <p>{{ $p->excerpt }}</p>
    <div style="margin-top:10px">
        @foreach (array_filter(array_map('trim', explode(',', (string) $p->tags))) as $t)
            <span class="tag">{{ $t }}</span>
        @endforeach
    </div>
</a>
@empty
<p>New articles are coming soon.</p>
@endforelse
@endsection
