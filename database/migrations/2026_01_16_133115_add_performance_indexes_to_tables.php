<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Menambahkan indexes untuk optimasi query performance
     */
    public function up(): void
    {
        // Indexes untuk tabel exams
        Schema::table('exams', function (Blueprint $table) {
            $table->index('skill_id', 'idx_exams_skill_id');
            $table->index('is_published', 'idx_exams_is_published');
            $table->index(['skill_id', 'is_published'], 'idx_exams_skill_published');
            $table->index('target_level', 'idx_exams_target_level');
        });

        // Indexes untuk tabel exam_sessions
        Schema::table('exam_sessions', function (Blueprint $table) {
            $table->index('employee_nik', 'idx_exam_sessions_employee_nik');
            $table->index('exam_id', 'idx_exam_sessions_exam_id');
            $table->index('status', 'idx_exam_sessions_status');
            $table->index(['employee_nik', 'status'], 'idx_exam_sessions_employee_status');
            $table->index(['exam_id', 'status'], 'idx_exam_sessions_exam_status');
            $table->index('verified_by', 'idx_exam_sessions_verified_by');
            $table->index('submitted_at', 'idx_exam_sessions_submitted_at');
        });

        // Indexes untuk tabel employee_competencies
        Schema::table('employee_competencies', function (Blueprint $table) {
            $table->index('employee_nik', 'idx_employee_comp_employee_nik');
            $table->index('skill_id', 'idx_employee_comp_skill_id');
            $table->index(['employee_nik', 'skill_id'], 'idx_employee_comp_employee_skill');
            $table->index('current_level', 'idx_employee_comp_current_level');
        });

        // Indexes untuk tabel questions
        Schema::table('questions', function (Blueprint $table) {
            $table->index('skill_id', 'idx_questions_skill_id');
            $table->index('status', 'idx_questions_status');
            $table->index('question_set_id', 'idx_questions_set_id');
            $table->index(['skill_id', 'status'], 'idx_questions_skill_status');
            $table->index(['question_set_id', 'status'], 'idx_questions_set_status');
            $table->index('for_level', 'idx_questions_for_level');
            $table->index('type', 'idx_questions_type');
        });

        // Indexes untuk tabel employees
        Schema::table('employees', function (Blueprint $table) {
            $table->index('department_id', 'idx_employees_department_id');
            $table->index('division_id', 'idx_employees_division_id');
            $table->index('position_id', 'idx_employees_position_id');
            $table->index('status', 'idx_employees_status');
            $table->index(['department_id', 'status'], 'idx_employees_dept_status');
            $table->index(['division_id', 'status'], 'idx_employees_div_status');
        });

        // Indexes untuk tabel skills
        Schema::table('skills', function (Blueprint $table) {
            $table->index('division_id', 'idx_skills_division_id');
            $table->index('is_active', 'idx_skills_is_active');
            $table->index('status', 'idx_skills_status');
            $table->index(['division_id', 'is_active'], 'idx_skills_div_active');
        });

        // Indexes untuk tabel users
        Schema::table('users', function (Blueprint $table) {
            $table->index('employee_id', 'idx_users_employee_id');
            $table->index('role', 'idx_users_role');
        });

        // Indexes untuk tabel positions
        Schema::table('positions', function (Blueprint $table) {
            $table->index('division_id', 'idx_positions_division_id');
        });

        // Indexes untuk tabel divisions
        Schema::table('divisions', function (Blueprint $table) {
            $table->index('department_id', 'idx_divisions_department_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes dari exams
        Schema::table('exams', function (Blueprint $table) {
            $table->dropIndex('idx_exams_skill_id');
            $table->dropIndex('idx_exams_is_published');
            $table->dropIndex('idx_exams_skill_published');
            $table->dropIndex('idx_exams_target_level');
        });

        // Drop indexes dari exam_sessions
        Schema::table('exam_sessions', function (Blueprint $table) {
            $table->dropIndex('idx_exam_sessions_employee_nik');
            $table->dropIndex('idx_exam_sessions_exam_id');
            $table->dropIndex('idx_exam_sessions_status');
            $table->dropIndex('idx_exam_sessions_employee_status');
            $table->dropIndex('idx_exam_sessions_exam_status');
            $table->dropIndex('idx_exam_sessions_verified_by');
            $table->dropIndex('idx_exam_sessions_submitted_at');
        });

        // Drop indexes dari employee_competencies
        Schema::table('employee_competencies', function (Blueprint $table) {
            $table->dropIndex('idx_employee_comp_employee_nik');
            $table->dropIndex('idx_employee_comp_skill_id');
            $table->dropIndex('idx_employee_comp_employee_skill');
            $table->dropIndex('idx_employee_comp_current_level');
        });

        // Drop indexes dari questions
        Schema::table('questions', function (Blueprint $table) {
            $table->dropIndex('idx_questions_skill_id');
            $table->dropIndex('idx_questions_status');
            $table->dropIndex('idx_questions_set_id');
            $table->dropIndex('idx_questions_skill_status');
            $table->dropIndex('idx_questions_set_status');
            $table->dropIndex('idx_questions_for_level');
            $table->dropIndex('idx_questions_type');
        });

        // Drop indexes dari employees
        Schema::table('employees', function (Blueprint $table) {
            $table->dropIndex('idx_employees_department_id');
            $table->dropIndex('idx_employees_division_id');
            $table->dropIndex('idx_employees_position_id');
            $table->dropIndex('idx_employees_status');
            $table->dropIndex('idx_employees_dept_status');
            $table->dropIndex('idx_employees_div_status');
        });

        // Drop indexes dari skills
        Schema::table('skills', function (Blueprint $table) {
            $table->dropIndex('idx_skills_division_id');
            $table->dropIndex('idx_skills_is_active');
            $table->dropIndex('idx_skills_status');
            $table->dropIndex('idx_skills_div_active');
        });

        // Drop indexes dari users
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_employee_id');
            $table->dropIndex('idx_users_role');
        });

        // Drop indexes dari positions
        Schema::table('positions', function (Blueprint $table) {
            $table->dropIndex('idx_positions_division_id');
        });

        // Drop indexes dari divisions
        Schema::table('divisions', function (Blueprint $table) {
            $table->dropIndex('idx_divisions_department_id');
        });
    }
};
