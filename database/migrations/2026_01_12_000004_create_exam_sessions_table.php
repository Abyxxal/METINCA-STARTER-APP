<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: exam_sessions
     * Purpose: Track individual exam attempts by employees
     * Fields:
     * - exam_id: Which exam
     * - employee_nik: Which employee (FK to employees.nik)
     * - score: Total score earned (0-100)
     * - status: Workflow status (assigned -> started -> submitted -> verified)
     * - verified_by: Admin who verified the result
     * - admin_notes: Notes from admin verification
     */
    public function up(): void
    {
        Schema::create('exam_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exam_id');
            $table->string('employee_nik')->comment('FK to employees.nik');
            $table->unsignedSmallInteger('score')->nullable()->comment('Total score (0-100)');
            $table->enum('status', ['assigned', 'started', 'submitted', 'verified_pass', 'verified_fail'])
                ->default('assigned')
                ->comment('Workflow status');
            $table->unsignedBigInteger('verified_by')->nullable()->comment('Admin who verified');
            $table->text('admin_notes')->nullable()->comment('Notes from admin verification');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('cascade');
            $table->foreign('employee_nik')->references('nik')->on('employees')->onDelete('cascade');
            $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');
            $table->index(['exam_id', 'employee_nik']);
            $table->index('status');
            $table->index('verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_sessions');
    }
};
