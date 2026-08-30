<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->string('lrn', 50)->nullable()->index();
            $table->string('from_grade', 30);
            $table->string('to_grade', 30);
            $table->string('from_school_year', 20);
            $table->string('to_school_year', 20);
            $table->foreignId('from_section_id')->nullable()->constrained('sections')->onDelete('set null');
            $table->foreignId('to_section_id')->nullable()->constrained('sections')->onDelete('set null');
            $table->foreignId('promoted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('promoted_at')->useCurrent();
            $table->string('status', 30)->default('completed');
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'from_school_year']);
            $table->index(['to_school_year', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
