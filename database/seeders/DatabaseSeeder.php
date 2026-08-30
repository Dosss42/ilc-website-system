<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name'     => 'Test User',
                'email'    => 'test@example.com',
                'password' => Hash::make('password'),
                'role'     => 'superadmin',
            ]
        );


        // ── ADMIN / REGISTRAR ──
        User::updateOrCreate(
            ['email' => 'admin@ilc.edu.ph'],
            [
                'name'              => 'Admin Registrar',
                'email'             => 'admin@ilc.edu.ph',
                'password'          => Hash::make('Admin@123'),
                'role'              => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // ── TEACHER ──
        User::updateOrCreate(
            ['email' => 'teacher@ilc.edu.ph'],
            [
                'name'              => 'Sample Teacher',
                'email'             => 'teacher@ilc.edu.ph',
                'password'          => Hash::make('Teacher@123'),
                'role'              => 'teacher',
                'email_verified_at' => now(),
            ]
        );

        // ── STUDENT ──
        User::updateOrCreate(
            ['email' => 'student@ilc.edu.ph'],
            [
                'name'              => 'Juan Dela Cruz',
                'email'             => 'student@ilc.edu.ph',
                'password'          => Hash::make('Student@123'),
                'role'              => 'student',
                'email_verified_at' => now(),
            ]
        );

        // ── FINANCE ──
        User::updateOrCreate(
            ['email' => 'finance@ilc.edu.ph'],
            [
                'name'              => 'Finance Officer',
                'email'             => 'finance@ilc.edu.ph',
                'password'          => Hash::make('Finance@123'),
                'role'              => 'finance',
                'is_active'         => true,
                'email_verified_at' => now(),
            ]
        );

        // ── CASHIER ──
        User::updateOrCreate(
            ['email' => 'cashier@ilc.edu.ph'],
            [
                'name'              => 'Cashier Staff',
                'email'             => 'cashier@ilc.edu.ph',
                'password'          => Hash::make('Cashier@123'),
                'role'              => 'cashier',
                'is_active'         => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
