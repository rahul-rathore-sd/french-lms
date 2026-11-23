<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Support\Api;
use Illuminate\Contracts\View\View;

/** Server-rendered public blog (good for SEO): /blog/ and /blog/{slug}/. */
class BlogPageController extends Controller
{
    public function index(): View
    {
        $posts = Api::wherePublic(BlogPost::query())
            ->orderByRaw('COALESCE(publish_at, created_at) DESC')->limit(50)->get();

        return view('blog.index', [
            'posts' => $posts,
            'title' => 'Blog · Académie de Français',
            'desc' => 'Tips, grammar and exam guidance for learners of French.',
            'canonical' => Api::siteUrl().'/blog/',
        ]);
    }

    public function show(string $slug): View|\Illuminate\Http\Response
    {
        $post = BlogPost::where('slug', $slug)->first();
        if (! $post || ! Api::isPublicPost($post->toArray())) {
            return response(view('blog.missing', [
                'title' => 'Not found', 'desc' => 'Post not found', 'canonical' => Api::siteUrl().'/blog/',
            ]), 404);
        }

        $base = Api::siteUrl();
        $desc = $post->meta_description ?: $post->excerpt;
        $published = $post->publish_at ?? $post->created_at;
        $cover = $post->cover_image && preg_match('#^(https?://|/)#', $post->cover_image) ? $post->cover_image : null;
        $ld = '<script type="application/ld+json">'.json_encode([
            '@context' => 'https://schema.org', '@type' => 'BlogPosting', 'headline' => $post->title, 'description' => $desc,
            'datePublished' => $published, 'dateModified' => $post->updated_at,
            'author' => ['@type' => 'Organization', 'name' => $post->author],
            'mainEntityOfPage' => $base.'/blog/'.$post->slug.'/',
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG).'</script>';

        return view('blog.show', [
            'post' => $post,
            'html' => Api::mdToHtml((string) $post->content_md),
            'date' => substr((string) $published, 0, 10),
            'cover' => $cover,
            'title' => $post->title.' · Académie de Français',
            'desc' => (string) $desc,
            'canonical' => $base.'/blog/'.$post->slug.'/',
            'ogImage' => $cover,
            'extraHead' => $ld,
        ]);
    }
}
