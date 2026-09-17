<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleAccountsSeeder extends Seeder
{
    /**
     * Creates one default login for each staff role.
     * Safe to re-run — updateOrCreate keyed by email.
     */
    public function run(): void
    {
        $accounts = [
            [
                'email' => 'superadmin@ilc.com',
                'name' => 'Super Administrator',
                'role' => 'superadmin',
                'password' => 'SuperAdmin123!',
            ],
            [
                'email' => 'admin@ilc.com',
                'name' => 'Administrator',
                'role' => 'admin',
                'password' => 'Admin123!',
            ],
            [
                'email' => 'teacher@ilc.com',
                'name' => 'Teacher Account',
                'role' => 'teacher',
                'password' => 'Teacher123!',
            ],
            [
                'email' => 'cashier@ilc.com',
                'name' => 'Cashier Account',
                'role' => 'cashier',
                'password' => 'Cashier123!',
            ],
            [
                'email' => 'finance@ilc.com',
                'name' => 'Finance Administrator',
                'role' => 'finance',
                'password' => 'Finance123!',
            ],
        ];

        foreach ($accounts as $account) {
            User::updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'password' => Hash::make($account['password']),
                    'role' => $account['role'],
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );

            $this->command->info("{$account['role']}: {$account['email']} / {$account['password']}");
        }
    }
}
