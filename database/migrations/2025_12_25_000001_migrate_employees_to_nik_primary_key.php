<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Drop foreign key from users table if exists
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['employee_id']);
            });
        } catch (\Exception $e) {
            // Foreign key doesn't exist, continue
        }

        // Step 2: Drop employee_id column from users if exists
        if (Schema::hasColumn('users', 'employee_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('employee_id');
            });
        }

        // Step 3: Check if employees table has id column and primary key to drop
        if (Schema::hasColumn('employees', 'id')) {
            DB::statement('ALTER TABLE employees DROP PRIMARY KEY, DROP COLUMN id, ADD PRIMARY KEY (nik)');
        }

        // Step 4: Add employee_nik column to users if not exists
        if (!Schema::hasColumn('users', 'employee_nik')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('employee_nik')->nullable()->after('profile_photo_url')->comment('Link ke NIK employee');
                $table->foreign('employee_nik')->references('nik')->on('employees')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a complex migration, rollback is not recommended
        // You would need to manually restore the data structure
    }
};
