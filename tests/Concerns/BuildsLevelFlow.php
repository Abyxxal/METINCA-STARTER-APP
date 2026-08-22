<?php

namespace Tests\Concerns;

use App\Models\DivisionSkill;
use App\Models\EmployeeCompetency;
use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\ManagerAssessment;
use App\Models\Skill;
use Illuminate\Support\Collection;

/**
 * Level-aware flow builders for the 36 main scenarios
 * (3 tipe ujian x 4 level x 3 jalur hasil) plus role / matrix / history tests.
 *
 * Alur yang dipakai konsisten dengan aplikasi:
 *   Karyawan mendaftar (register) -> mulai (start) -> submit -> (essay: verifikasi
 *   Supervisor) -> Manager menyetujui/menolak -> level berubah/tidak pada matriks.
 */
trait BuildsLevelFlow
{
    use BuildsScoringData;

    private const ESSAY_WEIGHTS = [20, 30, 25, 20, 5];

    /**
     * Set level kompetensi karyawan sesuai level saat ini (target - 1).
     * Level 0 berarti tidak ada record kompetensi (belum terlatih).
     */
    protected function setEmployeeLevel(array $scenario, int $currentLevel): ?EmployeeCompetency
    {
        if ($currentLevel === 0) {
            $scenario['competency']->delete();

            return null;
        }

        $scenario['competency']->update(['level' => $currentLevel]);

        return $scenario['competency']->refresh();
    }

    /**
     * Build soal + ujian untuk target level dan tipe tertentu, KKM diberikan
     * sebagai threshold ujian.
     */
    protected function buildLevelExam(array $scenario, int $targetLevel, string $type, string $setId, int $passingScore = 70): Exam
    {
        $skill = $scenario['skill'];
        $questions = match ($type) {
            'mc' => $this->createMcQuestions($skill, 4, 'A', $setId, $targetLevel),
            'tf' => $this->createTfQuestions($skill, 4, 'A', $setId, $targetLevel),
            'essay' => $this->createEssayQuestions($skill, count(self::ESSAY_WEIGHTS), 20, $setId, $targetLevel, self::ESSAY_WEIGHTS),
            default => throw new \InvalidArgumentException("Tipe ujian tidak dikenal: {$type}"),
        };

        return $this->makeExam($skill, $questions, $passingScore, "Level {$targetLevel} {$type} exam", $targetLevel);
    }

    /**
     * Karyawan mendaftar, mulai, dan mengirim jawaban lewat endpoint asli.
     * Mengembalikan session yang sudah di-refresh.
     */
    protected function registerStartSubmit(array $scenario, Exam $exam, array $answers): ExamSession
    {
        $this->actingAs($scenario['employeeUser'])
            ->post(route('cbt.employee.register', $exam))
            ->assertRedirect();

        $session = ExamSession::where('exam_id', $exam->id)
            ->where('employee_nik', $scenario['employee']->nik)
            ->firstOrFail();

        $this->actingAs($scenario['employeeUser'])
            ->post(route('cbt.employee.start', $session))
            ->assertRedirect();

        $this->actingAs($scenario['employeeUser'])
            ->post(route('cbt.employee.submit', $session), ['answers' => $answers])
            ->assertRedirect();

        return $session->refresh();
    }

    /**
     * Jawaban PG / Benar-Salah dengan sejumlah jawaban benar (sisanya salah).
     * Nilai dihitung sistem = (benar / total) x 100.
     */
    protected function autoAnswers(Exam $exam, int $correctCount): array
    {
        $answers = [];
        foreach ($exam->questions as $index => $question) {
            $answers[$question->id] = $index < $correctCount ? $question->correct_answer : ($question->correct_answer === 'A' ? 'B' : 'A');
        }

        return $answers;
    }

    /**
     * Jawaban essay berupa teks (belum dinilai oleh Supervisor).
     */
    protected function essayTextAnswers(Exam $exam): array
    {
        return $exam->questions
            ->mapWithKeys(fn ($q) => [$q->id => 'Jawaban essay untuk soal #'.$q->id])
            ->all();
    }

    /**
     * Supervisor (admin) menilai essay: nilai per soal tidak boleh melebihi bobot.
     */
    protected function gradeEssay(array $scenario, ExamSession $session, array $scores, string $action = 'approve')
    {
        $payload = ['action' => $action];
        foreach ($scores as $questionId => $score) {
            $payload['essay_score_'.$questionId] = $score;
        }

        return $this->actingAs($scenario['admin'])
            ->post(route('cbt.admin.sessions.verify', $session), $payload);
    }

    /**
     * Manager menyetujui kenaikan level (5 kriteria memenuhi).
     */
    protected function managerApprove(array $scenario, ExamSession $session, array $overrides = [])
    {
        return $this->actingAs($scenario['manager'])
            ->post(route('cbt.admin.sessions.approve-level', $session), array_merge([
                'assessment_method' => 'interview',
                'sop_understanding' => 'memenuhi',
                'competency_application' => 'memenuhi',
                'independence' => 'memenuhi',
                'problem_solving' => 'memenuhi',
                'readiness' => 'memenuhi',
                'verification_date' => now()->format('Y-m-d'),
                'manager_notes' => 'Penilaian kualitatif memenuhi semua kriteria.',
            ], $overrides));
    }

    /**
     * Manager menolak kenaikan level (dengan alasan).
     */
    protected function managerReject(array $scenario, ExamSession $session, array $criteria = [], string $notes = 'Karyawan belum memenuhi kompetensi untuk naik level.')
    {
        return $this->actingAs($scenario['manager'])
            ->post(route('cbt.admin.sessions.reject-level', $session), array_merge([
                'assessment_method' => 'interview',
                'sop_understanding' => 'memenuhi',
                'competency_application' => 'memenuhi',
                'independence' => 'memenuhi',
                'problem_solving' => 'memenuhi',
                'readiness' => 'memenuhi',
                'manager_notes' => $notes,
            ], $criteria));
    }

    /**
     * Hubungkan skill ke divisi supaya muncul di Matriks Kompetensi.
     */
    protected function linkSkillToDivision(array $scenario): void
    {
        DivisionSkill::create([
            'division_id' => $scenario['division']->id,
            'skill_id' => $scenario['skill']->id,
        ]);
    }

    /**
     * Buka halaman Approval Manager dan pastikan sesi tampil/tidak pada antrean.
     * Marker yang dicek adalah tautan penilaian kualitatif yang hanya muncul di
     * tabel antrean (nama karyawan tidak dipakai karena bisa bocor lewat notifikasi
     * topbar untuk sesi berstatus "submitted").
     */
    protected function assertInManagerQueue(array $scenario, ExamSession $session, bool $present): void
    {
        $marker = route('cbt.admin.sessions.assessment', $session);

        $response = $this->actingAs($scenario['manager'])
            ->get(route('cbt.admin.sessions.pending-approval'))
            ->assertOk();

        $present
            ? $response->assertSee($marker)
            : $response->assertDontSee($marker);
    }

    /**
     * Buka tab Riwayat Approval dan pastikan sesi tampil/tidak.
     * Marker yang dicek adalah modal detail yang hanya dirender untuk sesi
     * yang benar-benar ada di riwayat.
     */
    protected function assertInApprovalHistory(array $scenario, ExamSession $session, bool $present): void
    {
        $marker = 'detailModal'.$session->id;

        $response = $this->actingAs($scenario['manager'])
            ->get(route('cbt.admin.sessions.pending-approval', ['tab' => 'history']))
            ->assertOk();

        $present
            ? $response->assertSee($marker)
            : $response->assertDontSee($marker);
    }

    /**
     * Buka halaman Matriks Kompetensi dan pastikan karyawan + kolom skill tampil.
     */
    protected function assertMatrixShowsEmployee(array $scenario, string $employeeName): void
    {
        $this->actingAs($scenario['admin'])
            ->get(route('cbt.admin.competency-matrix', ['division_id' => $scenario['division']->id]))
            ->assertOk()
            ->assertSee($employeeName)
            ->assertSee($scenario['skill']->name);
    }

    /**
     * Periksa level kompetensi karyawan saat ini.
     * Level 0 diperlakukan sama dengan "belum ada record" (belum terlatih).
     */
    protected function assertEmployeeCompetencyLevel(array $scenario, ?int $expectedLevel): void
    {
        $record = EmployeeCompetency::where('employee_nik', $scenario['employee']->nik)
            ->where('skill_id', $scenario['skill']->id)
            ->first();

        if ($expectedLevel === 0 || $expectedLevel === null) {
            $this->assertNull($record, 'Seharusnya tidak ada record kompetensi karyawan (level 0).');
        } else {
            $this->assertNotNull($record, 'Record kompetensi karyawan tidak ditemukan.');
            $this->assertEquals($expectedLevel, $record->level);
        }
    }

    /**
     * Periksa riwayat kenaikan level tersimpan setelah Manager menyetujui.
     */
    protected function assertLevelUpHistory(array $scenario, ExamSession $session, ?int $previous, int $new): void
    {
        $this->assertDatabaseHas('employee_competency_histories', [
            'exam_session_id' => $session->id,
            'previous_level' => $previous,
            'new_level' => $new,
            'change_type' => $previous === null ? 'initial' : 'up',
            'change_source' => 'exam_pass',
            'changed_by' => $scenario['manager']->id,
        ]);
    }

    /**
     * Ambil catatan penilaian kualitatif Manager untuk sebuah sesi.
     */
    protected function assessmentOf(ExamSession $session): ManagerAssessment
    {
        $assessment = ManagerAssessment::where('exam_session_id', $session->id)->first();

        $this->assertNotNull($assessment, 'Penilaian kualitatif Manager tidak tersimpan.');

        return $assessment;
    }
}
