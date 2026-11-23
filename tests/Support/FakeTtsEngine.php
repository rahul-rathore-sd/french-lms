<?php

namespace Tests\Support;

use App\Support\TtsEngine;

/** Writes small fake MP3 files and remembers what it was asked to render. */
class FakeTtsEngine implements TtsEngine
{
    /** @var list<array{text:string,voice:string,rate:string,file:string}> */
    public array $rendered = [];

    public bool $fail = false;

    public function renderMany(array $jobs): array
    {
        $ok = [];
        foreach ($jobs as $job) {
            if ($this->fail) {
                $ok[] = false;

                continue;
            }
            @mkdir(dirname($job['file']), 0775, true);
            file_put_contents($job['file'], str_repeat('ID3', 400));
            $this->rendered[] = $job;
            $ok[] = true;
        }

        return $ok;
    }
}
