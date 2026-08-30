<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // enrollments — most heavily queried table in the system
        Schema::table('enrollments', function (Blueprint $table) {
            // status and payment_status are filtered on nearly every admin/finance page
            $table->index('status', 'idx_enrollments_status');
            $table->index('payment_status', 'idx_enrollments_payment_status');
            $table->index('school_year', 'idx_enrollments_school_year');
            $table->index('grade_level', 'idx_enrollments_grade_level');
            // composite: admin dashboard filters (status + school_year is the most common pair)
            $table->index(['school_year', 'status'], 'idx_enrollments_school_year_status');
            // composite: payment tracking (finance portal)
            $table->index(['payment_status', 'payment_type'], 'idx_enrollments_payment_type_status');
        });

        // student_documents — queried on every student portal load and finance review
        Schema::table('student_documents', function (Blueprint $table) {
            $table->index('document_type', 'idx_student_documents_type');
            $table->index('status', 'idx_student_documents_status');
            // composite: the single most common query — "show pending payment screenshots"
            $table->index(['document_type', 'status'], 'idx_student_documents_type_status');
        });

        // users — role filter runs on admin/teacher/student counts everywhere
        Schema::table('users', function (Blueprint $table) {
            $table->index('role', 'idx_users_role');
            $table->index('is_active', 'idx_users_is_active');
            // composite: admin lists almost always filter role + active together
            $table->index(['role', 'is_active'], 'idx_users_role_active');
        });

        // schedules — student portal loads schedules on every dashboard hit
        Schema::table('schedules', function (Blueprint $table) {
            $table->index('is_active', 'idx_schedules_is_active');
            $table->index('day_of_week', 'idx_schedules_day');
            // composite: primary query = section_id + is_active
            $table->index(['section_id', 'is_active'], 'idx_schedules_section_active');
        });

        // sections — filtered by grade_level + school_year + is_active on enrollment assign
        Schema::table('sections', function (Blueprint $table) {
            $table->index('grade_level', 'idx_sections_grade_level');
            $table->index('school_year', 'idx_sections_school_year');
            $table->index('is_active', 'idx_sections_active');
            $table->index(['grade_level', 'school_year', 'is_active'], 'idx_sections_grade_year_active');
        });

        // guidance_records — admin lists filter by status and concern_type
        Schema::table('guidance_records', function (Blueprint $table) {
            $table->index('status', 'idx_guidance_status');
            $table->index('concern_type', 'idx_guidance_concern_type');
        });

        // announcements — student portal loads announcements filtered by section + is_active
        Schema::table('announcements', function (Blueprint $table) {
            $table->index('is_active', 'idx_announcements_active');
        });

        // subjects — admin lists filter by is_active and grade_level
        Schema::table('subjects', function (Blueprint $table) {
            $table->index('grade_level', 'idx_subjects_grade_level');
            $table->index('is_active', 'idx_subjects_active');
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropIndex('idx_enrollments_status');
            $table->dropIndex('idx_enrollments_payment_status');
            $table->dropIndex('idx_enrollments_school_year');
            $table->dropIndex('idx_enrollments_grade_level');
            $table->dropIndex('idx_enrollments_school_year_status');
            $table->dropIndex('idx_enrollments_payment_type_status');
        });

        Schema::table('student_documents', function (Blueprint $table) {
            $table->dropIndex('idx_student_documents_type');
            $table->dropIndex('idx_student_documents_status');
            $table->dropIndex('idx_student_documents_type_status');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_role');
            $table->dropIndex('idx_users_is_active');
            $table->dropIndex('idx_users_role_active');
        });

        Schema::table('schedules', function (Blueprint $table) {
            $table->dropIndex('idx_schedules_is_active');
            $table->dropIndex('idx_schedules_day');
            $table->dropIndex('idx_schedules_section_active');
        });

        Schema::table('sections', function (Blueprint $table) {
            $table->dropIndex('idx_sections_grade_level');
            $table->dropIndex('idx_sections_school_year');
            $table->dropIndex('idx_sections_active');
            $table->dropIndex('idx_sections_grade_year_active');
        });

        Schema::table('guidance_records', function (Blueprint $table) {
            $table->dropIndex('idx_guidance_status');
            $table->dropIndex('idx_guidance_concern_type');
        });

        Schema::table('announcements', function (Blueprint $table) {
            $table->dropIndex('idx_announcements_active');
        });

        Schema::table('subjects', function (Blueprint $table) {
            $table->dropIndex('idx_subjects_grade_level');
            $table->dropIndex('idx_subjects_active');
        });
    }
};
