<?php

/*
 | Natural French voices (Microsoft Edge "read aloud" neural voices) through the free `edge-tts` tool.
 | No API key and no cost. It is an unofficial endpoint, so if it ever stops working the site falls back
 | to the voices built into the visitor's browser.
 */
return [
    'enabled' => filter_var(env('TTS_ENABLED', true), FILTER_VALIDATE_BOOLEAN),

    // Path of the edge-tts command (a Python virtualenv on the server: /opt/edge-tts).
    'binary' => env('TTS_BINARY', '/opt/edge-tts/bin/edge-tts'),

    // Pre-generate audio in the background when new material is added through the API.
    'warm' => filter_var(env('TTS_WARM', true), FILTER_VALIDATE_BOOLEAN),

    'default_voice' => 'fr-CA-SylvieNeural',

    // Longest text sent in one request, and how many NEW audio files one learner may create per minute.
    'max_chars' => 900,
    'per_minute' => 60,

    // Where the audio files are cached (default public/audio, served by the web server as static files).
    'cache_path' => env('TTS_CACHE_PATH'),

    // Audio cache: oldest files are deleted when it grows beyond this many MB.
    'cache_max_mb' => 400,

    // Speeds the player may ask for.
    'rates' => ['+0%', '-25%', '-40%'],

    // key => label, accent, gender and the voice used for the second speaker in dialogues.
    'voices' => [
        'fr-CA-SylvieNeural' => ['label' => 'Sylvie', 'accent' => 'Canada', 'gender' => 'F', 'partner' => 'fr-CA-ThierryNeural'],
        'fr-CA-ThierryNeural' => ['label' => 'Thierry', 'accent' => 'Canada', 'gender' => 'M', 'partner' => 'fr-CA-SylvieNeural'],
        'fr-CA-AntoineNeural' => ['label' => 'Antoine', 'accent' => 'Canada', 'gender' => 'M', 'partner' => 'fr-CA-SylvieNeural'],
        'fr-CA-JeanNeural' => ['label' => 'Jean', 'accent' => 'Canada', 'gender' => 'M', 'partner' => 'fr-CA-SylvieNeural'],
        'fr-FR-DeniseNeural' => ['label' => 'Denise', 'accent' => 'France', 'gender' => 'F', 'partner' => 'fr-FR-HenriNeural'],
        'fr-FR-HenriNeural' => ['label' => 'Henri', 'accent' => 'France', 'gender' => 'M', 'partner' => 'fr-FR-DeniseNeural'],
        'fr-FR-EloiseNeural' => ['label' => 'Éloïse', 'accent' => 'France', 'gender' => 'F', 'partner' => 'fr-FR-HenriNeural'],
        'fr-FR-VivienneMultilingualNeural' => ['label' => 'Vivienne', 'accent' => 'France', 'gender' => 'F', 'partner' => 'fr-FR-RemyMultilingualNeural'],
        'fr-FR-RemyMultilingualNeural' => ['label' => 'Rémy', 'accent' => 'France', 'gender' => 'M', 'partner' => 'fr-FR-VivienneMultilingualNeural'],
        'fr-BE-CharlineNeural' => ['label' => 'Charline', 'accent' => 'Belgium', 'gender' => 'F', 'partner' => 'fr-BE-GerardNeural'],
        'fr-BE-GerardNeural' => ['label' => 'Gérard', 'accent' => 'Belgium', 'gender' => 'M', 'partner' => 'fr-BE-CharlineNeural'],
        'fr-CH-ArianeNeural' => ['label' => 'Ariane', 'accent' => 'Switzerland', 'gender' => 'F', 'partner' => 'fr-CH-FabriceNeural'],
        'fr-CH-FabriceNeural' => ['label' => 'Fabrice', 'accent' => 'Switzerland', 'gender' => 'M', 'partner' => 'fr-CH-ArianeNeural'],
    ],

    // "Mixed accents": each text is read by one of these, chosen from the text itself (same text = same voice, so the cache works).
    // The real TCF uses speakers with different accents, so this is good listening practice.
    'mix' => ['fr-CA-SylvieNeural', 'fr-FR-DeniseNeural', 'fr-CA-ThierryNeural', 'fr-FR-HenriNeural', 'fr-BE-CharlineNeural', 'fr-CH-ArianeNeural', 'fr-CA-AntoineNeural', 'fr-FR-RemyMultilingualNeural'],
];
