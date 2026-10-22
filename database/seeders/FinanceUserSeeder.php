<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class FinanceUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Known password — a developer's own computer only, never the live server
        if (app()->environment('production')) {
            $this->command?->warn('Skipped: no dummy accounts on the live server (APP_ENV=production).');
            return;
        }

        // Create default finance user
        User::updateOrCreate(
            ['email' => 'finance@ilc.com'],
            [
                'name' => 'Finance Administrator',
                'email' => 'finance@ilc.com',
                'password' => Hash::make('admin123'),
                'role' => 'finance',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Finance user created successfully!');
        $this->command->info('Email: finance@ilc.com');
        $this->command->info('Password: admin123');
    }
}
