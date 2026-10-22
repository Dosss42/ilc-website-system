<?php

namespace Database\Seeders;

use App\Support\DefaultSettings;
use Illuminate\Database\Seeder;

/**
 * Adds any missing default setting (school info, academic, fees and payment options, security)
 * and, when there are no fees yet, the school's standard fees. Never changes a saved value and
 * creates no accounts or sample data — safe to run on the live server, and
 * docker/entrypoint.sh runs it on every start.
 */
class DefaultSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $created = DefaultSettings::install();
        $this->command?->info($created > 0 ? "Default settings: {$created} added." : 'Default settings: all present.');
        $this->command?->info(DefaultSettings::installFees() ? 'Default fees: standard fees added.' : 'Default fees: already set.');
    }
}
