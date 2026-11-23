<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class FrenchKeys extends Command
{
    protected $signature = 'french:keys {--password= : Use this admin password instead of generating one}';

    protected $description = 'Generate the /admin password hash and the Hermes API key (prints values for your .env)';

    public function handle(): int
    {
        $password = $this->option('password') ?: Str::password(16, symbols: false);
        $hermes = bin2hex(random_bytes(24));

        $this->line('# Add these two lines to .env (the password itself is NOT stored anywhere, so save it now):');
        $this->line('ADMIN_PASSWORD_HASH="'.str_replace('$', '\\$', Hash::make($password)).'"');
        $this->line('HERMES_API_KEY='.$hermes);
        $this->newLine();
        $this->info('Admin password (save it in a password manager): '.$password);

        return self::SUCCESS;
    }
}
