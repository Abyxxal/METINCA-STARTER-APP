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
        Schema::table('exam_periods', function (Blueprint $table) {
            $table->foreignId('department_id')
                ->nullable()
                ->after('name')
                ->constrained()
                ->nullOnDelete()
                ->comment('NULL = periode tidak dibatasi per departemen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_periods', function (Blueprint $table) {
            $table->dropConstrainedForeignId('department_id');
        });
    }
};