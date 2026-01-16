<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: exam_answers
     * Purpose: Store individual question answers for each exam session
     * Fields:
     * - exam_session_id: Which exam session
     * - question_id: Which question
     * - selected_answer: Employee's answer
     * - is_correct: Auto-calculated for MC questions
     * - score_earned: Points earned for this answer
     */
    public function up(): void
    {
        Schema::create('exam_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exam_session_id');
            $table->unsignedBigInteger('question_id');
            $table->text('selected_answer')->nullable()->comment('Employee answer');
            $table->boolean('is_correct')->default(false)->comment('Auto-calculated for MC');
            $table->unsignedInteger('score_earned')->default(0)->comment('Points earned');
            $table->timestamps();

            $table->foreign('exam_session_id')->references('id')->on('exam_sessions')->onDelete('cascade');
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
            $table->unique(['exam_session_id', 'question_id']);
            $table->index('exam_session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_answers');
    }
};
