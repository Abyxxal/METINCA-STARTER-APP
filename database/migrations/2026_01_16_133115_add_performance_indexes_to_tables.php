<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Menambahkan indexes untuk optimasi query performance
     */
    public function up(): void
    {
        $indexes = [
            // exams
            "CREATE INDEX idx_exams_skill_id ON exams(skill_id)",
            "CREATE INDEX idx_exams_is_published ON exams(is_published)",
            "CREATE INDEX idx_exams_skill_published ON exams(skill_id, is_published)",
            "CREATE INDEX idx_exams_target_level ON exams(target_level)",
            
            // exam_sessions
            "CREATE INDEX idx_exam_sessions_employee_nik ON exam_sessions(employee_nik)",
            "CREATE INDEX idx_exam_sessions_exam_id ON exam_sessions(exam_id)",
            "CREATE INDEX idx_exam_sessions_status ON exam_sessions(status)",
            "CREATE INDEX idx_exam_sessions_employee_status ON exam_sessions(employee_nik, status)",
            "CREATE INDEX idx_exam_sessions_exam_status ON exam_sessions(exam_id, status)",
            "CREATE INDEX idx_exam_sessions_verified_by ON exam_sessions(verified_by)",
            "CREATE INDEX idx_exam_sessions_submitted_at ON exam_sessions(submitted_at)",
            
            // employee_competencies
            "CREATE INDEX idx_employee_comp_employee_nik ON employee_competencies(employee_nik)",
            "CREATE INDEX idx_employee_comp_skill_id ON employee_competencies(skill_id)",
            "CREATE INDEX idx_employee_comp_employee_skill ON employee_competencies(employee_nik, skill_id)",
            "CREATE INDEX idx_employee_comp_level ON employee_competencies(level)",
            
            // questions
            "CREATE INDEX idx_questions_skill_id ON questions(skill_id)",
            "CREATE INDEX idx_questions_status ON questions(status)",
            "CREATE INDEX idx_questions_set_id ON questions(question_set_id)",
            "CREATE INDEX idx_questions_skill_status ON questions(skill_id, status)",
            "CREATE INDEX idx_questions_set_status ON questions(question_set_id, status)",
            "CREATE INDEX idx_questions_for_level ON questions(for_level)",
            "CREATE INDEX idx_questions_type ON questions(type)",
            
            // employees
            "CREATE INDEX idx_employees_department_id ON employees(department_id)",
            "CREATE INDEX idx_employees_division_id ON employees(division_id)",
            "CREATE INDEX idx_employees_position_id ON employees(position_id)",
            "CREATE INDEX idx_employees_status ON employees(status)",
            "CREATE INDEX idx_employees_dept_status ON employees(department_id, status)",
            "CREATE INDEX idx_employees_div_status ON employees(division_id, status)",
            
            // skills
            "CREATE INDEX idx_skills_division_id ON skills(division_id)",
            "CREATE INDEX idx_skills_is_active ON skills(is_active)",
            "CREATE INDEX idx_skills_status ON skills(status)",
            "CREATE INDEX idx_skills_div_active ON skills(division_id, is_active)",
            
            // users
            "CREATE INDEX idx_users_employee_id ON users(employee_id)",
            "CREATE INDEX idx_users_role ON users(role)",
            
            // positions
            "CREATE INDEX idx_positions_division_id ON positions(division_id)",
            
            // divisions
            "CREATE INDEX idx_divisions_department_id ON divisions(department_id)",
        ];

        foreach ($indexes as $sql) {
            try {
                DB::statement($sql);
            } catch (\Exception $e) {
                // Skip jika ada error (biasanya duplicate key)
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $indexes = [
            ['exams', 'idx_exams_skill_id'],
            ['exams', 'idx_exams_is_published'],
            ['exams', 'idx_exams_skill_published'],
            ['exams', 'idx_exams_target_level'],
            ['exam_sessions', 'idx_exam_sessions_employee_nik'],
            ['exam_sessions', 'idx_exam_sessions_exam_id'],
            ['exam_sessions', 'idx_exam_sessions_status'],
            ['exam_sessions', 'idx_exam_sessions_employee_status'],
            ['exam_sessions', 'idx_exam_sessions_exam_status'],
            ['exam_sessions', 'idx_exam_sessions_verified_by'],
            ['exam_sessions', 'idx_exam_sessions_submitted_at'],
            ['employee_competencies', 'idx_employee_comp_employee_nik'],
            ['employee_competencies', 'idx_employee_comp_skill_id'],
            ['employee_competencies', 'idx_employee_comp_employee_skill'],
            ['employee_competencies', 'idx_employee_comp_level'],
            ['questions', 'idx_questions_skill_id'],
            ['questions', 'idx_questions_status'],
            ['questions', 'idx_questions_set_id'],
            ['questions', 'idx_questions_skill_status'],
            ['questions', 'idx_questions_set_status'],
            ['questions', 'idx_questions_for_level'],
            ['questions', 'idx_questions_type'],
            ['employees', 'idx_employees_department_id'],
            ['employees', 'idx_employees_division_id'],
            ['employees', 'idx_employees_position_id'],
            ['employees', 'idx_employees_status'],
            ['employees', 'idx_employees_dept_status'],
            ['employees', 'idx_employees_div_status'],
            ['skills', 'idx_skills_division_id'],
            ['skills', 'idx_skills_is_active'],
            ['skills', 'idx_skills_status'],
            ['skills', 'idx_skills_div_active'],
            ['users', 'idx_users_employee_id'],
            ['users', 'idx_users_role'],
            ['positions', 'idx_positions_division_id'],
            ['divisions', 'idx_divisions_department_id'],
        ];

        foreach ($indexes as [$table, $index]) {
            try {
                DB::statement("ALTER TABLE `{$table}` DROP INDEX `{$index}`");
            } catch (\Exception $e) {
                // Skip jika index tidak ada atau digunakan oleh FK
            }
        }
    }
};
