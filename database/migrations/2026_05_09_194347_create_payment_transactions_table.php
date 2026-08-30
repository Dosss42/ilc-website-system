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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained('enrollments')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('payment_type'); // 'online', 'walkin', 'admin'
            $table->string('payment_method'); // 'gcash', 'cash'
            $table->decimal('amount', 10, 2);
            $table->string('reference_number')->nullable();
            $table->string('description')->nullable();
            $table->string('status')->default('completed'); // 'completed', 'pending', 'rejected'
            $table->string('installment_month')->nullable(); // e.g., 'July', 'August'
            $table->foreignId('installment_id')->nullable()->constrained('payment_installments')->nullOnDelete();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete(); // Admin who processed
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            
            $table->index(['enrollment_id', 'status']);
            $table->index(['user_id', 'payment_type']);
            $table->index(['payment_type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
