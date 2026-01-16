<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add verification fields to employee_competencies table.
 * This enables admin verification of competency levels after CBT exams.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_competencies', function (Blueprint $table) {
            // Add verification fields if they don't exist
            if (!Schema::hasColumn('employee_competencies', 'verified_by')) {
                $table->unsignedBigInteger('verified_by')->nullable()->after('level');
            }
            if (!Schema::hasColumn('employee_competencies', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('verified_by');
            }
            if (!Schema::hasColumn('employee_competencies', 'notes')) {
                $table->text('notes')->nullable()->after('verified_at');
            }
            if (!Schema::hasColumn('employee_competencies', 'employee_nik')) {
                // Rename nik to employee_nik for consistency
                $table->renameColumn('nik', 'employee_nik');
            }

            // Add foreign key for verified_by (admin user)
            // Note: Only add if nik column was renamed
        });

        // Add foreign key constraint in separate statement
        if (Schema::hasColumn('employee_competencies', 'verified_by')) {
            Schema::table('employee_competencies', function (Blueprint $table) {
                // Try to add foreign key, ignore if already exists
                try {
                    $table->foreign('verified_by')
                        ->references('id')
                        ->on('users')
                        ->onDelete('set null');
                } catch (\Exception $e) {
                    // Foreign key might already exist
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('employee_competencies', function (Blueprint $table) {
            // Drop foreign key first
            try {
                $table->dropForeign(['verified_by']);
            } catch (\Exception $e) {
                // Ignore if doesn't exist
            }

            // Drop columns
            $columns = ['verified_by', 'verified_at', 'notes'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('employee_competencies', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
