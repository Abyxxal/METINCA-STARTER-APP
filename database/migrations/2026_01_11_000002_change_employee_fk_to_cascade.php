<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Change onDelete constraint dari 'set null' ke 'cascade'
     * Sehingga ketika employee dihapus, user account juga otomatis dihapus
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop existing foreign key
            $table->dropForeign(['employee_nik']);
            
            // Add new foreign key with cascade delete
            $table->foreign('employee_nik')
                ->references('nik')
                ->on('employees')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop cascade foreign key
            $table->dropForeign(['employee_nik']);
            
            // Restore set null foreign key
            $table->foreign('employee_nik')
                ->references('nik')
                ->on('employees')
                ->onDelete('set null');
        });
    }
};
