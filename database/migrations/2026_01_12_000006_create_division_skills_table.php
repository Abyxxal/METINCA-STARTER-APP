<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: division_skills (Pivot Table)
     * Purpose: Map which skills are required for each division
     * Fields:
     * - division_id: Which division
     * - skill_id: Which skill
     * - required_level: Minimum required level for this division
     * - is_mandatory: Whether skill is mandatory or optional
     */
    public function up(): void
    {
        Schema::create('division_skills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('division_id');
            $table->unsignedBigInteger('skill_id');
            $table->unsignedTinyInteger('required_level')->default(1)->comment('Minimum required level');
            $table->text('description')->nullable();
            $table->boolean('is_mandatory')->default(true);
            $table->timestamps();

            $table->foreign('division_id')->references('id')->on('divisions')->onDelete('cascade');
            $table->foreign('skill_id')->references('id')->on('skills')->onDelete('cascade');
            $table->unique(['division_id', 'skill_id']);
            $table->index('division_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('division_skills');
    }
};
