<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * The project is now a personal TCF Canada study site. The old academy-business tables
 * (leads, clients, blog, social posts, emails, books) are no longer used.
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (['leads', 'clients', 'blog_posts', 'social_posts', 'emails', 'books'] as $table) {
            Schema::dropIfExists($table);
        }
    }

    public function down(): void
    {
        // not restored: the data was never used by the new site
    }
};
