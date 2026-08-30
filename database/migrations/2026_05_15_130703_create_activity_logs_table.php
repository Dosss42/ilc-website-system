<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->string('user_role')->nullable();
            $table->string('event_type', 50);          // login, logout, create, update, delete, error, etc.
            $table->string('description');
            $table->string('subject_type')->nullable(); // e.g. User, Enrollment
            $table->string('subject_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('extra')->nullable();          // JSON for extra context
            $table->timestamps();

            $table->index('user_id');
            $table->index('event_type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
