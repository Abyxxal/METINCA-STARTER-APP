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
        // MySQL requires re-declaring all enum values when modifying
        DB::statement("ALTER TABLE exam_sessions MODIFY COLUMN status ENUM('assigned','started','submitted','verified_pass','verified_fail','approved','rejected') NOT NULL DEFAULT 'assigned'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE exam_sessions MODIFY COLUMN status ENUM('assigned','started','submitted','verified_pass','verified_fail') NOT NULL DEFAULT 'assigned'");
    }
};
