<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manager_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_session_id')
                ->constrained('exam_sessions')
                ->cascadeOnDelete()
                ->unique();
            $table->enum('assessment_method', ['interview', 'observation', 'both'])->nullable();
            $table->enum('sop_understanding', ['memenuhi', 'perlu_perbaikan', 'tidak_memenuhi'])->nullable();
            $table->enum('competency_application', ['memenuhi', 'perlu_perbaikan', 'tidak_memenuhi'])->nullable();
            $table->enum('independence', ['memenuhi', 'perlu_perbaikan', 'tidak_memenuhi'])->nullable();
            $table->enum('problem_solving', ['memenuhi', 'perlu_perbaikan', 'tidak_memenuhi'])->nullable();
            $table->enum('readiness', ['memenuhi', 'perlu_perbaikan', 'tidak_memenuhi'])->nullable();
            $table->date('verification_date')->nullable();
            $table->text('manager_notes')->nullable();
            $table->text('follow_up_recommendation')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manager_assessments');
    }
};
