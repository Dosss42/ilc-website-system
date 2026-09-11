<?php

namespace App\Console\Commands;

use App\Services\BackupService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('backup:run')]
#[Description('Create a database backup automatically, copy it to the configured secondary drive / Google Drive, and prune old backups')]
class RunDatabaseBackup extends Command
{
    public function handle()
    {
        $this->info('Running scheduled database backup...');

        $result = BackupService::runFull();

        if (!$result['success']) {
            $this->error($result['message']);
            return self::FAILURE;
        }

        $this->info($result['message']);
        $this->line('Secondary drive copy: ' . ($result['secondary'] ? 'yes' : 'skipped/not configured'));
        $this->line('Google Drive copy: ' . ($result['drive'] ? 'yes' : 'skipped/not configured'));

        return self::SUCCESS;
    }
}
