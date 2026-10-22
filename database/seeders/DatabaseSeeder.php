<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * `php artisan db:seed` installs only the default settings — no dummy accounts or sample
     * data. The first real Super Admin is made on the Emergency Recovery page (/recovery ›
     * Repair Super Admin) or with `php artisan superadmin:recover`.
     *
     * Test accounts for a developer's own computer, never the live server (they refuse to run
     * when APP_ENV=production):
     *   php artisan db:seed --class=RoleAccountsSeeder
     *   php artisan db:seed --class=AnnouncementNewsSeeder
     */
    public function run(): void
    {
        $this->call(DefaultSettingsSeeder::class);
    }
}
