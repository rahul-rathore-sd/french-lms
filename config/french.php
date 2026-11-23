<?php

return [
    // Public URL of the site (used in links returned by the API).
    'site_url' => rtrim((string) env('SITE_URL', env('APP_URL', 'http://localhost')), '/'),

    // true (default): keep the site out of Google (X-Robots-Tag noindex). Set FRENCH_NOINDEX=false to go public.
    'noindex' => filter_var(env('FRENCH_NOINDEX', true), FILTER_VALIDATE_BOOLEAN),

    // Bearer token for the daily automation / Hermes agent. Min. 24 chars. Create with: php artisan french:keys
    'hermes_api_key' => (string) env('HERMES_API_KEY', ''),
];
