<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_documents', function (Blueprint $table) {
            $table->string('file_path')->nullable()->change();
            $table->string('original_name')->nullable()->change();
            $table->unsignedBigInteger('file_size')->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('student_documents', function (Blueprint $table) {
            $table->string('file_path')->nullable(false)->change();
            $table->string('original_name')->nullable(false)->change();
            $table->unsignedBigInteger('file_size')->nullable(false)->default(0)->change();
        });
    }
};
