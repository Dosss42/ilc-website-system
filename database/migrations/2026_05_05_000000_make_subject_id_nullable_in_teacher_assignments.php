<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Must drop foreign keys first (they depend on the unique index)
        // Then drop the unique index, alter the column, and re-add everything

        // Step 1: Drop foreign key constraints that use the unique index
        DB::statement('ALTER TABLE teacher_assignments DROP FOREIGN KEY teacher_assignments_teacher_id_foreign');
        DB::statement('ALTER TABLE teacher_assignments DROP FOREIGN KEY teacher_assignments_section_id_foreign');

        // Step 2: Drop the unique index
        DB::statement('ALTER TABLE teacher_assignments DROP INDEX teacher_assignment_unique');

        // Step 3: Also drop the leftover subject_id index if it exists.
        // On a fresh migrate (new install, disaster-recovery rebuild, or a
        // test database), the FK constraint of the same name still exists
        // at this point (MySQL auto-names it after the index), and MySQL
        // refuses to drop an index a foreign key still depends on — drop
        // the FK first. Guarded because on an already-migrated database
        // (this migration already ran there) that FK is long gone.
        try {
            DB::statement('ALTER TABLE teacher_assignments DROP FOREIGN KEY teacher_assignments_subject_id_foreign');
        } catch (\Throwable $e) {
            // FK doesn't exist — nothing to drop, continue.
        }
        DB::statement('ALTER TABLE teacher_assignments DROP INDEX teacher_assignments_subject_id_foreign');

        // Step 4: Make subject_id nullable
        DB::statement('ALTER TABLE teacher_assignments MODIFY subject_id bigint unsigned NULL');

        // Step 5: Re-add foreign keys
        DB::statement('ALTER TABLE teacher_assignments ADD CONSTRAINT teacher_assignments_teacher_id_foreign FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE CASCADE');
        DB::statement('ALTER TABLE teacher_assignments ADD CONSTRAINT teacher_assignments_section_id_foreign FOREIGN KEY (section_id) REFERENCES sections(id) ON DELETE CASCADE');
        DB::statement('ALTER TABLE teacher_assignments ADD CONSTRAINT teacher_assignments_subject_id_foreign FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE SET NULL');

        // Step 6: Re-add unique constraint
        DB::statement('ALTER TABLE teacher_assignments ADD UNIQUE KEY teacher_assignment_unique (teacher_id, subject_id, section_id, school_year)');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE teacher_assignments DROP FOREIGN KEY teacher_assignments_teacher_id_foreign');
        DB::statement('ALTER TABLE teacher_assignments DROP FOREIGN KEY teacher_assignments_section_id_foreign');
        DB::statement('ALTER TABLE teacher_assignments DROP FOREIGN KEY teacher_assignments_subject_id_foreign');
        DB::statement('ALTER TABLE teacher_assignments DROP INDEX teacher_assignment_unique');

        DB::statement('ALTER TABLE teacher_assignments MODIFY subject_id bigint unsigned NOT NULL');

        DB::statement('ALTER TABLE teacher_assignments ADD CONSTRAINT teacher_assignments_teacher_id_foreign FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE CASCADE');
        DB::statement('ALTER TABLE teacher_assignments ADD CONSTRAINT teacher_assignments_section_id_foreign FOREIGN KEY (section_id) REFERENCES sections(id) ON DELETE CASCADE');
        DB::statement('ALTER TABLE teacher_assignments ADD CONSTRAINT teacher_assignments_subject_id_foreign FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE');
        DB::statement('ALTER TABLE teacher_assignments ADD UNIQUE KEY teacher_assignment_unique (teacher_id, subject_id, section_id, school_year)');
    }
};
