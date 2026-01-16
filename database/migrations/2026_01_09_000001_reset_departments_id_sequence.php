<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Disable foreign key constraints temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        try {
            // Truncate related tables first (cascade)
            DB::table('divisions')->truncate();
            DB::table('departments')->truncate();

            // Reset auto-increment untuk departments
            DB::statement('ALTER TABLE departments AUTO_INCREMENT = 1');
            
            // Reset auto-increment untuk divisions
            DB::statement('ALTER TABLE divisions AUTO_INCREMENT = 1');

            // Re-enable foreign key constraints
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        } catch (\Exception $e) {
            // Re-enable foreign key constraints on error
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Truncate again on rollback
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        DB::table('divisions')->truncate();
        DB::table('departments')->truncate();
        
        DB::statement('ALTER TABLE departments AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE divisions AUTO_INCREMENT = 1');
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
