<?php
// build-deploy.py writes config.php from this shape, with real secrets filled in.
return [
    'db_driver' => 'mysql',          // 'mysql' (shared hosting) or 'sqlite' (local testing)
    'db_host' => 'localhost',
    'db_name' => 'CHANGE_ME',
    'db_user' => 'CHANGE_ME',
    'db_pass' => 'CHANGE_ME',
    'site_url' => 'https://yourdomain.com',
    'notify_email' => '',            // new-lead alerts go here
    'admin_password_hash' => '',
    'hermes_api_key' => '',
];
