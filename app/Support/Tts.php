<?php

namespace App\Support;

/**
 * Cached text-to-speech. Same (voice, speed, text) always maps to the same file in public/audio, so every
 * sentence is generated once and then served as a plain static file. Dialogues ("Name : line" per line) are
 * split per speaker and read by two different voices.
 */
class Tts
{
    public function __construct(private TtsEngine $engine) {}

    public static function voices(): array
    {
        return config('tts.voices');
    }

    /** Valid voice choices for a learner: a real voice, "mix" (mixed accents) or "browser" (no server audio). */
    public static function isChoice(string $v): bool
    {
        return $v === 'mix' || $v === 'browser' || isset(self::voices()[$v]);
    }

    public static function dir(): string
    {
        return config('tts.cache_path') ?: public_path('audio');
    }

    public static function normalize(string $text): string
    {
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', '', $text) ?? '';
        $text = preg_replace('/[ \t]+/u', ' ', $text) ?? '';
        $text = preg_replace('/\s*\n\s*/u', "\n", $text) ?? '';

        return trim($text);
    }

    /** "mix" picks one voice from the text itself, so the same text is always read by the same voice. */
    public static function resolveVoice(string $voice, string $text): string
    {
        if ($voice !== 'mix') {
            return $voice;
        }
        $mix = config('tts.mix');

        return $mix[crc32($text) % count($mix)];
    }

    /**
     * Splits a text into [text, voice] pieces. Dialogue lines "Karim : Bonjour" are read by two voices
     * (speaker labels are not read aloud); other text is cut at sentence ends into pieces of 350 characters.
     *
     * @return list<array{text:string,voice:string}>
     */
    public static function split(string $text, string $voice): array
    {
        $text = self::normalize($text);
        $main = self::resolveVoice($voice, $text);
        $partner = config("tts.voices.$main.partner", $main);
        $lines = $text === '' ? [] : explode("\n", $text);

        $parsed = array_map(fn ($l) => preg_match('/^([^:：\n]{1,32}?)\s*[:：]\s+(.+)$/u', $l, $m) ? [trim($m[1]), trim($m[2])] : null, $lines);
        if (count($lines) >= 2 && ! in_array(null, $parsed, true) && count(array_unique(array_column($parsed, 0))) >= 2) {
            $speakers = [];
            $parts = [];
            foreach ($parsed as [$who, $said]) {
                $speakers[$who] ??= count($speakers);
                $parts[] = ['text' => $said, 'voice' => $speakers[$who] % 2 === 0 ? $main : $partner];
            }

            return $parts;
        }

        $parts = [];
        foreach ($lines as $line) {
            foreach (self::chunk($line, 350) as $piece) {
                $parts[] = ['text' => $piece, 'voice' => $main];
            }
        }

        return $parts;
    }

    /** @return list<string> */
    private static function chunk(string $line, int $max): array
    {
        if (mb_strlen($line) <= $max) {
            return [$line];
        }
        $out = [];
        $cur = '';
        foreach (preg_split('/(?<=[.!?…])\s+/u', $line) ?: [$line] as $sentence) {
            if ($cur !== '' && mb_strlen($cur.' '.$sentence) > $max) {
                $out[] = $cur;
                $cur = $sentence;
            } else {
                $cur = trim($cur.' '.$sentence);
            }
        }
        if ($cur !== '') {
            $out[] = $cur;
        }

        return $out;
    }

    /** How many audio files still have to be generated for this text. */
    public function pending(string $text, string $voice, string $rate): int
    {
        $n = 0;
        $seen = [];
        foreach (self::split($text, $voice) as $p) {
            $f = self::fileFor($p['voice'], $rate, $p['text'])['file'];
            if (! isset($seen[$f]) && ! is_file($f)) {
                $n++;
            }
            $seen[$f] = true;
        }

        return $n;
    }

    /** Starts `php artisan tts:warm ...` in the background (does not wait for it). */
    public static function warmAsync(array $args): void
    {
        if (! config('tts.enabled') || ! config('tts.warm')) {
            return;
        }
        try {
            $php = PHP_BINDIR.'/php'.PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;
            $php = is_executable($php) ? $php : PHP_BINDIR.'/php';
            $cmd = 'cd '.escapeshellarg(base_path()).' && nohup '.escapeshellarg($php).' artisan tts:warm '
                .implode(' ', array_map('escapeshellarg', $args)).' >/dev/null 2>&1 &';
            \Symfony\Component\Process\Process::fromShellCommandline($cmd)->run();
        } catch (\Throwable) {
            // warming is only an optimisation
        }
    }

    public static function fileFor(string $voice, string $rate, string $text): array
    {
        $hash = sha1($voice.'|'.$rate.'|'.$text);

        return ['file' => self::dir().'/'.substr($hash, 0, 2).'/'.$hash.'.mp3', 'url' => '/audio/'.substr($hash, 0, 2).'/'.$hash.'.mp3'];
    }

    /**
     * Makes sure every piece of the text has an audio file and returns their URLs in reading order.
     *
     * @param  int|null  $newFiles  receives how many files had to be generated now
     * @return list<string>|null null when generation failed
     */
    public function parts(string $text, string $voice, string $rate, ?int &$newFiles = null): ?array
    {
        $parts = self::split($text, $voice);
        $urls = [];
        $jobs = [];
        foreach ($parts as $p) {
            $f = self::fileFor($p['voice'], $rate, $p['text']);
            $urls[] = $f['url'];
            if (! is_file($f['file'])) {
                $jobs[$f['file']] = ['text' => $p['text'], 'voice' => $p['voice'], 'rate' => $rate, 'file' => $f['file']];
            }
        }
        $newFiles = count($jobs);
        if ($jobs) {
            $ok = $this->engine->renderMany(array_values($jobs));
            if (in_array(false, $ok, true)) {
                return null;
            }
            if (random_int(1, 40) === 1) {
                self::prune();
            }
        }

        return $urls;
    }

    /** Deletes the oldest cached files when the cache is bigger than the limit (keeps ~75 percent). */
    public static function prune(): void
    {
        $max = config('tts.cache_max_mb') * 1024 * 1024;
        $files = [];
        $total = 0;
        foreach (glob(self::dir().'/*/*.mp3') ?: [] as $f) {
            $files[$f] = [filemtime($f), filesize($f)];
            $total += $files[$f][1];
        }
        if ($total <= $max) {
            return;
        }
        asort($files);
        foreach ($files as $f => [$mtime, $size]) {
            @unlink($f);
            $total -= $size;
            if ($total <= $max * 0.75) {
                break;
            }
        }
    }
}
