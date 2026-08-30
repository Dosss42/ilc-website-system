<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_installments', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_transaction_id')->nullable()->after('reference_number');
            $table->index('payment_transaction_id');
        });
    }

    public function down(): void
    {
        Schema::table('payment_installments', function (Blueprint $table) {
            $table->dropIndex(['payment_transaction_id']);
            $table->dropColumn('payment_transaction_id');
        });
    }
};
