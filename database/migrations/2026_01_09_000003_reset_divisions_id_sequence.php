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
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        try {
            // Truncate semua divisions
            DB::table('divisions')->truncate();
            
            // Reset auto-increment untuk divisions
            DB::statement('ALTER TABLE divisions AUTO_INCREMENT = 1');

            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        DB::table('divisions')->truncate();
        
        DB::statement('ALTER TABLE divisions AUTO_INCREMENT = 1');
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
