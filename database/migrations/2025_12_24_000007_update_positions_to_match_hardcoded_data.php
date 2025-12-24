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
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Clear existing positions
        DB::table('positions')->delete();

        // Truncate auto-increment
        DB::statement('ALTER TABLE positions AUTO_INCREMENT = 1');

        $positions = [
            // Department 1: Quality
            ['id' => 1, 'name' => 'Manager Quality', 'department_id' => 1, 'level' => 4],
            ['id' => 2, 'name' => 'Assistant Manager Quality', 'department_id' => 1, 'level' => 3],
            ['id' => 3, 'name' => 'Supervisor Quality Control', 'department_id' => 1, 'level' => 3],
            ['id' => 4, 'name' => 'QA Engineer', 'department_id' => 1, 'level' => 2],
            ['id' => 5, 'name' => 'Foreman QC', 'department_id' => 1, 'level' => 2],
            ['id' => 6, 'name' => 'Inspektor QC', 'department_id' => 1, 'level' => 1],
            ['id' => 7, 'name' => 'Staff Admin QC', 'department_id' => 1, 'level' => 1],
            ['id' => 8, 'name' => 'Staff Admin QA', 'department_id' => 1, 'level' => 1],
            ['id' => 9, 'name' => 'Staff HSE', 'department_id' => 1, 'level' => 1],

            // Department 2: Maintenance
            ['id' => 10, 'name' => 'Manager Maintenance', 'department_id' => 2, 'level' => 4],
            ['id' => 11, 'name' => 'Supervisor Maintenance', 'department_id' => 2, 'level' => 3],
            ['id' => 12, 'name' => 'Foreman Maintenance', 'department_id' => 2, 'level' => 2],
            ['id' => 13, 'name' => 'Operator Maintenance Listrik', 'department_id' => 2, 'level' => 1],
            ['id' => 14, 'name' => 'Operator Maintenance Umum', 'department_id' => 2, 'level' => 1],

            // Department 3: PPC (Production Planning & Control)
            ['id' => 15, 'name' => 'Manager Production, Planning & Control', 'department_id' => 3, 'level' => 4],
            ['id' => 16, 'name' => 'Staff PPIC', 'department_id' => 3, 'level' => 2],
            ['id' => 17, 'name' => 'Supervisor Gudang', 'department_id' => 3, 'level' => 3],
            ['id' => 18, 'name' => 'Foreman Gudang', 'department_id' => 3, 'level' => 2],
            ['id' => 19, 'name' => 'Operator Gudang', 'department_id' => 3, 'level' => 1],

            // Department 4: Produksi & Dev Engineering
            ['id' => 20, 'name' => 'Manager Produksi & Development Engineering', 'department_id' => 4, 'level' => 4],
            ['id' => 21, 'name' => 'Supervisor Development Engineer', 'department_id' => 4, 'level' => 3],
            ['id' => 22, 'name' => 'Staff Development Engineer', 'department_id' => 4, 'level' => 2],
            ['id' => 23, 'name' => 'Supervisor Wax Room', 'department_id' => 4, 'level' => 3],
            ['id' => 24, 'name' => 'Foreman Wax Room', 'department_id' => 4, 'level' => 2],
            ['id' => 25, 'name' => 'Operator Wax Room', 'department_id' => 4, 'level' => 1],
            ['id' => 26, 'name' => 'Supervisor Mould Room', 'department_id' => 4, 'level' => 3],
            ['id' => 27, 'name' => 'Foreman Mould Room', 'department_id' => 4, 'level' => 2],
            ['id' => 28, 'name' => 'Operator Mould Room', 'department_id' => 4, 'level' => 1],
            ['id' => 29, 'name' => 'Supervisor Melting', 'department_id' => 4, 'level' => 3],
            ['id' => 30, 'name' => 'Foreman Melting', 'department_id' => 4, 'level' => 2],
            ['id' => 31, 'name' => 'Operator Melting', 'department_id' => 4, 'level' => 1],
            ['id' => 32, 'name' => 'Supervisor Cut Off', 'department_id' => 4, 'level' => 3],
            ['id' => 33, 'name' => 'Foreman Cut Off', 'department_id' => 4, 'level' => 2],
            ['id' => 34, 'name' => 'Operator Cut Off', 'department_id' => 4, 'level' => 1],
            ['id' => 35, 'name' => 'Supervisor Finishing & Straightening', 'department_id' => 4, 'level' => 3],
            ['id' => 36, 'name' => 'Foreman Finishing & Straightening', 'department_id' => 4, 'level' => 2],
            ['id' => 37, 'name' => 'Operator Finishing & Straightening', 'department_id' => 4, 'level' => 1],
            ['id' => 38, 'name' => 'Supervisor Machining', 'department_id' => 4, 'level' => 3],
            ['id' => 39, 'name' => 'Foreman Machining', 'department_id' => 4, 'level' => 2],
            ['id' => 40, 'name' => 'Operator Machining', 'department_id' => 4, 'level' => 1],
        ];

        DB::table('positions')->insert($positions);

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('positions')->delete();
    }
};
