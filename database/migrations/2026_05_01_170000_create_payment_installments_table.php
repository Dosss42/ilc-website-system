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
        Schema::create('payment_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained('enrollments')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('month_name'); // e.g., 'July', 'August'
            $table->date('due_date'); // End of month date
            $table->decimal('amount', 10, 2); // Monthly amount due
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->decimal('late_fee', 10, 2)->default(0);
            $table->string('status')->default('pending'); // pending, paid, overdue, waived
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('reference_number')->nullable();
            $table->foreignId('document_id')->nullable()->constrained('student_documents')->nullOnDelete();
            $table->integer('weeks_overdue')->default(0);
            $table->boolean('warning_sent')->default(false);
            $table->boolean('late_fee_applied')->default(false);
            $table->boolean('block_notice_sent')->default(false);
            $table->timestamps();

            $table->index(['enrollment_id', 'status']);
            $table->index(['due_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_installments');
    }
};
