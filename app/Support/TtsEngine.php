<?php

namespace App\Support;

/** Turns text into an MP3 file. Swapped for a fake in tests. */
interface TtsEngine
{
    /**
     * Renders several jobs (in parallel where possible).
     *
     * @param  list<array{text:string,voice:string,rate:string,file:string}>  $jobs
     * @return list<bool> success flag per job, in the same order
     */
    public function renderMany(array $jobs): array;
}
