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
        Schema::table('employees', function (Blueprint $table) {
            $table->foreignId('division_id')->nullable()->after('department_id')->constrained('divisions')->onDelete('set null')->comment('ID Divisi');
            $table->index('division_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['division_id']);
            $table->dropIndex(['division_id']);
            $table->dropColumn('division_id');
        });
    }
};
