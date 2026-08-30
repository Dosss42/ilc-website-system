<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixDatabaseSchema extends Command
{
    protected $signature = 'db:fix-schema';
    protected $description = 'Fix database schema for progressive registration';

    public function handle()
    {
        $this->info('Fixing database schema...');

        try {
            // Make birthdate and gender nullable in student_profiles
            DB::statement('ALTER TABLE student_profiles MODIFY COLUMN birthdate DATE NULL');
            DB::statement('ALTER TABLE student_profiles MODIFY COLUMN gender ENUM("male", "female") NULL');
            
            $this->info('Database schema fixed successfully!');
            return 0;
        } catch (\Exception $e) {
            $this->error('Error fixing schema: ' . $e->getMessage());
            return 1;
        }
    }
}
