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
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        try {
            // Ambil semua departments yang ada, sorted by created_at
            $departments = DB::table('departments')
                ->orderBy('created_at')
                ->select('id', 'name', 'description', 'created_at', 'updated_at')
                ->get();

            // Jika ada data, reindex dengan ID baru
            if ($departments->count() > 0) {
                // Delete all departments (cascade akan delete divisions)
                DB::table('departments')->delete();
                
                // Reset auto-increment
                DB::statement('ALTER TABLE departments AUTO_INCREMENT = 1');
                
                // Insert kembali dengan ID sequential baru
                $newId = 1;
                foreach ($departments as $dept) {
                    DB::table('departments')->insert([
                        'id' => $newId,
                        'name' => $dept->name,
                        'description' => $dept->description,
                        'created_at' => $dept->created_at,
                        'updated_at' => $dept->updated_at,
                    ]);
                    $newId++;
                }
            }

            // Reset auto-increment untuk divisions
            DB::statement('ALTER TABLE divisions AUTO_INCREMENT = 1');
            DB::table('divisions')->delete();

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        } catch (\Exception $e) {
            // Re-enable foreign key checks on error
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
        
        DB::table('divisions')->delete();
        DB::table('departments')->delete();
        
        DB::statement('ALTER TABLE departments AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE divisions AUTO_INCREMENT = 1');
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
