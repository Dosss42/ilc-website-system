<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 3NF fix: mother_name, mother_age, father_name, father_age stored
        // in student_profiles are a transitive dependency — they belong in
        // the guardians table (which already exists for the primary guardian).

        // 1. Make guardians.contact nullable (parents may not provide a number)
        //    and add an age column.
        Schema::table('guardians', function (Blueprint $table) {
            $table->string('contact', 20)->nullable()->change();
            $table->unsignedSmallInteger('age')->nullable()->after('contact');
        });

        // 2. Migrate mother / father data from student_profiles into guardians.
        //    If a Mother/Father record already exists (from enrollment), only
        //    backfill the age if it's still NULL.
        $profiles = DB::table('student_profiles')
            ->whereNotNull('mother_name')
            ->orWhereNotNull('father_name')
            ->get(['user_id', 'mother_name', 'mother_age', 'father_name', 'father_age']);

        foreach ($profiles as $profile) {
            if ($profile->mother_name) {
                $existing = DB::table('guardians')
                    ->where('user_id', $profile->user_id)
                    ->where('relationship', 'Mother')
                    ->first();

                if ($existing) {
                    if (is_null($existing->age) && $profile->mother_age) {
                        DB::table('guardians')
                            ->where('id', $existing->id)
                            ->update(['age' => $profile->mother_age, 'updated_at' => now()]);
                    }
                } else {
                    DB::table('guardians')->insert([
                        'user_id'      => $profile->user_id,
                        'relationship' => 'Mother',
                        'name'         => $profile->mother_name,
                        'age'          => $profile->mother_age,
                        'contact'      => null,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]);
                }
            }

            if ($profile->father_name) {
                $existing = DB::table('guardians')
                    ->where('user_id', $profile->user_id)
                    ->where('relationship', 'Father')
                    ->first();

                if ($existing) {
                    if (is_null($existing->age) && $profile->father_age) {
                        DB::table('guardians')
                            ->where('id', $existing->id)
                            ->update(['age' => $profile->father_age, 'updated_at' => now()]);
                    }
                } else {
                    DB::table('guardians')->insert([
                        'user_id'      => $profile->user_id,
                        'relationship' => 'Father',
                        'name'         => $profile->father_name,
                        'age'          => $profile->father_age,
                        'contact'      => null,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]);
                }
            }
        }

        // 3. Drop the now-redundant columns from student_profiles
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->dropColumn(['mother_name', 'mother_age', 'father_name', 'father_age']);
        });
    }

    public function down(): void
    {
        // Restore columns to student_profiles
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->string('mother_name', 150)->nullable()->after('religious_affiliation');
            $table->integer('mother_age')->nullable()->after('mother_name');
            $table->string('father_name', 150)->nullable()->after('mother_age');
            $table->integer('father_age')->nullable()->after('father_name');
        });

        // Repopulate from guardians
        DB::table('guardians')
            ->whereIn('relationship', ['Mother', 'Father'])
            ->get()
            ->each(function ($g) {
                $col = $g->relationship === 'Mother'
                    ? ['mother_name' => $g->name, 'mother_age' => $g->age]
                    : ['father_name' => $g->name, 'father_age' => $g->age];

                DB::table('student_profiles')
                    ->where('user_id', $g->user_id)
                    ->update($col + ['updated_at' => now()]);
            });

        Schema::table('guardians', function (Blueprint $table) {
            $table->dropColumn('age');
            $table->string('contact', 20)->nullable(false)->change();
        });
    }
};
