<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class FixUserRoles extends Command
{
    protected $signature = 'fix:user-roles';
    protected $description = 'Fix user roles in database';

    public function handle()
    {
        $this->info('Starting to fix user roles...');

        // Fix specific users based on your debug output
        $updates = [
            2 => ['role' => 'superadmin'],
            3 => ['role' => 'admin'], 
            4 => ['role' => 'teacher'],
        ];

        foreach ($updates as $userId => $data) {
            $user = User::find($userId);
            if ($user) {
                $user->update($data);
                $this->info("Updated user ID {$userId}: {$user->name} -> role: {$data['role']}");
            } else {
                $this->error("User ID {$userId} not found");
            }
        }

        $this->info('User roles fixed successfully!');
        
        // Show current roles
        $this->line("\nCurrent user roles:");
        $users = User::select('id', 'name', 'email', 'role')->get();
        foreach ($users as $user) {
            $this->line("ID: {$user->id}, Name: {$user->name}, Role: {$user->role}");
        }

        return 0;
    }
}
