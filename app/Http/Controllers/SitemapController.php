<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Support\Api;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $base = Api::siteUrl();
        $urls = [['/', null], ['/apply/', null], ['/library/', null], ['/books/', null], ['/dashboard/', null], ['/practice/', null], ['/blog/', null]];
        foreach (Api::wherePublic(BlogPost::query())->orderByDesc('id')->limit(1000)->get(['slug', 'updated_at']) as $r) {
            $urls[] = ['/blog/'.$r->slug.'/', $r->updated_at];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n".'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
        foreach ($urls as [$loc, $mod]) {
            $xml .= '  <url><loc>'.htmlspecialchars($base.$loc, ENT_XML1).'</loc>'
                .($mod ? '<lastmod>'.htmlspecialchars($mod, ENT_XML1).'</lastmod>' : '')."</url>\n";
        }
        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=utf-8']);
    }
}
