<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Serves the pre-built frontend (static HTML exported into public/) for URLs like /apply/ or
 * /library/. Nginx normally serves these files directly; this is the safe fallback, and it
 * returns a real 404 (with the site's 404.html) for unknown URLs.
 */
class StaticPageController extends Controller
{
    public function home(): BinaryFileResponse
    {
        return $this->page('index.html');
    }

    public function show(Request $request): BinaryFileResponse
    {
        $path = trim($request->path(), '/');
        // only plain path segments: no dots-dots, no hidden files
        if ($path !== '' && ! preg_match('#^[A-Za-z0-9_\-/]+$#', $path)) {
            return $this->notFound();
        }
        foreach (["$path/index.html", "$path.html"] as $candidate) {
            if (is_file(public_path($candidate))) {
                return $this->page($candidate);
            }
        }

        return $this->notFound();
    }

    private function page(string $file): BinaryFileResponse
    {
        return response()->file(public_path($file), ['Content-Type' => 'text/html; charset=utf-8']);
    }

    private function notFound(): BinaryFileResponse
    {
        $file = is_file(public_path('404.html')) ? '404.html' : 'index.html';

        return response()->file(public_path($file), ['Content-Type' => 'text/html; charset=utf-8'])->setStatusCode(404);
    }
}
