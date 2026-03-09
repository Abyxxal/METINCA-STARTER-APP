<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: question_positions
     * Purpose: Many-to-many relationship between questions and positions
     * 
     * Logic:
     * - If a question has NO entries in this table → Universal (applies to ALL positions)
     * - If a question has entries → Only for those specific positions
     * 
     * Example:
     * - Question #1: No records → All positions can see it
     * - Question #2: position_id = 1,2 → Only QA Engineer & Staff Adm QA can see it
     * - Question #3: position_id = 3 → Only Foreman can see it
     */
    public function up(): void
    {
        Schema::create('question_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->foreignId('position_id')->constrained('positions')->onDelete('cascade');
            $table->timestamps();

            // Ensure no duplicate combinations
            $table->unique(['question_id', 'position_id']);
            
            // Performance indexes
            $table->index('question_id');
            $table->index('position_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_positions');
    }
};
