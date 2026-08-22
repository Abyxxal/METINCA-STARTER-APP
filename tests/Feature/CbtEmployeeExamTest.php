<?php

namespace Tests\Feature;

use App\Models\ExamAnswer;
use App\Models\ExamSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsCbtScenario;
use Tests\TestCase;

class CbtEmployeeExamTest extends TestCase
{
    use BuildsCbtScenario, RefreshDatabase;

    private function startedSession(array $scenario): ExamSession
    {
        return $this->makeSession($scenario['mcExam'], $scenario['employee'], ExamSession::STATUS_STARTED, [
            'started_at' => now(),
        ]);
    }

    // ============================================
    // REGISTER
    // ============================================

    public function test_employee_can_register_for_published_exam(): void
    {
        $scenario = $this->buildCbtScenario();
        $employeeUser = $scenario['employeeUser'];
        $mcExam = $scenario['mcExam'];

        $this->actingAs($employeeUser)
            ->post(route('cbt.employee.register', $mcExam))
            ->assertRedirect();

        $this->assertDatabaseHas('exam_sessions', [
            'exam_id' => $mcExam->id,
            'employee_nik' => $scenario['employee']->nik,
            'status' => ExamSession::STATUS_ASSIGNED,
        ]);
    }

    public function test_employee_cannot_register_for_unpublished_exam(): void
    {
        $scenario = $this->buildCbtScenario();
        $employeeUser = $scenario['employeeUser'];
        $mcExam = $scenario['mcExam'];
        $mcExam->update(['is_published' => false]);

        $this->actingAs($employeeUser)
            ->post(route('cbt.employee.register', $mcExam))
            ->assertRedirect(route('cbt.employee.dashboard'))
            ->assertSessionHas('error');

        $this->assertDatabaseMissing('exam_sessions', [
            'exam_id' => $mcExam->id,
            'employee_nik' => $scenario['employee']->nik,
        ]);
    }

    public function test_employee_with_wrong_level_cannot_register(): void
    {
        $scenario = $this->buildCbtScenario();
        $employeeUser = $scenario['employeeUser'];
        $mcExam = $scenario['mcExam'];

        // Naikkan level karyawan menjadi 2 (sama dengan target exam level 2) -> tidak eligible.
        $scenario['competency']->update(['level' => 2]);

        $this->actingAs($employeeUser)
            ->post(route('cbt.employee.register', $mcExam))
            ->assertRedirect(route('cbt.employee.dashboard'))
            ->assertSessionHas('error');

        $this->assertDatabaseMissing('exam_sessions', [
            'exam_id' => $mcExam->id,
            'employee_nik' => $scenario['employee']->nik,
        ]);
    }

    public function test_register_does_not_create_duplicate_session(): void
    {
        $scenario = $this->buildCbtScenario();
        $employeeUser = $scenario['employeeUser'];
        $mcExam = $scenario['mcExam'];

        $this->actingAs($employeeUser)->post(route('cbt.employee.register', $mcExam))->assertRedirect();
        $this->actingAs($employeeUser)->post(route('cbt.employee.register', $mcExam))->assertRedirect();

        $this->assertEquals(1, ExamSession::where('exam_id', $mcExam->id)
            ->where('employee_nik', $scenario['employee']->nik)
            ->count());
    }

    // ============================================
    // START & TAKE
    // ============================================

    public function test_employee_can_start_exam(): void
    {
        $scenario = $this->buildCbtScenario();
        $employeeUser = $scenario['employeeUser'];
        $session = $this->makeSession($scenario['mcExam'], $scenario['employee'], ExamSession::STATUS_ASSIGNED);

        $this->actingAs($employeeUser)
            ->post(route('cbt.employee.start', $session))
            ->assertRedirect(route('cbt.employee.take', $session));

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_STARTED, $session->status);
        $this->assertNotNull($session->started_at);
    }

    public function test_take_exam_requires_started_status(): void
    {
        $scenario = $this->buildCbtScenario();
        $employeeUser = $scenario['employeeUser'];
        $session = $this->makeSession($scenario['mcExam'], $scenario['employee'], ExamSession::STATUS_ASSIGNED);

        $this->actingAs($employeeUser)
            ->get(route('cbt.employee.take', $session))
            ->assertRedirect(route('cbt.employee.dashboard'));
    }

    // ============================================
    // SAVE ANSWER
    // ============================================

    public function test_save_answer_grades_multiple_choice(): void
    {
        $scenario = $this->buildCbtScenario();
        $employeeUser = $scenario['employeeUser'];
        $session = $this->startedSession($scenario);
        $question = $scenario['mcQuestions']->first(); // correct_answer = A

        $this->actingAs($employeeUser)
            ->post(route('cbt.employee.save-answer', $session), [
                'question_id' => $question->id,
                'selected_answer' => 'A',
            ])
            ->assertJson(['success' => true]);

        $answer = ExamAnswer::where('exam_session_id', $session->id)
            ->where('question_id', $question->id)
            ->first();

        $this->assertNotNull($answer);
        $this->assertTrue($answer->is_correct);
        $this->assertEquals(1, $answer->score_earned);
    }

    public function test_save_answer_marks_wrong_mc_choice(): void
    {
        $scenario = $this->buildCbtScenario();
        $employeeUser = $scenario['employeeUser'];
        $session = $this->startedSession($scenario);
        $question = $scenario['mcQuestions']->first(); // correct_answer = A

        $this->actingAs($employeeUser)
            ->post(route('cbt.employee.save-answer', $session), [
                'question_id' => $question->id,
                'selected_answer' => 'C',
            ])
            ->assertJson(['success' => true]);

        $answer = ExamAnswer::where('exam_session_id', $session->id)
            ->where('question_id', $question->id)
            ->first();

        $this->assertNotNull($answer);
        $this->assertFalse($answer->is_correct);
        $this->assertEquals(0, $answer->score_earned);
    }

    // ============================================
    // SUBMIT
    // ============================================

    public function test_submit_mc_exam_auto_verifies_and_marks_pass(): void
    {
        $scenario = $this->buildCbtScenario();
        $employeeUser = $scenario['employeeUser'];
        $session = $this->startedSession($scenario);
        $answers = $scenario['mcQuestions']->mapWithKeys(fn ($q) => [$q->id => 'A'])->all();

        $this->actingAs($employeeUser)
            ->post(route('cbt.employee.submit', $session), ['answers' => $answers])
            ->assertRedirect(route('cbt.employee.result', $session))
            ->assertSessionHas('success');

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->status);
        $this->assertEquals(100, $session->score);
        $this->assertEquals(ExamSession::DECISION_PENDING, $session->manager_decision);
        $this->assertNotNull($session->verified_at);
    }

    public function test_submit_mc_exam_with_wrong_answers_marks_fail(): void
    {
        $scenario = $this->buildCbtScenario();
        $employeeUser = $scenario['employeeUser'];
        $session = $this->startedSession($scenario);
        $answers = $scenario['mcQuestions']->mapWithKeys(fn ($q) => [$q->id => 'D'])->all(); // semua salah

        $this->actingAs($employeeUser)
            ->post(route('cbt.employee.submit', $session), ['answers' => $answers])
            ->assertRedirect(route('cbt.employee.result', $session));

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_VERIFIED_FAIL, $session->status);
        $this->assertEquals(0, $session->score);
    }

    public function test_auto_submit_grades_mc_as_percentage(): void
    {
        $scenario = $this->buildCbtScenario();
        $employeeUser = $scenario['employeeUser'];
        $session = $this->startedSession($scenario);
        $questions = $scenario['mcQuestions'];

        // Jawab benar 3 dari 4 soal
        foreach ($questions->take(3) as $q) {
            $this->actingAs($employeeUser)
                ->post(route('cbt.employee.save-answer', $session), [
                    'question_id' => $q->id,
                    'selected_answer' => 'A',
                ]);
        }

        // Paksa waktu habis (durasi ujian 60 menit)
        $session->update(['started_at' => now()->subMinutes(65)]);

        $this->actingAs($employeeUser)
            ->get(route('cbt.employee.take', $session))
            ->assertRedirect(route('cbt.employee.result', $session));

        $session->refresh();
        $this->assertEquals(75, $session->score, 'Auto-submit harus menghitung persentase (3/4 x 100), bukan jumlah benar (3).');
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->status);
        $this->assertDatabaseCount('exam_answers', 4);
    }

    public function test_submit_essay_exam_requires_admin_verification(): void
    {
        $scenario = $this->buildCbtScenario();
        $employeeUser = $scenario['employeeUser'];
        $essayQuestion = $scenario['essayQuestion'];
        $session = $this->makeSession($scenario['essayExam'], $scenario['employee'], ExamSession::STATUS_STARTED, [
            'started_at' => now(),
        ]);

        $this->actingAs($employeeUser)
            ->post(route('cbt.employee.submit', $session), [
                'answers' => [$essayQuestion->id => 'Jawaban esai karyawan'],
            ])
            ->assertRedirect(route('cbt.employee.result', $session));

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_SUBMITTED, $session->status);
        $this->assertNull($session->verified_by);
        $this->assertEquals(0, $session->score);
    }

    public function test_cannot_submit_twice(): void
    {
        $scenario = $this->buildCbtScenario();
        $employeeUser = $scenario['employeeUser'];
        $session = $this->startedSession($scenario);
        $answers = $scenario['mcQuestions']->mapWithKeys(fn ($q) => [$q->id => 'A'])->all();

        $this->actingAs($employeeUser)->post(route('cbt.employee.submit', $session), ['answers' => $answers])->assertRedirect();
        $this->actingAs($employeeUser)->post(route('cbt.employee.submit', $session), ['answers' => $answers])->assertRedirect(route('cbt.employee.dashboard'));
    }

    // ============================================
    // OWNERSHIP / ACCESS
    // ============================================

    public function test_employee_cannot_access_other_employees_session(): void
    {
        $scenario = $this->buildCbtScenario();
        $emp2 = $this->makeEmployee('TST002', $scenario['division']);
        $emp2User = $this->makeEmployeeUser($emp2);
        $session = $this->makeSession($scenario['mcExam'], $emp2, ExamSession::STATUS_STARTED);

        $this->actingAs($scenario['employeeUser'])
            ->get(route('cbt.employee.show', $session))
            ->assertForbidden();
    }

    // ============================================
    // RESULT & DASHBOARD
    // ============================================

    public function test_result_page_shows_score(): void
    {
        $scenario = $this->buildCbtScenario();
        $employeeUser = $scenario['employeeUser'];
        $session = $this->makeSession($scenario['mcExam'], $scenario['employee'], ExamSession::STATUS_VERIFIED_PASS, [
            'score' => 85,
        ]);

        $this->actingAs($employeeUser)
            ->get(route('cbt.employee.result', $session))
            ->assertOk()
            ->assertSee('85');
    }

    public function test_employee_dashboard_lists_assigned_exams(): void
    {
        $scenario = $this->buildCbtScenario();
        $employeeUser = $scenario['employeeUser'];
        $this->makeSession($scenario['mcExam'], $scenario['employee'], ExamSession::STATUS_ASSIGNED);

        $this->actingAs($employeeUser)
            ->get(route('cbt.employee.dashboard'))
            ->assertOk()
            ->assertSee($scenario['mcExam']->title);
    }
}
