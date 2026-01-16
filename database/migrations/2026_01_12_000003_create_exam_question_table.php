<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: exam_question (Pivot Table)
     * Purpose: Link questions to exams with custom weights
     * Fields:
     * - exam_id: Which exam
     * - question_id: Which question
     * - weight: Points for this question (supports custom weighting)
     * - order: Display order in exam
     */
    public function up(): void
    {
        Schema::create('exam_question', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exam_id');
            $table->unsignedBigInteger('question_id');
            $table->unsignedInteger('weight')->default(20)->comment('Points for this question');
            $table->unsignedTinyInteger('order')->default(0)->comment('Display order in exam');
            $table->timestamps();

            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('cascade');
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
            $table->unique(['exam_id', 'question_id']);
            $table->index(['exam_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_question');
    }
};
