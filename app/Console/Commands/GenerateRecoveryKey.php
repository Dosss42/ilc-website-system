<?php

namespace App\Console\Commands;

use App\Services\EmergencyRecovery;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

/**
 * Make a new Recovery Key for the Emergency Recovery page (/recovery). It is only printed —
 * put it in the server settings (RECOVERY_KEY) and on paper; it is never stored in the
 * database. See docs/EMERGENCY_RECOVERY.md.
 */
#[Signature('recovery:key')]
#[Description('Generate a new Recovery Key for the Emergency Recovery page')]
class GenerateRecoveryKey extends Command
{
    public function handle(): int
    {
        $key = EmergencyRecovery::generateKey();

        if (EmergencyRecovery::isSetUp()) {
            $this->warn('A Recovery Key is already set. Replacing it makes the old printed key stop working.');
        }
        $this->info('New Recovery Key:');
        $this->newLine();
        $this->line('    ' . $key);
        $this->newLine();
        $this->line('1) Add this line to .env (XAMPP) and to Railway › your service › Variables:');
        $this->line('   RECOVERY_KEY=' . $key);
        $this->line('2) Run  php artisan config:clear  (XAMPP).');
        $this->line('3) Print or write the key down, seal it in an envelope, and give it to the school head.');
        $this->line('   Do not save it on the school computers or send it by chat/email.');

        return self::SUCCESS;
    }
}
