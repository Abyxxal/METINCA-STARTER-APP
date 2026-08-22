<?php

namespace Tests\Feature;

use App\Models\EmployeeCompetency;
use App\Models\EmployeeCompetencyHistory;
use App\Models\ExamAnswer;
use App\Models\ExamSession;
use App\Models\ManagerAssessment;
use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsCbtScenario;
use Tests\Concerns\BuildsLevelFlow;
use Tests\TestCase;

/**
 * Alur peran (Supervisor/Admin, Karyawan, Manager), Matriks Kompetensi,
 * dan ketahanan data History.
 */
class CbtRoleMatrixHistoryTest extends TestCase
{
    use BuildsCbtScenario, BuildsLevelFlow, RefreshDatabase;

    private const THRESHOLD = 70;

    // ============================================
    // SUPERVISOR (ADMIN)
    // ============================================

    public function test_supervisor_creates_question_bank_for_levels_1_to_4(): void
    {
        $scenario = $this->buildCbtScenario();

        foreach ([1, 2, 3, 4] as $level) {
            $this->actingAs($scenario['admin'])
                ->post(route('cbt.admin.questions.store'), [
                    'skill_id' => $scenario['skill']->id,
                    'for_level' => $level,
                    'set_title' => "Set Uji Level {$level}",
                    'questions' => [
                        [
                            'question_text' => "Soal PG level {$level}",
                            'type' => 'multiple_choice',
                            'options' => ['A' => 'Pilihan A', 'B' => 'Pilihan B', 'C' => 'Pilihan C', 'D' => 'Pilihan D'],
                            'correct_answer' => 'A',
                            'default_weight' => 1,
                        ],
                    ],
                ])
                ->assertRedirect(route('cbt.admin.questions.index'))
                ->assertSessionHas('success');

            $this->assertDatabaseHas('questions', [
                'for_level' => $level,
                'question_text' => "Soal PG level {$level}",
                'status' => 'active',
            ]);
        }

        // Level di luar 1-4 tidak diperbolehkan
        $this->actingAs($scenario['admin'])
            ->post(route('cbt.admin.questions.store'), [
                'skill_id' => $scenario['skill']->id,
                'for_level' => 5,
                'set_title' => 'Set Ilegal',
                'questions' => [
                    ['question_text' => 'Soal ilegal level 5', 'type' => 'multiple_choice', 'correct_answer' => 'A'],
                ],
            ])
            ->assertSessionHasErrors('for_level');
    }

    public function test_supervisor_threshold_controls_pass_fail(): void
    {
        $scenario = $this->buildCbtScenario();
        $this->createMcQuestions($scenario['skill'], 4, 'A', 'QS-THOLD-CTRL', 2);

        // Supervisor menugaskan ujian dengan KKM 90
        $this->actingAs($scenario['admin'])
            ->post(route('cbt.admin.sessions.store'), [
                'duration_minutes' => 60,
                'passing_score' => 90,
                'deadline_at' => now()->addDays(7)->format('Y-m-d H:i:s'),
                'question_set_ids' => ['QS-THOLD-CTRL'],
                'employee_niks' => [$scenario['employee']->nik],
            ])
            ->assertRedirect(route('cbt.admin.sessions.index'))
            ->assertSessionHas('success');

        $session = ExamSession::where('employee_nik', $scenario['employee']->nik)
            ->latest('id')->first();
        $this->assertNotNull($session);
        $this->assertEquals(90, $session->exam->passing_score);

        // Karyawan menjawab 3 dari 4 benar (75) -> masih di bawah KKM 90
        $this->actingAs($scenario['employeeUser'])
            ->post(route('cbt.employee.start', $session))->assertRedirect();

        $this->actingAs($scenario['employeeUser'])
            ->post(route('cbt.employee.submit', $session), ['answers' => $this->autoAnswers($session->exam, 3)])
            ->assertRedirect();

        $session->refresh();
        $this->assertEquals(75, $session->score);
        $this->assertEquals(ExamSession::STATUS_VERIFIED_FAIL, $session->status);
    }

    // ============================================
    // KARYAWAN
    // ============================================

    public function test_employee_can_view_take_submit_and_see_result(): void
    {
        $scenario = $this->buildCbtScenario();
        $exam = $this->buildLevelExam($scenario, 2, 'mc', 'QS-EMP-FLOW');

        // Karyawan melihat daftar ujian yang tersedia
        $this->actingAs($scenario['employeeUser'])
            ->get(route('cbt.employee.dashboard'))
            ->assertOk();

        $session = $this->registerStartSubmit($scenario, $exam, $this->autoAnswers($exam, 4));
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->status);

        // Karyawan melihat hasil sesuai hak akses
        $this->actingAs($scenario['employeeUser'])
            ->get(route('cbt.employee.result', $session))
            ->assertOk()
            ->assertSee('100%')
            ->assertSee('LULUS - MENUNGGU PERSETUJUAN');
    }

    // ============================================
    // MANAGER - DETAIL PENILAIAN & HISTORY
    // ============================================

    public function test_manager_opens_assessment_detail_with_five_criteria(): void
    {
        $scenario = $this->buildCbtScenario();
        $exam = $this->buildLevelExam($scenario, 2, 'mc', 'QS-MGR-DETAIL');

        $session = $this->registerStartSubmit($scenario, $exam, $this->autoAnswers($exam, 3));

        $this->actingAs($scenario['manager'])
            ->get(route('cbt.admin.sessions.assessment', $session))
            ->assertOk()
            ->assertSee('Pemahaman dan penerapan SOP')
            ->assertSee('Kemampuan menerapkan kompetensi di tempat kerja')
            ->assertSee('Kemandirian dalam menjalankan pekerjaan')
            ->assertSee('Kemampuan menyelesaikan masalah')
            ->assertSee('Kesiapan menjalankan tanggung jawab level berikutnya');
    }

    public function test_approval_history_stores_full_record(): void
    {
        $scenario = $this->buildCbtScenario();
        $exam = $this->buildLevelExam($scenario, 2, 'mc', 'QS-HIST-FULL');

        $session = $this->registerStartSubmit($scenario, $exam, $this->autoAnswers($exam, 3));

        $this->managerApprove($scenario, $session, [
            'assessment_method' => 'both',
            'verification_date' => now()->format('Y-m-d'),
            'manager_notes' => 'Sesuai hasil wawancara dan observasi.',
        ])->assertSessionHas('success');

        $session->refresh();

        // Keputusan Manager + nilai ujian tersimpan pada sesi
        $this->assertEquals(ExamSession::STATUS_APPROVED, $session->status);
        $this->assertEquals(ExamSession::DECISION_APPROVED, $session->manager_decision);
        $this->assertEquals(75, $session->score);
        $this->assertEquals($scenario['manager']->id, $session->decided_by);
        $this->assertNotNull($session->decided_at);
        $this->assertEquals('Sesuai hasil wawancara dan observasi.', $session->manager_notes);

        // Penilaian 5 kriteria tersimpan
        $assessment = $this->assessmentOf($session);
        $this->assertEquals('both', $assessment->assessment_method);
        $this->assertEquals('memenuhi', $assessment->sop_understanding);
        $this->assertEquals('memenuhi', $assessment->competency_application);
        $this->assertEquals('memenuhi', $assessment->independence);
        $this->assertEquals('memenuhi', $assessment->problem_solving);
        $this->assertEquals('memenuhi', $assessment->readiness);
        $this->assertEquals($scenario['manager']->id, $assessment->created_by);
        $this->assertNotNull($assessment->verification_date);

        // Riwayat kenaikan level tersimpan
        $history = EmployeeCompetencyHistory::where('exam_session_id', $session->id)->firstOrFail();
        $this->assertEquals(1, $history->previous_level);
        $this->assertEquals(2, $history->new_level);
        $this->assertEquals('up', $history->change_type);
        $this->assertEquals('exam_pass', $history->change_source);

        // Terlihat di Riwayat Approval dengan status Disetujui
        $this->actingAs($scenario['manager'])
            ->get(route('cbt.admin.sessions.pending-approval', ['tab' => 'history']))
            ->assertOk()
            ->assertSee($scenario['employee']->name)
            ->assertSee('Disetujui');
    }

    // ============================================
    // MATRIKS KOMPETENSI
    // ============================================

    public function test_matrix_updates_on_approve_but_not_on_reject(): void
    {
        $scenario = $this->buildCbtScenario();
        $this->linkSkillToDivision($scenario);

        // Karyawan B (level 1) untuk skenario ditolak
        $employeeB = $this->makeEmployee('TST002', $scenario['division']);
        $employeeBUser = $this->makeEmployeeUser($employeeB);
        $this->makeCompetency($employeeB, $scenario['skill'], 1, $scenario['admin']->id);

        // Karyawan A disetujui, Karyawan B ditolak (dua ujian berbeda)
        $examA = $this->buildLevelExam($scenario, 2, 'mc', 'QS-MAT-A');
        $sessionA = $this->registerStartSubmit($scenario, $examA, $this->autoAnswers($examA, 3));
        $this->managerApprove($scenario, $sessionA)->assertSessionHas('success');

        $examB = $this->buildLevelExam($scenario, 2, 'mc', 'QS-MAT-B');
        $scenarioB = $scenario;
        $scenarioB['employee'] = $employeeB;
        $scenarioB['employeeUser'] = $employeeBUser;
        $sessionB = $this->registerStartSubmit($scenarioB, $examB, $this->autoAnswers($examB, 3));
        $this->managerReject($scenarioB, $sessionB);

        // Matriks menampilkan kedua karyawan
        $this->assertMatrixShowsEmployee($scenario, $scenario['employee']->name);
        $this->assertMatrixShowsEmployee($scenario, $employeeB->name);

        // Level A naik, level B tetap
        $this->assertEquals(2, EmployeeCompetency::where('employee_nik', $scenario['employee']->nik)->first()->level);
        $this->assertEquals(1, EmployeeCompetency::where('employee_nik', $employeeB->nik)->first()->level);
    }

    // ============================================
    // LEVEL 4 TIDAK NAIK KE LEVEL 5
    // ============================================

    public function test_level4_does_not_promote_to_level5(): void
    {
        $scenario = $this->buildCbtScenario();

        // Sistem hanya menerima level 1-4
        $this->actingAs($scenario['admin'])
            ->post(route('cbt.admin.questions.store'), [
                'skill_id' => $scenario['skill']->id,
                'for_level' => 5,
                'questions' => [['question_text' => 'Soal level 5', 'type' => 'multiple_choice', 'correct_answer' => 'A']],
            ])
            ->assertSessionHasErrors('for_level');

        // Karyawan level 4 tidak bisa mengikuti ujian level 4 (harus level 3),
        // dan tidak ada ujian level 5 yang bisa diambil.
        $this->setEmployeeLevel($scenario, 4);
        $exam = $this->buildLevelExam($scenario, 4, 'mc', 'QS-L4-NO5');

        $this->actingAs($scenario['employeeUser'])
            ->post(route('cbt.employee.register', $exam))
            ->assertRedirect(route('cbt.employee.dashboard'))
            ->assertSessionHas('error');

        $this->assertDatabaseMissing('exam_sessions', ['employee_nik' => $scenario['employee']->nik]);
        $this->assertEmployeeCompetencyLevel($scenario, 4);
    }

    // ============================================
    // HISTORY UJIAN TAHAN TERHADAP PENGHAPUSAN SOAL
    // ============================================

    public function test_exam_history_survives_bank_question_deletion(): void
    {
        $scenario = $this->buildCbtScenario();
        $exam = $this->buildLevelExam($scenario, 2, 'mc', 'QS-SNAP-DEL');

        $session = $this->registerStartSubmit($scenario, $exam, $this->autoAnswers($exam, 4));
        $this->assertEquals(100, $session->score);

        $question = $exam->questions->first();
        $snapshotText = $question->question_text;
        $questionId = $question->id;

        // Supervisor menghapus soal dari Bank Soal
        $this->actingAs($scenario['admin'])
            ->delete(route('cbt.admin.questions.destroy', $question))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertNull(Question::find($questionId));

        // Snapshot soal tetap tersimpan di pivot ujian
        $this->assertDatabaseHas('exam_question', [
            'exam_id' => $exam->id,
            'question_id' => $questionId,
            'question_text' => $snapshotText,
        ]);

        // Jawaban karyawan tetap tersimpan
        $this->assertDatabaseHas('exam_answers', [
            'exam_session_id' => $session->id,
            'question_id' => $questionId,
        ]);

        // Nilai dan status tetap utuh
        $session->refresh();
        $this->assertEquals(100, $session->score);
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->status);

        // Halaman history admin tetap dapat dibuka
        $this->actingAs($scenario['admin'])
            ->get(route('cbt.admin.sessions.show', $session))
            ->assertOk();
    }
}
