<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Starter TCF Canada content: grammar, reading/listening, writing/speaking tasks, vocabulary.
        $this->call(MaterialSeeder::class);
    }
}
