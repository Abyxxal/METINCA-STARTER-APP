<?php

namespace Tests\Concerns;

use App\Models\Department;
use App\Models\Division;
use App\Models\Employee;
use App\Models\EmployeeCompetency;
use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\Position;
use App\Models\Question;
use App\Models\Skill;
use App\Models\User;

/**
 * Builds the shared data graph used by the CBT feature tests.
 *
 * Structure: Department > Division > Position > Employee (+ linked User),
 * Skill, MC-only exam (4 x multiple_choice), essay exam (1 x essay),
 * and a Level 1 competency so the employee can take Level 2 exams.
 */
trait BuildsCbtScenario
{
    private string $testNik = 'TST001';

    /**
     * Build the base CBT scenario and return every created entity.
     */
    protected function buildCbtScenario(): array
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $manager = User::factory()->create(['role' => 'manager']);

        $department = Department::create(['name' => 'Dept Test']);
        $division = Division::create(['department_id' => $department->id, 'name' => 'Div Test']);
        $position = Position::create(['division_id' => $division->id, 'name' => 'Pos Test']);

        $employee = Employee::create([
            'nik' => $this->testNik,
            'name' => 'Test Employee',
            'email' => 'test.employee@example.com',
            'department_id' => $department->id,
            'division_id' => $division->id,
            'position_id' => $position->id,
            'status' => 'Aktif',
        ]);

        $employeeUser = User::factory()->create([
            'role' => 'user',
            'employee_nik' => $employee->nik,
        ]);

        $skill = Skill::create([
            'code' => 'TST-SKILL',
            'name' => 'Test Skill',
            'is_active' => true,
        ]);

        $mcQuestions = collect();
        for ($i = 1; $i <= 4; $i++) {
            $mcQuestions->push(Question::create([
                'question_set_id' => 'QS-MC-TEST',
                'set_title' => 'MC Set Level 2',
                'skill_id' => $skill->id,
                'for_level' => 2,
                'question_text' => 'MC question '.$i.'?',
                'type' => 'multiple_choice',
                'options' => ['A' => 'Option A', 'B' => 'Option B', 'C' => 'Option C', 'D' => 'Option D'],
                'correct_answer' => 'A',
                'default_weight' => 1,
                'status' => 'active',
            ]));
        }

        $mcExam = Exam::create([
            'skill_id' => $skill->id,
            'title' => 'MC Exam Level 2',
            'target_level' => 2,
            'passing_score' => 70,
            'duration_minutes' => 60,
            'is_published' => true,
            'status' => 'active',
        ]);
        $mcQuestionIds = $mcQuestions->pluck('id')->all();
        $mcExam->attachQuestionsWithSnapshot($mcQuestionIds, array_fill_keys($mcQuestionIds, 1));

        $essayQuestion = Question::create([
            'question_set_id' => 'QS-ESS-TEST',
            'set_title' => 'Essay Set Level 2',
            'skill_id' => $skill->id,
            'for_level' => 2,
            'question_text' => 'Explain the process?',
            'type' => 'essay',
            'default_weight' => 100,
            'status' => 'active',
        ]);

        $essayExam = Exam::create([
            'skill_id' => $skill->id,
            'title' => 'Essay Exam Level 2',
            'target_level' => 2,
            'passing_score' => 70,
            'duration_minutes' => 60,
            'is_published' => true,
            'status' => 'active',
        ]);
        $essayExam->attachQuestionsWithSnapshot([$essayQuestion->id], [$essayQuestion->id => 100]);

        $competency = EmployeeCompetency::create([
            'employee_nik' => $employee->nik,
            'skill_id' => $skill->id,
            'level' => 1,
            'verified_by' => $admin->id,
            'verified_at' => now(),
        ]);

        return compact(
            'admin',
            'manager',
            'department',
            'division',
            'position',
            'employee',
            'employeeUser',
            'skill',
            'mcQuestions',
            'mcExam',
            'essayQuestion',
            'essayExam',
            'competency'
        );
    }

    /**
     * Create an additional employee (with a unique NIK) in the given division.
     */
    protected function makeEmployee(string $nik, Division $division, string $status = 'Aktif'): Employee
    {
        $position = Position::where('division_id', $division->id)->first()
            ?? Position::create(['division_id' => $division->id, 'name' => 'Pos '.$nik]);

        return Employee::create([
            'nik' => $nik,
            'name' => 'Employee '.$nik,
            'email' => strtolower($nik).'@test.com',
            'department_id' => $division->department_id,
            'division_id' => $division->id,
            'position_id' => $position->id,
            'status' => $status,
        ]);
    }

    /**
     * Link a fresh user account to an employee.
     */
    protected function makeEmployeeUser(Employee $employee): User
    {
        return User::factory()->create([
            'role' => 'user',
            'employee_nik' => $employee->nik,
        ]);
    }

    /**
     * Create a competency record for an employee on a skill.
     */
    protected function makeCompetency(Employee $employee, Skill $skill, int $level, ?int $verifiedBy = null): EmployeeCompetency
    {
        return EmployeeCompetency::create([
            'employee_nik' => $employee->nik,
            'skill_id' => $skill->id,
            'level' => $level,
            'verified_by' => $verifiedBy,
            'verified_at' => now(),
        ]);
    }

    /**
     * Create an exam session with the given status.
     */
    protected function makeSession(Exam $exam, Employee $employee, string $status, array $overrides = []): ExamSession
    {
        return ExamSession::create(array_merge([
            'exam_id' => $exam->id,
            'employee_nik' => $employee->nik,
            'status' => $status,
        ], $overrides));
    }

    /**
     * Create a question set (a group of questions sharing a set id/title).
     *
     * @return \Illuminate\Support\Collection<int, Question>
     */
    protected function makeQuestionSet(Skill $skill, int $forLevel, string $setId, string $setTitle, int $count): \Illuminate\Support\Collection
    {
        $questions = collect();
        for ($i = 1; $i <= $count; $i++) {
            $questions->push(Question::create([
                'question_set_id' => $setId,
                'set_title' => $setTitle,
                'skill_id' => $skill->id,
                'for_level' => $forLevel,
                'question_text' => "{$setTitle} - Q{$i}?",
                'type' => 'multiple_choice',
                'options' => ['A' => 'Option A', 'B' => 'Option B', 'C' => 'Option C', 'D' => 'Option D'],
                'correct_answer' => 'A',
                'default_weight' => 1,
                'status' => 'active',
            ]));
        }

        return $questions;
    }
}
