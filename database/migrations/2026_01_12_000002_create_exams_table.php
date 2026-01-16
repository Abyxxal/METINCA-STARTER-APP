<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: exams
     * Purpose: Store exam definitions with settings
     * Fields:
     * - skill_id: Which skill this exam tests
     * - title: Exam name
     * - target_level: Competency level employee will achieve if passed
     * - passing_score: Custom KKM (minimum passing score)
     * - duration_minutes: Time limit for exam
     * - is_published: Whether exam is available to employees
     */
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('skill_id');
            $table->string('title')->comment('Exam title e.g., CMM Level 2 Upgrade Test');
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('target_level')->comment('Target competency level: 1-4');
            $table->unsignedTinyInteger('passing_score')->default(70)->comment('KKM - Kriteria Ketuntasan Minimal (0-100)');
            $table->unsignedInteger('duration_minutes')->default(60)->comment('Time limit in minutes');
            $table->boolean('is_published')->default(false);
            $table->enum('status', ['draft', 'active', 'archived'])->default('draft');
            $table->timestamps();

            $table->foreign('skill_id')->references('id')->on('skills')->onDelete('cascade');
            $table->index('skill_id');
            $table->index('is_published');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
