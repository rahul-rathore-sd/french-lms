<?php

namespace App\Support;

use Symfony\Component\Process\Process;

/** Runs the free `edge-tts` command (Python) and writes MP3 files. Up to 4 files are rendered at the same time. */
class EdgeTtsEngine implements TtsEngine
{
    private const PARALLEL = 4;
    private const TIMEOUT = 40;

    public function renderMany(array $jobs): array
    {
        $binary = config('tts.binary');
        if (! is_executable($binary)) {
            return array_fill(0, count($jobs), false);
        }

        $results = array_fill(0, count($jobs), false);
        foreach (array_chunk(array_keys($jobs), self::PARALLEL) as $batch) {
            $running = [];
            foreach ($batch as $i) {
                $job = $jobs[$i];
                @mkdir(dirname($job['file']), 0775, true);
                $tmpText = tempnam(sys_get_temp_dir(), 'tts');
                file_put_contents($tmpText, $job['text']);
                $tmpOut = $job['file'].'.part'.getmypid();
                $p = new Process([$binary, '--voice', $job['voice'], '--rate='.$job['rate'], '--file', $tmpText, '--write-media', $tmpOut]);
                $p->setTimeout(self::TIMEOUT);
                $p->start();
                $running[$i] = [$p, $tmpText, $tmpOut];
            }
            foreach ($running as $i => [$p, $tmpText, $tmpOut]) {
                try {
                    $p->wait();
                    // a real MP3 is more than a few hundred bytes; anything smaller is an error page or empty audio
                    if ($p->isSuccessful() && is_file($tmpOut) && filesize($tmpOut) > 800) {
                        $results[$i] = rename($tmpOut, $jobs[$i]['file']);
                    }
                } catch (\Throwable) {
                    // timeout or crash: reported as failure below
                } finally {
                    @unlink($tmpText);
                    if (is_file($tmpOut)) {
                        @unlink($tmpOut);
                    }
                }
            }
        }

        return $results;
    }
}
