<?php

return [
    // Public URL used in emails, the sitemap and blog links.
    'site_url' => rtrim((string) env('SITE_URL', env('APP_URL', 'http://localhost')), '/'),

    // true (default): keep the site out of Google (X-Robots-Tag noindex, no sitemap). Set FRENCH_NOINDEX=false to go public.
    'noindex' => filter_var(env('FRENCH_NOINDEX', true), FILTER_VALIDATE_BOOLEAN),

    // Where new-lead alerts are mailed ("" disables them).
    'notify_email' => (string) env('NOTIFY_EMAIL', ''),

    // bcrypt hash for the /admin dashboard password. Create one with:  php artisan french:admin-password
    'admin_password_hash' => (string) env('ADMIN_PASSWORD_HASH', ''),

    // Bearer token for the Hermes agent (POST /api/v1/vocabulary, blog, ...). Min. 24 chars.
    'hermes_api_key' => (string) env('HERMES_API_KEY', ''),
];
