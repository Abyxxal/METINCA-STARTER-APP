<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: questions
     * Purpose: Store exam questions for CBT system
     * Fields:
     * - skill_id: Link to which skill this question tests
     * - for_level: Target competency level (1-4)
     * - question_text: The actual question
     * - type: Question type (multiple_choice, essay, true_false)
     * - options: JSON array of answer options for MC
     * - correct_answer: The correct answer key
     */
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('skill_id');
            $table->unsignedTinyInteger('for_level')->comment('Target level: 1=Novice, 2=Competent, 3=Proficient, 4=Expert');
            $table->text('question_text');
            $table->enum('type', ['multiple_choice', 'essay', 'true_false'])->default('multiple_choice');
            $table->json('options')->nullable()->comment('For MC: {"A": "...", "B": "...", "C": "...", "D": "..."}');
            $table->string('correct_answer')->nullable()->comment('For MC: A/B/C/D, For TF: true/false');
            $table->enum('status', ['active', 'inactive', 'draft'])->default('draft');
            $table->timestamps();

            $table->foreign('skill_id')->references('id')->on('skills')->onDelete('cascade');
            $table->index('skill_id');
            $table->index('for_level');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
