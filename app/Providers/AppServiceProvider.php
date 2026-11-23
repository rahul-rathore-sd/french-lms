<?php

namespace App\Providers;

use App\Support\EdgeTtsEngine;
use App\Support\TtsEngine;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // text-to-speech engine (the free edge-tts command); tests replace it with a fake
        $this->app->bind(TtsEngine::class, EdgeTtsEngine::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
