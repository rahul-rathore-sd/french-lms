@extends('blog.layout')

@section('content')
<p class="meta" style="margin-top:2rem"><a href="/blog/">← Blog</a> · {{ $date }} · {{ $post->author }}</p>
<article>
    <h1>{{ $post->title }}</h1>
    @if ($cover)<img src="{{ $cover }}" alt="">@endif
    {{-- md_to_html() escapes all input first, so this is safe markup --}}
    {!! $html !!}
</article>
<div class="cta">
    <strong>Ready to speak French with confidence?</strong><br>
    <span class="meta">Talk to us for a free consultation.</span><br>
    <a href="/apply/">Get in touch</a>
</div>
@endsection
