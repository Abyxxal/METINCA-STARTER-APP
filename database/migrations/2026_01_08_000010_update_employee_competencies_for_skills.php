<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_competencies', function (Blueprint $table) {
            // First drop the foreign key constraint on nik
            $table->dropForeign(['nik']);

            // Drop the unique constraint on nik
            $table->dropUnique(['nik']);

            // Add skill_id column
            $table->unsignedBigInteger('skill_id')->nullable()->after('nik');

            // Re-add the foreign key on nik (non-unique)
            $table->foreign('nik')->references('nik')->on('employees')->onDelete('cascade');

            // Add foreign key for skill_id
            $table->foreign('skill_id')->references('id')->on('skills')->onDelete('cascade');

            // Add new unique constraint for (nik, skill_id)
            $table->unique(['nik', 'skill_id']);

            // Change level to allow 0-4 (0 = belum training)
            $table->integer('level')->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('employee_competencies', function (Blueprint $table) {
            $table->dropForeign(['nik']);
            $table->dropForeign(['skill_id']);
            $table->dropUnique(['nik', 'skill_id']);
            $table->dropColumn('skill_id');
            $table->foreign('nik')->references('nik')->on('employees')->onDelete('cascade');
            $table->unique(['nik']);
            $table->integer('level')->default(1)->change();
        });
    }
};
