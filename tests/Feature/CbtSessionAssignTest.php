<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\ExamSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsCbtScenario;
use Tests\TestCase;

class CbtSessionAssignTest extends TestCase
{
    use BuildsCbtScenario, RefreshDatabase;

    private function storePayload(string $nik): array
    {
        return [
            'question_set_ids' => ['QS-MC-TEST'],
            'employee_niks' => [$nik],
            'duration_minutes' => 60,
            'passing_score' => 70,
            'deadline_at' => now()->addDays(3)->format('Y-m-d H:i:s'),
        ];
    }

    public function test_admin_can_assign_exam_via_question_set(): void
    {
        $scenario = $this->buildCbtScenario();
        $admin = $scenario['admin'];
        $employee = $scenario['employee'];

        $this->actingAs($admin)
            ->post(route('cbt.admin.sessions.store'), $this->storePayload($employee->nik))
            ->assertRedirect(route('cbt.admin.sessions.index'))
            ->assertSessionHas('success');

        $exam = Exam::where('title', 'MC Set Level 2')->latest()->first();
        $this->assertNotNull($exam, 'Ujian otomatis harus dibuat dari set soal.');
        $this->assertEquals(2, $exam->target_level);
        $this->assertEquals(4, $exam->questions()->count());
        $this->assertTrue($exam->is_published);
        $this->assertEquals('active', $exam->status);

        $this->assertDatabaseHas('exam_sessions', [
            'exam_id' => $exam->id,
            'employee_nik' => $employee->nik,
            'status' => ExamSession::STATUS_ASSIGNED,
        ]);
    }

    public function test_store_skips_employee_with_wrong_competency_level(): void
    {
        $scenario = $this->buildCbtScenario();
        $admin = $scenario['admin'];
        $employee = $scenario['employee'];

        // Karyawan kedua sudah level 2 (sama dengan target) -> tidak memenuhi syarat.
        $emp2 = $this->makeEmployee('TST002', $scenario['division']);
        $this->makeCompetency($emp2, $scenario['skill'], 2, $admin->id);

        $this->actingAs($admin)
            ->post(route('cbt.admin.sessions.store'), [
                'question_set_ids' => ['QS-MC-TEST'],
                'employee_niks' => [$employee->nik, $emp2->nik],
                'duration_minutes' => 60,
                'passing_score' => 70,
                'deadline_at' => now()->addDays(3)->format('Y-m-d H:i:s'),
            ])
            ->assertSessionHas('success')
            ->assertSessionHas('notEligibleList');

        $exam = Exam::where('title', 'MC Set Level 2')->latest()->first();
        $this->assertDatabaseHas('exam_sessions', [
            'exam_id' => $exam->id,
            'employee_nik' => $employee->nik,
        ]);
        $this->assertDatabaseMissing('exam_sessions', [
            'exam_id' => $exam->id,
            'employee_nik' => $emp2->nik,
        ]);
    }

    public function test_reassigning_same_set_reuses_existing_exam(): void
    {
        $scenario = $this->buildCbtScenario();
        $admin = $scenario['admin'];
        $employee = $scenario['employee'];
        $emp2 = $this->makeEmployee('TST002', $scenario['division']);
        $this->makeCompetency($emp2, $scenario['skill'], 1, $admin->id);

        $this->actingAs($admin)->post(route('cbt.admin.sessions.store'), $this->storePayload($employee->nik))->assertSessionHas('success');
        $this->actingAs($admin)->post(route('cbt.admin.sessions.store'), $this->storePayload($emp2->nik))->assertSessionHas('success');

        $exams = Exam::where('title', 'MC Set Level 2')->get();
        $this->assertCount(1, $exams, 'Assign ulang set yang sama harus memakai exam yang sudah ada, bukan bikin duplikat.');
        $this->assertEquals(2, ExamSession::where('exam_id', $exams->first()->id)->count());
    }

    public function test_store_rejects_mixed_exam(): void
    {
        $scenario = $this->buildCbtScenario();
        $admin = $scenario['admin'];
        $employee = $scenario['employee'];

        $this->actingAs($admin)
            ->post(route('cbt.admin.sessions.store'), [
                'question_set_ids' => ['QS-MC-TEST', 'QS-ESS-TEST'],
                'employee_niks' => [$employee->nik],
                'duration_minutes' => 60,
                'passing_score' => 70,
                'deadline_at' => now()->addDays(3)->format('Y-m-d H:i:s'),
            ])
            ->assertSessionHas('error');

        $this->assertEquals(2, Exam::count(), 'Ujian campuran tidak boleh tercipta.');
    }

    // ============================================
    // BULK ASSIGN BY DIVISION
    // ============================================

    public function test_bulk_assign_by_division_assigns_only_active_eligible_employees(): void
    {
        $scenario = $this->buildCbtScenario();
        $admin = $scenario['admin'];
        $skill = $scenario['skill'];
        $mcExam = $scenario['mcExam'];

        // TST001 (trait): Aktif + level 1 -> eligible
        // TST002: Aktif + level 1 -> eligible
        $emp2 = $this->makeEmployee('TST002', $scenario['division'], 'Aktif');
        $this->makeCompetency($emp2, $skill, 1, $admin->id);
        // TST003: Non-Aktif + level 1 -> harus TIDAK di-assign (filter status Aktif)
        $emp3 = $this->makeEmployee('TST003', $scenario['division'], 'Non-Aktif');
        $this->makeCompetency($emp3, $skill, 1, $admin->id);

        $this->actingAs($admin)
            ->post(route('cbt.admin.sessions.bulk-assign'), [
                'exam_id' => $mcExam->id,
                'division_id' => $scenario['division']->id,
                'deadline_at' => now()->addDays(3)->format('Y-m-d H:i:s'),
            ])
            ->assertRedirect(route('cbt.admin.sessions.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('exam_sessions', [
            'exam_id' => $mcExam->id,
            'employee_nik' => $scenario['employee']->nik,
            'status' => ExamSession::STATUS_ASSIGNED,
        ]);
        $this->assertDatabaseHas('exam_sessions', [
            'exam_id' => $mcExam->id,
            'employee_nik' => $emp2->nik,
            'status' => ExamSession::STATUS_ASSIGNED,
        ]);
        $this->assertDatabaseMissing('exam_sessions', [
            'exam_id' => $mcExam->id,
            'employee_nik' => $emp3->nik,
        ]);
    }

    public function test_bulk_assign_skips_employee_with_wrong_level(): void
    {
        $scenario = $this->buildCbtScenario();
        $admin = $scenario['admin'];
        $skill = $scenario['skill'];
        $mcExam = $scenario['mcExam'];

        $emp2 = $this->makeEmployee('TST002', $scenario['division'], 'Aktif');
        $this->makeCompetency($emp2, $skill, 2, $admin->id);

        $this->actingAs($admin)
            ->post(route('cbt.admin.sessions.bulk-assign'), [
                'exam_id' => $mcExam->id,
                'division_id' => $scenario['division']->id,
                'deadline_at' => now()->addDays(3)->format('Y-m-d H:i:s'),
            ])
            ->assertRedirect(route('cbt.admin.sessions.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('exam_sessions', [
            'exam_id' => $mcExam->id,
            'employee_nik' => $emp2->nik,
        ]);
    }

    public function test_bulk_assign_requires_exam_division_and_deadline(): void
    {
        $scenario = $this->buildCbtScenario();
        $admin = $scenario['admin'];

        $this->actingAs($admin)
            ->post(route('cbt.admin.sessions.bulk-assign'), [])
            ->assertSessionHasErrors(['exam_id', 'division_id', 'deadline_at']);
    }

    // ============================================
    // CANCEL
    // ============================================

    public function test_admin_can_cancel_assigned_session(): void
    {
        $scenario = $this->buildCbtScenario();
        $admin = $scenario['admin'];
        $session = $this->makeSession($scenario['mcExam'], $scenario['employee'], ExamSession::STATUS_ASSIGNED);

        $this->actingAs($admin)
            ->delete(route('cbt.admin.sessions.cancel', $session))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('exam_sessions', ['id' => $session->id]);
    }

    public function test_cannot_cancel_started_session(): void
    {
        $scenario = $this->buildCbtScenario();
        $admin = $scenario['admin'];
        $session = $this->makeSession($scenario['mcExam'], $scenario['employee'], ExamSession::STATUS_STARTED);

        $this->actingAs($admin)
            ->delete(route('cbt.admin.sessions.cancel', $session))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('exam_sessions', ['id' => $session->id]);
    }

    // ============================================
    // SESSION LIST TABS
    // ============================================

    public function test_session_index_has_ongoing_and_completed_tabs(): void
    {
        $scenario = $this->buildCbtScenario();
        $admin = $scenario['admin'];

        $this->makeSession($scenario['mcExam'], $scenario['employee'], ExamSession::STATUS_ASSIGNED);
        $this->makeSession($scenario['essayExam'], $scenario['employee'], ExamSession::STATUS_VERIFIED_PASS);

        $this->actingAs($admin)
            ->get(route('cbt.admin.sessions.index', ['tab' => 'berlangsung']))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('cbt.admin.sessions.index', ['tab' => 'selesai']))
            ->assertOk()
            ->assertSee($scenario['essayExam']->title);
    }
}
