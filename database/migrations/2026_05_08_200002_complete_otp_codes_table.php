<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The original migration only created id + timestamps.
        // The OtpCode model expects user_id, code, expires_at, used — add them.
        Schema::table('otp_codes', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->nullOnDelete();

            $table->string('code', 10)->after('user_id');
            $table->timestamp('expires_at')->nullable()->after('code');
            $table->boolean('used')->default(false)->after('expires_at');

            $table->index(['user_id', 'used']);
        });
    }

    public function down(): void
    {
        Schema::table('otp_codes', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'used']);
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'code', 'expires_at', 'used']);
        });
    }
};
