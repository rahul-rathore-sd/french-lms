<?php

use App\Http\Controllers\BlogPageController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\StaticPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StaticPageController::class, 'home']);
Route::get('sitemap.xml', SitemapController::class);

// Server-rendered blog (SEO)
Route::get('blog', [BlogPageController::class, 'index']);
Route::get('blog/{slug}', [BlogPageController::class, 'show'])->where('slug', '[A-Za-z0-9-]+');

// The original site has no separate sign-in pages: send them to the contact/apply form
Route::redirect('sign-in', '/apply/');
Route::redirect('sign-up', '/apply/');
Route::redirect('login', '/apply/');
Route::redirect('register', '/apply/');

// Everything else: the pre-built frontend in public/ (or a real 404)
Route::fallback([StaticPageController::class, 'show']);
