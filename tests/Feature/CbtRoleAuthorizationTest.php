<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\ManagerAssessment;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsCbtScenario;
use Tests\Concerns\BuildsLevelFlow;
use Tests\TestCase;

/**
 * Pengujian hak akses (role/authorization) berbasis route & middleware.
 *
 * Matriks yang diuji (sesuai dokumen aturan aplikasi):
 *   | Role       | Fitur Admin/Supervisor | Approval Manager | Fitur Karyawan |
 *   | Supervisor | Boleh                  | TIDAK BOLEH      | Sesuai implementasi |
 *   | Manager    | Boleh                  | Boleh            | Sesuai implementasi |
 *   | Karyawan   | TIDAK BOLEH            | TIDAK BOLEH      | Boleh          |
 *
 * Mekanisme authorization aplikasi:
 *   - middleware 'is.admin'  -> role admin (Supervisor) ATAU manager.
 *   - middleware 'is.manager'-> hanya role manager (fitur Approval Manager).
 *   - middleware 'is.user'   -> hanya role user (Karyawan).
 *   - Tidak ada Gate/Policy; penolakan berupa redirect ke dashboard + flash error.
 *   - Menu "Persetujuan Level" hanya dirender bila user isManager().
 */
class CbtRoleAuthorizationTest extends TestCase
{
    use BuildsCbtScenario, BuildsLevelFlow, RefreshDatabase;

    /**
     * Daftar halaman Admin (GET) yang wajib bisa dibuka Supervisor & Manager.
     * Semua harus merespons 200 kecuali cbt.admin.dashboard (redirect).
     */
    private function adminPages(array $scenario): array
    {
        return [
            ['admin.users.index', []],
            ['master-data', []],
            ['departments.index', []],
            ['departments.show', ['id' => $scenario['department']->id]],
            ['employee.import.form', []],
            ['material-management', []],
            ['evaluation-and-exam', []],
            ['report-and-audit', []],
            ['settings', []],
            ['machining.monitoring.index', []],
            ['cbt.admin.questions.index', []],
            ['cbt.admin.questions.create', []],
            ['cbt.admin.exams.index', []],
            ['cbt.admin.exams.create', []],
            ['cbt.admin.sessions.index', []],
            ['cbt.admin.sessions.pending', []],
            ['cbt.admin.sessions.create', []],
            ['cbt.admin.competency-matrix', []],
            ['cbt.admin.division-skills.index', []],
            ['cbt.admin.employee-competencies.index', []],
        ];
    }

    /**
     * Buka semua halaman admin dan pastikan respons 200.
     */
    private function assertAllAdminPagesOk(array $scenario, User $user): void
    {
        foreach ($this->adminPages($scenario) as [$route, $params]) {
            $this->actingAs($user)->get(route($route, $params))->assertOk();
        }
    }

    /**
     * Karyawan membuka semua halaman admin -> selalu ditolak (redirect ke dashboard).
     */
    private function assertAllAdminPagesDeniedForEmployee(array $scenario, User $employeeUser): void
    {
        foreach ($this->adminPages($scenario) as [$route, $params]) {
            $this->actingAs($employeeUser)
                ->get(route($route, $params))
                ->assertRedirect(route('dashboard'));
        }
    }

    /**
     * Buat satu sesi ujian MC yang lulus otomatis (verified_pass) menunggu approval.
     */
    private function makePendingApprovalScenario(): array
    {
        $scenario = $this->buildCbtScenario();
        $exam = $this->buildLevelExam($scenario, 2, 'mc', 'QS-AUTH-AP', 70);
        $session = $this->registerStartSubmit($scenario, $exam, $this->autoAnswers($exam, 3));

        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->refresh()->status);

        $scenario['session'] = $session;

        return $scenario;
    }

    // ============================================
    // 1. TAMU (BELUM LOGIN)
    // ============================================

    public function test_guest_is_redirected_to_login_on_all_protected_areas(): void
    {
        $this->get(route('cbt.admin.questions.index'))->assertRedirect(route('login'));
        $this->get(route('cbt.admin.exams.index'))->assertRedirect(route('login'));
        $this->get(route('cbt.admin.sessions.index'))->assertRedirect(route('login'));
        $this->get(route('cbt.admin.sessions.pending-approval'))->assertRedirect(route('login'));
        $this->get(route('cbt.admin.sessions.approval-history'))->assertRedirect(route('login'));
        $this->get(route('cbt.employee.dashboard'))->assertRedirect(route('login'));
        $this->get(route('admin.users.index'))->assertRedirect(route('login'));
    }

    // ============================================
    // 2. SUPERVISOR - AKSES FITUR ADMIN
    // ============================================

    public function test_supervisor_can_open_all_admin_pages(): void
    {
        $scenario = $this->buildCbtScenario();

        $this->assertAllAdminPagesOk($scenario, $scenario['admin']);

        // Dashboard CBT Admin me-redirect ke daftar sesi.
        $this->actingAs($scenario['admin'])
            ->get(route('cbt.admin.dashboard'))
            ->assertRedirect(route('cbt.admin.sessions.index'));
    }

    public function test_employee_import_template_download_is_broken_for_all_admin_roles(): void
    {
        $scenario = $this->buildCbtScenario();

        // Defect aplikasi (bukan masalah role): paket maatwebsite/excel belum terpasang,
        // sehingga route download template Excel error 500 untuk semua role admin.
        $this->actingAs($scenario['admin'])
            ->get(route('employee.template'))
            ->assertServerError();

        $this->actingAs($scenario['manager'])
            ->get(route('employee.template'))
            ->assertServerError();

        // Karyawan tetap ditolak middleware is.admin (sebelum sampai ke controller).
        $this->actingAs($scenario['employeeUser'])
            ->get(route('employee.template'))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');
    }

    public function test_supervisor_can_see_admin_data_on_pages(): void
    {
        $scenario = $this->buildCbtScenario();

        // Siapkan satu set soal agar halaman Bank Soal memiliki data.
        Question::create([
            'question_set_id' => 'QS-AUTH-SEED',
            'set_title' => 'Auth Seed Set',
            'skill_id' => $scenario['skill']->id,
            'for_level' => 2,
            'question_text' => 'Seed question?',
            'type' => 'multiple_choice',
            'options' => ['A' => 'a', 'B' => 'b'],
            'correct_answer' => 'A',
            'default_weight' => 1,
            'status' => 'active',
        ]);

        $this->actingAs($scenario['admin'])
            ->get(route('cbt.admin.questions.index'))
            ->assertOk()
            ->assertSee('Auth Seed Set');

        $this->actingAs($scenario['admin'])
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee($scenario['manager']->name);

        // Karyawan (role user) tampil pada tab "employee".
        $this->actingAs($scenario['admin'])
            ->get(route('admin.users.index', ['tab' => 'employee']))
            ->assertOk()
            ->assertSee($scenario['employeeUser']->name);
    }

    public function test_supervisor_can_update_user_role(): void
    {
        $scenario = $this->buildCbtScenario();

        $this->actingAs($scenario['admin'])
            ->put(route('admin.users.update-role', $scenario['employeeUser']), ['role' => 'manager'])
            ->assertRedirect(route('admin.users.index', ['tab' => 'staff']));

        $this->assertDatabaseHas('users', [
            'id' => $scenario['employeeUser']->id,
            'role' => 'manager',
        ]);
    }

    public function test_supervisor_can_create_question_set_multiple_choice(): void
    {
        $scenario = $this->buildCbtScenario();

        $this->actingAs($scenario['admin'])
            ->post(route('cbt.admin.questions.store'), [
                'skill_id' => $scenario['skill']->id,
                'for_level' => 2,
                'set_title' => 'Auth PG Set',
                'questions' => [
                    [
                        'question_text' => 'Auth PG 1?',
                        'type' => 'multiple_choice',
                        'options' => ['A' => 'A1', 'B' => 'B1', 'C' => 'C1', 'D' => 'D1'],
                        'correct_answer' => 'A',
                        'default_weight' => 1,
                    ],
                    [
                        'question_text' => 'Auth PG 2?',
                        'type' => 'multiple_choice',
                        'options' => ['A' => 'A2', 'B' => 'B2', 'C' => 'C2', 'D' => 'D2'],
                        'correct_answer' => 'B',
                        'default_weight' => 1,
                    ],
                ],
            ])
            ->assertRedirect(route('cbt.admin.questions.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('questions', [
            'set_title' => 'Auth PG Set',
            'for_level' => 2,
            'type' => 'multiple_choice',
            'status' => 'active',
        ]);
        $this->assertSame(2, Question::where('set_title', 'Auth PG Set')->count());
    }

    public function test_supervisor_can_create_question_set_true_false(): void
    {
        $scenario = $this->buildCbtScenario();

        $this->actingAs($scenario['admin'])
            ->post(route('cbt.admin.questions.store'), [
                'skill_id' => $scenario['skill']->id,
                'for_level' => 3,
                'set_title' => 'Auth TF Set',
                'questions' => [
                    ['question_text' => 'Auth TF 1?', 'type' => 'true_false', 'correct_answer' => 'A', 'default_weight' => 1],
                    ['question_text' => 'Auth TF 2?', 'type' => 'true_false', 'correct_answer' => 'B', 'default_weight' => 1],
                ],
            ])
            ->assertRedirect(route('cbt.admin.questions.index'));

        $this->assertDatabaseHas('questions', [
            'set_title' => 'Auth TF Set',
            'for_level' => 3,
            'type' => 'true_false',
        ]);
        $this->assertSame(2, Question::where('set_title', 'Auth TF Set')->count());
    }

    public function test_supervisor_can_create_question_set_essay_with_weight_100(): void
    {
        $scenario = $this->buildCbtScenario();

        $this->actingAs($scenario['admin'])
            ->post(route('cbt.admin.questions.store'), [
                'skill_id' => $scenario['skill']->id,
                'for_level' => 4,
                'set_title' => 'Auth Essay Set',
                'questions' => [
                    ['question_text' => 'Auth Essay 1?', 'type' => 'essay', 'default_weight' => 60],
                    ['question_text' => 'Auth Essay 2?', 'type' => 'essay', 'default_weight' => 40],
                ],
            ])
            ->assertRedirect(route('cbt.admin.questions.index'));

        $this->assertDatabaseHas('questions', [
            'set_title' => 'Auth Essay Set',
            'for_level' => 4,
            'type' => 'essay',
        ]);
        $this->assertSame(100, (int) Question::where('set_title', 'Auth Essay Set')->sum('default_weight'));
    }

    public function test_supervisor_can_update_question_set_title_questions_and_essay_weight(): void
    {
        $scenario = $this->buildCbtScenario();
        $skill = $scenario['skill'];

        $q1 = Question::create([
            'question_set_id' => 'QS-AUTH-UPD',
            'set_title' => 'Auth Update Set',
            'skill_id' => $skill->id,
            'for_level' => 2,
            'question_text' => 'Old text?',
            'type' => 'multiple_choice',
            'options' => ['A' => 'a', 'B' => 'b'],
            'correct_answer' => 'A',
            'default_weight' => 1,
            'status' => 'active',
        ]);

        $this->actingAs($scenario['admin'])
            ->put(route('cbt.admin.questions.update-set', ['questionSetId' => 'QS-AUTH-UPD']), [
                'set_title' => 'Auth Update Set V2',
                'skill_id' => $skill->id,
                'for_level' => 3,
                'status' => 'active',
                'questions' => [
                    [
                        'id' => $q1->id,
                        'question_text' => 'Updated text?',
                        'type' => 'multiple_choice',
                        'options' => ['A' => 'a', 'B' => 'b', 'C' => 'c', 'D' => 'd'],
                        'correct_answer' => 'C',
                        'default_weight' => 1,
                    ],
                    [
                        'question_text' => 'New question?',
                        'type' => 'multiple_choice',
                        'options' => ['A' => 'a', 'B' => 'b'],
                        'correct_answer' => 'A',
                        'default_weight' => 1,
                    ],
                ],
            ])
            ->assertRedirect(route('cbt.admin.questions.show-set', 'QS-AUTH-UPD'));

        $this->assertDatabaseHas('questions', [
            'id' => $q1->id,
            'set_title' => 'Auth Update Set V2',
            'for_level' => 3,
            'question_text' => 'Updated text?',
            'correct_answer' => 'C',
        ]);
        $this->assertDatabaseHas('questions', [
            'question_set_id' => 'QS-AUTH-UPD',
            'set_title' => 'Auth Update Set V2',
            'question_text' => 'New question?',
        ]);

        // Ubah bobot essay lewat update-set (60+40 -> 70+30).
        $e1 = Question::create([
            'question_set_id' => 'QS-AUTH-ESSW',
            'set_title' => 'Auth Essay Weight',
            'skill_id' => $skill->id,
            'for_level' => 2,
            'question_text' => 'Essay A?',
            'type' => 'essay',
            'default_weight' => 60,
            'status' => 'active',
        ]);
        Question::create([
            'question_set_id' => 'QS-AUTH-ESSW',
            'set_title' => 'Auth Essay Weight',
            'skill_id' => $skill->id,
            'for_level' => 2,
            'question_text' => 'Essay B?',
            'type' => 'essay',
            'default_weight' => 40,
            'status' => 'active',
        ]);

        $this->actingAs($scenario['admin'])
            ->put(route('cbt.admin.questions.update-set', ['questionSetId' => 'QS-AUTH-ESSW']), [
                'set_title' => 'Auth Essay Weight',
                'skill_id' => $skill->id,
                'for_level' => 2,
                'status' => 'active',
                'questions' => [
                    ['id' => $e1->id, 'question_text' => 'Essay A?', 'type' => 'essay', 'default_weight' => 70],
                    ['question_text' => 'Essay C?', 'type' => 'essay', 'default_weight' => 30],
                ],
            ])
            ->assertRedirect(route('cbt.admin.questions.show-set', 'QS-AUTH-ESSW'));

        $this->assertDatabaseHas('questions', ['id' => $e1->id, 'default_weight' => 70]);
        $this->assertDatabaseHas('questions', [
            'question_set_id' => 'QS-AUTH-ESSW',
            'question_text' => 'Essay C?',
            'default_weight' => 30,
        ]);
    }

    public function test_supervisor_can_update_single_question(): void
    {
        $scenario = $this->buildCbtScenario();
        $question = $scenario['mcQuestions']->first();

        $this->actingAs($scenario['admin'])
            ->put(route('cbt.admin.questions.update', $question), [
                'skill_id' => $scenario['skill']->id,
                'question_text' => 'Changed question?',
                'for_level' => 3,
                'type' => 'multiple_choice',
                'options' => ['A' => 'a', 'B' => 'b', 'C' => 'c', 'D' => 'd'],
                'correct_answer' => 'D',
                'default_weight' => 1,
                'status' => 'active',
            ])
            ->assertRedirect(route('cbt.admin.questions.show', $question));

        $this->assertDatabaseHas('questions', [
            'id' => $question->id,
            'question_text' => 'Changed question?',
            'for_level' => 3,
            'correct_answer' => 'D',
        ]);
    }

    public function test_supervisor_can_delete_single_question(): void
    {
        $scenario = $this->buildCbtScenario();
        $question = $scenario['mcQuestions']->first();

        $this->actingAs($scenario['admin'])
            ->delete(route('cbt.admin.questions.destroy', $question))
            ->assertRedirect(route('cbt.admin.questions.index'));

        $this->assertDatabaseMissing('questions', ['id' => $question->id]);
    }

    public function test_supervisor_can_delete_question_set(): void
    {
        $scenario = $this->buildCbtScenario();

        Question::create([
            'question_set_id' => 'QS-AUTH-DELSET',
            'set_title' => 'Auth Delete Set',
            'skill_id' => $scenario['skill']->id,
            'for_level' => 2,
            'question_text' => 'Delete me 1?',
            'type' => 'multiple_choice',
            'options' => ['A' => 'a', 'B' => 'b'],
            'correct_answer' => 'A',
            'default_weight' => 1,
            'status' => 'active',
        ]);
        Question::create([
            'question_set_id' => 'QS-AUTH-DELSET',
            'set_title' => 'Auth Delete Set',
            'skill_id' => $scenario['skill']->id,
            'for_level' => 2,
            'question_text' => 'Delete me 2?',
            'type' => 'multiple_choice',
            'options' => ['A' => 'a', 'B' => 'b'],
            'correct_answer' => 'B',
            'default_weight' => 1,
            'status' => 'active',
        ]);

        $this->actingAs($scenario['admin'])
            ->delete(route('cbt.admin.questions.destroy-set', ['questionSetId' => 'QS-AUTH-DELSET']))
            ->assertRedirect(route('cbt.admin.questions.index'));

        $this->assertSame(0, Question::where('question_set_id', 'QS-AUTH-DELSET')->count());
    }

    public function test_supervisor_can_create_and_publish_exam(): void
    {
        $scenario = $this->buildCbtScenario();
        $questions = $scenario['mcQuestions'];

        $this->actingAs($scenario['admin'])
            ->post(route('cbt.admin.exams.store'), [
                'skill_id' => $scenario['skill']->id,
                'title' => 'Auth Exam',
                'target_level' => 2,
                'passing_score' => 70,
                'duration_minutes' => 60,
                'is_published' => 1,
                'questions' => $questions->map(fn ($q) => ['id' => $q->id])->all(),
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $exam = Exam::where('title', 'Auth Exam')->firstOrFail();

        $this->assertTrue($exam->examQuestions()->whereIn('question_id', $questions->pluck('id'))->count() === 4);

        // Toggle publish: is_published true -> false.
        $this->actingAs($scenario['admin'])
            ->post(route('cbt.admin.exams.toggle-publish', $exam))
            ->assertRedirect();

        $this->assertFalse($exam->refresh()->is_published);
    }

    public function test_supervisor_can_assign_exam_to_employee(): void
    {
        $scenario = $this->buildCbtScenario();

        $this->actingAs($scenario['admin'])
            ->post(route('cbt.admin.sessions.store'), [
                'duration_minutes' => 60,
                'passing_score' => 70,
                'deadline_at' => now()->addDays(2)->format('Y-m-d H:i'),
                'question_set_ids' => ['QS-MC-TEST'],
                'employee_niks' => [$scenario['employee']->nik],
            ])
            ->assertRedirect(route('cbt.admin.sessions.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('exam_sessions', [
            'employee_nik' => $scenario['employee']->nik,
            'status' => ExamSession::STATUS_ASSIGNED,
        ]);
    }

    public function test_supervisor_can_verify_essay(): void
    {
        $scenario = $this->buildCbtScenario();
        $exam = $this->buildLevelExam($scenario, 2, 'essay', 'QS-AUTH-VERIFY', 70);

        $session = $this->registerStartSubmit($scenario, $exam, $this->essayTextAnswers($exam));

        $scores = array_combine($exam->questions->pluck('id')->all(), [18, 25, 22, 17, 5]);
        $this->gradeEssay($scenario, $session, $scores)->assertSessionHas('success');

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->status);
        $this->assertEquals(87, $session->score);
        $this->assertEquals($scenario['admin']->id, $session->verified_by);
    }

    // ============================================
    // 3. SUPERVISOR - DITOLAK AKSES APPROVAL MANAGER
    // ============================================

    public function test_supervisor_cannot_open_approval_pages(): void
    {
        $scenario = $this->makePendingApprovalScenario();

        $this->actingAs($scenario['admin'])
            ->get(route('cbt.admin.sessions.pending-approval'))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');

        $this->actingAs($scenario['admin'])
            ->get(route('cbt.admin.sessions.approval-history'))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');
    }

    public function test_supervisor_cannot_open_approval_detail(): void
    {
        $scenario = $this->makePendingApprovalScenario();

        $this->actingAs($scenario['admin'])
            ->get(route('cbt.admin.sessions.assessment', $scenario['session']))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');
    }

    public function test_supervisor_cannot_approve_or_reject_level_request(): void
    {
        $scenario = $this->makePendingApprovalScenario();
        $session = $scenario['session'];
        $employeeNik = $scenario['employee']->nik;

        // Coba approve langsung via URL.
        $this->actingAs($scenario['admin'])
            ->post(route('cbt.admin.sessions.approve-level', $session), [
                'assessment_method' => 'interview',
                'sop_understanding' => 'memenuhi',
                'competency_application' => 'memenuhi',
                'independence' => 'memenuhi',
                'problem_solving' => 'memenuhi',
                'readiness' => 'memenuhi',
                'manager_notes' => 'Percobaan akses oleh Supervisor.',
            ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');

        // Coba tolak langsung via URL.
        $this->actingAs($scenario['admin'])
            ->post(route('cbt.admin.sessions.reject-level', $session), ['manager_notes' => 'Percobaan akses oleh Supervisor.'])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');

        // Tidak ada data approval yang berubah.
        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->status);
        $this->assertEquals('pending', $session->manager_decision);
        $this->assertDatabaseMissing('manager_assessments', ['exam_session_id' => $session->id]);
        $this->assertSame(1, (int) $scenario['competency']->refresh()->level);
    }

    public function test_supervisor_cannot_change_existing_manager_decision(): void
    {
        $scenario = $this->makePendingApprovalScenario();
        $session = $scenario['session'];

        // Manager menyetujui lebih dulu.
        $this->managerApprove($scenario, $session)->assertSessionHas('success');
        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_APPROVED, $session->status);

        $decidedBy = $session->decided_by;

        // Supervisor mencoba mengubah keputusan yang sudah dibuat manager.
        $this->actingAs($scenario['admin'])
            ->post(route('cbt.admin.sessions.approve-level', $session), [
                'assessment_method' => 'interview',
                'manager_notes' => 'Ubah keputusan.',
            ])
            ->assertRedirect(route('dashboard'));

        $this->actingAs($scenario['admin'])
            ->post(route('cbt.admin.sessions.reject-level', $session), ['manager_notes' => 'Ubah keputusan.'])
            ->assertRedirect(route('dashboard'));

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_APPROVED, $session->status);
        $this->assertEquals(ExamSession::DECISION_APPROVED, $session->manager_decision);
        $this->assertEquals($decidedBy, $session->decided_by);
    }

    public function test_supervisor_approval_menu_is_hidden(): void
    {
        $scenario = $this->buildCbtScenario();

        $this->actingAs($scenario['admin'])
            ->get(route('cbt.admin.questions.index'))
            ->assertOk()
            ->assertDontSee('Persetujuan Level')
            ->assertDontSee(route('cbt.admin.sessions.pending-approval'));
    }

    // ============================================
    // 4. MANAGER - AKSES SELURUH FITUR ADMIN
    // ============================================

    public function test_manager_can_open_all_admin_pages(): void
    {
        $scenario = $this->buildCbtScenario();

        $this->assertAllAdminPagesOk($scenario, $scenario['manager']);

        $this->actingAs($scenario['manager'])
            ->get(route('cbt.admin.dashboard'))
            ->assertRedirect(route('cbt.admin.sessions.index'));
    }

    public function test_manager_can_manage_bank_soal_crud(): void
    {
        $scenario = $this->buildCbtScenario();
        $skill = $scenario['skill'];

        // Buat set soal.
        $this->actingAs($scenario['manager'])
            ->post(route('cbt.admin.questions.store'), [
                'skill_id' => $skill->id,
                'for_level' => 2,
                'set_title' => 'Manager PG Set',
                'questions' => [
                    [
                        'question_text' => 'Manager PG 1?',
                        'type' => 'multiple_choice',
                        'options' => ['A' => 'a', 'B' => 'b', 'C' => 'c', 'D' => 'd'],
                        'correct_answer' => 'A',
                        'default_weight' => 1,
                    ],
                    [
                        'question_text' => 'Manager PG 2?',
                        'type' => 'multiple_choice',
                        'options' => ['A' => 'a', 'B' => 'b'],
                        'correct_answer' => 'B',
                        'default_weight' => 1,
                    ],
                ],
            ])
            ->assertRedirect(route('cbt.admin.questions.index'));

        $q1 = Question::where('set_title', 'Manager PG Set')->firstOrFail();

        $this->assertSame(2, Question::where('set_title', 'Manager PG Set')->count());

        // Ubah soal.
        $this->actingAs($scenario['manager'])
            ->put(route('cbt.admin.questions.update', $q1), [
                'skill_id' => $skill->id,
                'question_text' => 'Manager PG edited?',
                'for_level' => 2,
                'type' => 'multiple_choice',
                'options' => ['A' => 'a', 'B' => 'b', 'C' => 'c', 'D' => 'd'],
                'correct_answer' => 'B',
                'default_weight' => 1,
                'status' => 'active',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('questions', ['id' => $q1->id, 'question_text' => 'Manager PG edited?', 'correct_answer' => 'B']);

        // Hapus satu soal.
        $this->actingAs($scenario['manager'])
            ->delete(route('cbt.admin.questions.destroy', $q1))
            ->assertRedirect(route('cbt.admin.questions.index'));

        $this->assertDatabaseMissing('questions', ['id' => $q1->id]);

        // Buat set essay, lalu hapus seluruh set.
        $this->actingAs($scenario['manager'])
            ->post(route('cbt.admin.questions.store'), [
                'skill_id' => $skill->id,
                'for_level' => 2,
                'set_title' => 'Manager Essay Set',
                'questions' => [
                    ['question_text' => 'M Essay 1?', 'type' => 'essay', 'default_weight' => 100],
                ],
            ])
            ->assertRedirect(route('cbt.admin.questions.index'));

        // Hapus seluruh set; pakai question_set_id asli hasil create.
        $managerEssaySetId = Question::where('set_title', 'Manager Essay Set')->value('question_set_id');

        $this->actingAs($scenario['manager'])
            ->delete(route('cbt.admin.questions.destroy-set', ['questionSetId' => $managerEssaySetId]))
            ->assertRedirect(route('cbt.admin.questions.index'));

        $this->assertSame(0, Question::where('set_title', 'Manager Essay Set')->count());
    }

    public function test_manager_can_create_exam(): void
    {
        $scenario = $this->buildCbtScenario();
        $questions = $scenario['mcQuestions'];

        $this->actingAs($scenario['manager'])
            ->post(route('cbt.admin.exams.store'), [
                'skill_id' => $scenario['skill']->id,
                'title' => 'Manager Auth Exam',
                'target_level' => 2,
                'passing_score' => 70,
                'duration_minutes' => 60,
                'is_published' => 1,
                'questions' => $questions->map(fn ($q) => ['id' => $q->id])->all(),
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('exams', ['title' => 'Manager Auth Exam']);
    }

    // ============================================
    // 5. MANAGER - APPROVAL MANAGER
    // ============================================

    public function test_manager_can_open_approval_list_and_detail(): void
    {
        $scenario = $this->makePendingApprovalScenario();
        $session = $scenario['session'];

        // Daftar antrean approval.
        $this->actingAs($scenario['manager'])
            ->get(route('cbt.admin.sessions.pending-approval'))
            ->assertOk()
            ->assertSee(route('cbt.admin.sessions.assessment', $session));

        // Detail pengajuan: nilai kuantitatif, threshold, 5 kriteria, tombol Setujui/Tolak.
        $this->actingAs($scenario['manager'])
            ->get(route('cbt.admin.sessions.assessment', $session))
            ->assertOk()
            ->assertSee('75')                       // nilai ujian
            ->assertSee('Threshold kompetensi')     // label threshold
            ->assertSee('70')                       // KKM
            ->assertSee('Memenuhi Nilai Minimum')
            ->assertSee('Pemahaman dan penerapan SOP')
            ->assertSee('Kesiapan menjalankan tanggung jawab level berikutnya')
            ->assertSee('Setujui')
            ->assertSee('Tolak');
    }

    public function test_manager_can_approve_level_request(): void
    {
        $scenario = $this->makePendingApprovalScenario();
        $session = $scenario['session'];

        $this->managerApprove($scenario, $session)->assertSessionHas('success');

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_APPROVED, $session->status);
        $this->assertEquals(ExamSession::DECISION_APPROVED, $session->manager_decision);
        $this->assertEquals($scenario['manager']->id, $session->decided_by);

        // Level kompetensi naik + riwayat tersimpan.
        $this->assertSame(2, (int) $scenario['competency']->refresh()->level);
        $this->assertLevelUpHistory($scenario, $session, 1, 2);
    }

    public function test_manager_can_reject_level_request_with_reason(): void
    {
        $scenario = $this->makePendingApprovalScenario();
        $session = $scenario['session'];

        $this->managerReject($scenario, $session, ['readiness' => 'tidak_memenuhi'], 'Belum siap naik level.');

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_REJECTED, $session->status);
        $this->assertEquals(ExamSession::DECISION_REJECTED, $session->manager_decision);
        $this->assertSame('Belum siap naik level.', $session->manager_notes);

        // Level tidak berubah dan catatan kualitatif tersimpan.
        $this->assertSame(1, (int) $scenario['competency']->refresh()->level);
        $this->assertDatabaseHas('manager_assessments', ['exam_session_id' => $session->id, 'readiness' => 'tidak_memenuhi']);
    }

    public function test_manager_can_view_approval_history(): void
    {
        $scenario = $this->makePendingApprovalScenario();
        $session = $scenario['session'];

        $this->managerApprove($scenario, $session)->assertSessionHas('success');

        $this->actingAs($scenario['manager'])
            ->get(route('cbt.admin.sessions.pending-approval', ['tab' => 'history']))
            ->assertOk()
            ->assertSee('detailModal'.$session->id);
    }

    public function test_manager_sees_approval_menu(): void
    {
        $scenario = $this->buildCbtScenario();

        $this->actingAs($scenario['manager'])
            ->get(route('cbt.admin.questions.index'))
            ->assertOk()
            ->assertSee('Persetujuan Level')
            ->assertSee(route('cbt.admin.sessions.pending-approval'));
    }

    // ============================================
    // 6. MANAGER & SUPERVISOR - FITUR KARYAWAN (sesuai implementasi)
    // ============================================

    public function test_supervisor_and_manager_cannot_use_employee_flow_without_employee_account(): void
    {
        $scenario = $this->buildCbtScenario();

        // Supervisor tidak terhubung data karyawan -> diarahkan ke dashboard.
        $this->actingAs($scenario['admin'])
            ->get(route('cbt.employee.dashboard'))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');

        $this->actingAs($scenario['admin'])
            ->get(route('user.my-training'))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');

        // Manager juga tidak terhubung data karyawan.
        $this->actingAs($scenario['manager'])
            ->get(route('cbt.employee.dashboard'))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');

        $this->actingAs($scenario['manager'])
            ->get(route('user.my-training'))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');
    }

    // ============================================
    // 7. KARYAWAN - FITUR MILIK KARYAWAN
    // ============================================

    public function test_employee_can_login_with_email(): void
    {
        $scenario = $this->buildCbtScenario();
        $employeeUser = $scenario['employeeUser'];

        $this->get(route('login'))->assertOk();

        $this->post(route('login'), [
            'email_or_nik' => $employeeUser->email,
            'password' => 'password',
        ])->assertNoContent();

        $this->assertAuthenticatedAs($employeeUser);
    }

    public function test_employee_can_access_own_features(): void
    {
        $scenario = $this->buildCbtScenario();
        $employeeUser = $scenario['employeeUser'];

        $this->actingAs($employeeUser)->get(route('cbt.employee.dashboard'))->assertOk();
        $this->actingAs($employeeUser)->get(route('cbt.employee.competencies'))->assertOk();
        $this->actingAs($employeeUser)->get(route('cbt.employee.history'))->assertOk();

        // Fitur user biasa (is.user).
        $this->actingAs($employeeUser)->get(route('user.my-training'))->assertOk();
        $this->actingAs($employeeUser)->get(route('user.training-history'))->assertOk();
        $this->actingAs($employeeUser)->get(route('user.my-profile'))->assertOk();
        $this->actingAs($employeeUser)->get(route('user.my-competencies'))->assertOk();
    }

    public function test_employee_can_do_full_exam_flow_and_see_result(): void
    {
        $scenario = $this->buildCbtScenario();
        $employeeUser = $scenario['employeeUser'];
        $exam = $this->buildLevelExam($scenario, 2, 'mc', 'QS-AUTH-FLOW', 70);

        // Lihat info ujian & daftar.
        $this->actingAs($employeeUser)->get(route('cbt.employee.exam-info', $exam))->assertOk();

        // Daftar ujian -> sesi dibuat.
        $this->actingAs($employeeUser)
            ->post(route('cbt.employee.register', $exam))
            ->assertRedirect();

        $session = ExamSession::where('exam_id', $exam->id)
            ->where('employee_nik', $scenario['employee']->nik)
            ->firstOrFail();

        // Lihat sesi & mulai.
        $this->actingAs($employeeUser)->get(route('cbt.employee.show', $session))->assertOk();
        $this->actingAs($employeeUser)->post(route('cbt.employee.start', $session))->assertRedirect();

        // Kerjakan & kirim jawaban.
        $this->actingAs($employeeUser)->get(route('cbt.employee.take', $session))->assertOk();
        $this->actingAs($employeeUser)
            ->post(route('cbt.employee.submit', $session), ['answers' => $this->autoAnswers($exam, 3)])
            ->assertRedirect();

        // Lihat hasil.
        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->status);
        $this->assertEquals(75, $session->score);

        $this->actingAs($employeeUser)
            ->get(route('cbt.employee.result', $session))
            ->assertOk()
            ->assertSee('75')
            ->assertSee('LULUS');
    }

    // ============================================
    // 8. KARYAWAN - DITOLAK FITUR ADMIN
    // ============================================

    public function test_employee_cannot_access_admin_pages(): void
    {
        $scenario = $this->buildCbtScenario();

        $this->assertAllAdminPagesDeniedForEmployee($scenario, $scenario['employeeUser']);

        // Dashboard CBT Admin juga ditolak.
        $this->actingAs($scenario['employeeUser'])
            ->get(route('cbt.admin.dashboard'))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');
    }

    public function test_employee_cannot_create_edit_delete_questions(): void
    {
        $scenario = $this->buildCbtScenario();
        $employeeUser = $scenario['employeeUser'];
        $question = $scenario['mcQuestions']->first();

        $countBefore = Question::count();

        // Buat soal -> ditolak, tidak ada data baru.
        $this->actingAs($employeeUser)
            ->post(route('cbt.admin.questions.store'), [
                'skill_id' => $scenario['skill']->id,
                'for_level' => 2,
                'set_title' => 'Hacked Set',
                'questions' => [
                    ['question_text' => 'Hack?', 'type' => 'multiple_choice', 'correct_answer' => 'A', 'default_weight' => 1],
                ],
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertSame($countBefore, Question::count());

        // Ubah soal -> ditolak, data tetap.
        $this->actingAs($employeeUser)
            ->put(route('cbt.admin.questions.update', $question), [
                'skill_id' => $scenario['skill']->id,
                'question_text' => 'Hacked?',
                'for_level' => 2,
                'type' => 'multiple_choice',
                'options' => ['A' => 'a', 'B' => 'b'],
                'correct_answer' => 'A',
                'default_weight' => 1,
                'status' => 'active',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('questions', ['id' => $question->id, 'question_text' => $question->question_text]);

        // Hapus soal -> ditolak, soal tetap ada.
        $this->actingAs($employeeUser)
            ->delete(route('cbt.admin.questions.destroy', $question))
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('questions', ['id' => $question->id]);

        // Hapus set -> ditolak, set tetap ada.
        $this->actingAs($employeeUser)
            ->delete(route('cbt.admin.questions.destroy-set', ['questionSetId' => 'QS-MC-TEST']))
            ->assertRedirect(route('dashboard'));

        $this->assertSame(4, Question::where('question_set_id', 'QS-MC-TEST')->count());
    }

    // ============================================
    // 9. KARYAWAN - DITOLAK APPROVAL MANAGER
    // ============================================

    public function test_employee_cannot_access_approval_pages_or_actions(): void
    {
        $scenario = $this->makePendingApprovalScenario();
        $session = $scenario['session'];
        $employeeUser = $scenario['employeeUser'];

        // Halaman antrean & riwayat ditolak.
        $this->actingAs($employeeUser)
            ->get(route('cbt.admin.sessions.pending-approval'))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');

        $this->actingAs($employeeUser)
            ->get(route('cbt.admin.sessions.approval-history'))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');

        // Detail pengajuan ditolak.
        $this->actingAs($employeeUser)
            ->get(route('cbt.admin.sessions.assessment', $session))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');

        // Setujui / tolak lewat URL langsung ditolak.
        $this->actingAs($employeeUser)
            ->post(route('cbt.admin.sessions.approve-level', $session), ['manager_notes' => 'Hack'])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');

        $this->actingAs($employeeUser)
            ->post(route('cbt.admin.sessions.reject-level', $session), ['manager_notes' => 'Hack'])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');

        // Tidak ada perubahan data approval.
        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->status);
        $this->assertEquals('pending', $session->manager_decision);
        $this->assertDatabaseMissing('manager_assessments', ['exam_session_id' => $session->id]);
        $this->assertSame(1, (int) $scenario['competency']->refresh()->level);
    }

    // ============================================
    // 10. AKSES LANGSUNG (ROUTE MATRIX)
    // ============================================

    public function test_direct_route_access_matrix(): void
    {
        $scenario = $this->makePendingApprovalScenario();
        $session = $scenario['session'];

        // Supervisor -> route Bank Soal: DIPERBOLEHKAN.
        $this->actingAs($scenario['admin'])
            ->get(route('cbt.admin.questions.index'))
            ->assertOk();

        // Supervisor -> route Approval Manager: DITOLAK.
        $this->actingAs($scenario['admin'])
            ->get(route('cbt.admin.sessions.pending-approval'))
            ->assertRedirect(route('dashboard'));

        // Manager -> route Bank Soal: DIPERBOLEHKAN.
        $this->actingAs($scenario['manager'])
            ->get(route('cbt.admin.questions.index'))
            ->assertOk();

        // Manager -> route Approval Manager: DIPERBOLEHKAN.
        $this->actingAs($scenario['manager'])
            ->get(route('cbt.admin.sessions.pending-approval'))
            ->assertOk();

        // Manager -> detail approval: DIPERBOLEHKAN.
        $this->actingAs($scenario['manager'])
            ->get(route('cbt.admin.sessions.assessment', $session))
            ->assertOk();

        // Karyawan -> route Bank Soal: DITOLAK.
        $this->actingAs($scenario['employeeUser'])
            ->get(route('cbt.admin.questions.index'))
            ->assertRedirect(route('dashboard'));

        // Karyawan -> route Approval Manager: DITOLAK.
        $this->actingAs($scenario['employeeUser'])
            ->get(route('cbt.admin.sessions.pending-approval'))
            ->assertRedirect(route('dashboard'));

        // Supervisor -> route Verifikasi Essay (fitur admin): DIPERBOLEHKAN.
        $essayExam = $this->buildLevelExam($scenario, 2, 'essay', 'QS-AUTH-ESSX', 70);
        $essaySession = $this->registerStartSubmit($scenario, $essayExam, $this->essayTextAnswers($essayExam));
        $scores = array_combine($essayExam->questions->pluck('id')->all(), [18, 25, 22, 17, 5]);
        $this->actingAs($scenario['admin'])
            ->post(route('cbt.admin.sessions.verify', $essaySession), array_merge(['action' => 'approve'], $this->payloadForScores($scores)))
            ->assertRedirect();
    }

    /**
     * Ubah daftar nilai per soal menjadi payload essay_score_{id}.
     */
    private function payloadForScores(array $scores): array
    {
        $payload = [];
        foreach ($scores as $questionId => $score) {
            $payload['essay_score_'.$questionId] = $score;
        }

        return $payload;
    }
}
