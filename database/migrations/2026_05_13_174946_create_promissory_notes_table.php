<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promissory_notes', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number', 30)->unique();
            $table->foreignId('enrollment_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->decimal('amount_overdue', 10, 2)->default(0);
            $table->decimal('amount_promised', 10, 2);
            $table->date('promise_date');
            $table->date('date_issued');
            $table->string('parent_guardian')->nullable();
            $table->text('remarks')->nullable();
            $table->enum('status', ['pending', 'fulfilled', 'broken', 'extended'])->default('pending');
            $table->date('extended_date')->nullable();
            $table->timestamp('fulfilled_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promissory_notes');
    }
};
