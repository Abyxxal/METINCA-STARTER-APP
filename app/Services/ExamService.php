<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\Question;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

/**
 * ExamService
 * 
 * Service layer untuk business logic terkait Exam dan ExamSession
 */
class ExamService
{
    /**
     * Create exam dengan validasi dan transaction
     */
    public function createExam(array $data)
    {
        DB::beginTransaction();
        try {
            $exam = Exam::create([
                'skill_id' => $data['skill_id'],
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'duration_minutes' => $data['duration_minutes'],
                'passing_score' => $data['passing_score'],
                'target_level' => $data['target_level'],
                'is_published' => $data['is_published'] ?? false,
            ]);

            // Attach questions if provided
            if (isset($data['questions']) && is_array($data['questions'])) {
                foreach ($data['questions'] as $index => $questionId) {
                    $exam->questions()->attach($questionId, ['order' => $index + 1]);
                }
            }

            DB::commit();
            
            // Clear cache
            Cache::forget('active_exams');
            
            return $exam;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update exam dengan validasi
     */
    public function updateExam(Exam $exam, array $data)
    {
        DB::beginTransaction();
        try {
            $exam->update([
                'skill_id' => $data['skill_id'],
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'duration_minutes' => $data['duration_minutes'],
                'passing_score' => $data['passing_score'],
                'target_level' => $data['target_level'],
                'is_published' => $data['is_published'] ?? $exam->is_published,
            ]);

            // Update questions if provided
            if (isset($data['questions']) && is_array($data['questions'])) {
                $exam->questions()->detach();
                foreach ($data['questions'] as $index => $questionId) {
                    $exam->questions()->attach($questionId, ['order' => $index + 1]);
                }
            }

            DB::commit();
            
            // Clear cache
            Cache::forget('active_exams');
            
            return $exam->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete exam dengan validasi
     */
    public function deleteExam(Exam $exam)
    {
        // Cek apakah ada sesi ujian
        if ($exam->sessions()->count() > 0) {
            throw new \Exception('Tidak dapat menghapus ujian yang sudah memiliki sesi!');
        }

        DB::beginTransaction();
        try {
            $exam->questions()->detach();
            $exam->delete();
            
            DB::commit();
            
            // Clear cache
            Cache::forget('active_exams');
            
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Assign exam to employee(s)
     */
    public function assignExamToEmployees(Exam $exam, array $employeeNiks)
    {
        DB::beginTransaction();
        try {
            $sessions = [];
            
            foreach ($employeeNiks as $nik) {
                $session = ExamSession::create([
                    'exam_id' => $exam->id,
                    'employee_nik' => $nik,
                    'status' => 'assigned',
                ]);
                $sessions[] = $session;
            }

            DB::commit();
            
            return $sessions;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Calculate exam score
     */
    public function calculateScore(ExamSession $session)
    {
        $answers = json_decode($session->answers, true) ?? [];
        $questions = $session->exam->questions;
        
        if ($questions->count() === 0) {
            return ['score' => 0, 'correct' => 0, 'total' => 0];
        }

        $correctCount = 0;
        foreach ($questions as $question) {
            $userAnswer = $answers[$question->id] ?? null;
            if ($userAnswer === $question->correct_answer) {
                $correctCount++;
            }
        }

        $score = ($correctCount / $questions->count()) * 100;

        return [
            'score' => round($score, 2),
            'correct' => $correctCount,
            'total' => $questions->count()
        ];
    }

    /**
     * Submit exam and auto-grade
     */
    public function submitExam(ExamSession $session, array $answers)
    {
        DB::beginTransaction();
        try {
            $session->answers = json_encode($answers);
            $session->submitted_at = now();
            $session->status = 'submitted';

            // Auto-calculate score
            $result = $this->calculateScore($session);
            $session->score = $result['score'];

            $session->save();

            DB::commit();

            return [
                'session' => $session,
                'result' => $result
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Verify exam session (admin/verifier)
     */
    public function verifyExamSession(ExamSession $session, int $verifierId, string $status, ?string $notes = null)
    {
        if (!in_array($status, ['verified_pass', 'verified_fail'])) {
            throw new \Exception('Status harus verified_pass atau verified_fail');
        }

        DB::beginTransaction();
        try {
            $session->status = $status;
            $session->verified_by = $verifierId;
            $session->verified_at = now();
            $session->verification_notes = $notes;
            $session->save();

            // Jika lulus, update employee competency
            if ($status === 'verified_pass') {
                $this->updateEmployeeCompetency($session);
            }

            DB::commit();

            return $session;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update employee competency setelah lulus ujian
     */
    private function updateEmployeeCompetency(ExamSession $session)
    {
        $employeeCompetencyService = new EmployeeCompetencyService();
        $employeeCompetencyService->updateLevel(
            $session->employee_nik,
            $session->exam->skill_id,
            $session->exam->target_level
        );
    }

    /**
     * Get questions for employee exam (filtered by position)
     * 
     * This method retrieves questions that are either:
     * - Universal (no position targeting)
     * - Targeted specifically for the employee's position
     * 
     * @param int $skillId The skill ID to filter questions
     * @param int $forLevel The competency level (1-4)
     * @param int|null $employeePositionId The employee's position ID (optional)
     * @param int $limit Maximum number of questions to retrieve
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getQuestionsForEmployee($skillId, $forLevel, $employeePositionId = null, $limit = 10)
    {
        $query = Question::where('skill_id', $skillId)
            ->where('for_level', $forLevel)
            ->where('status', 'active');

        // Filter by position if provided
        if ($employeePositionId) {
            $query->where(function($q) use ($employeePositionId) {
                // Get universal questions (no position records)
                $q->whereDoesntHave('positions')
                  // OR questions targeted for this position
                  ->orWhereHas('positions', function($q2) use ($employeePositionId) {
                      $q2->where('position_id', $employeePositionId);
                  });
            });
        } else {
            // If no position provided, only get universal questions
            $query->whereDoesntHave('positions');
        }

        return $query->inRandomOrder()->limit($limit)->get();
    }
}
