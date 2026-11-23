<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FrenchKeys extends Command
{
    protected $signature = 'french:keys';

    protected $description = 'Generate a Hermes / automation API key (prints the line for your .env)';

    public function handle(): int
    {
        $this->line('# Add this line to .env:');
        $this->line('HERMES_API_KEY='.bin2hex(random_bytes(24)));

        return self::SUCCESS;
    }
}
