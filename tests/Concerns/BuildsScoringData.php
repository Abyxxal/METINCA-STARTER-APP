<?php

namespace Tests\Concerns;

use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\Question;
use App\Models\Skill;
use Illuminate\Support\Collection;

/**
 * Shared data builders for scoring / grading / approval feature tests.
 *
 * Creates fresh question banks (MC / True-False / Essay), attaches them to a
 * new exam (with snapshot + weight), and starts an exam session for the given
 * scenario's employee.
 */
trait BuildsScoringData
{
    /**
     * @return Collection<int, Question>
     */
    private function createMcQuestions(Skill $skill, int $count = 4, string $correct = 'A', string $setId = 'QS-SCORE-MC', int $level = 2): Collection
    {
        $questions = collect();
        for ($i = 1; $i <= $count; $i++) {
            $questions->push(Question::create([
                'question_set_id' => $setId,
                'set_title' => 'Scoring MC Set',
                'skill_id' => $skill->id,
                'for_level' => $level,
                'question_text' => "Scoring MC Question {$i}?",
                'type' => 'multiple_choice',
                'options' => ['A' => 'Option A', 'B' => 'Option B', 'C' => 'Option C', 'D' => 'Option D'],
                'correct_answer' => $correct,
                'default_weight' => 1,
                'status' => 'active',
            ]));
        }

        return $questions;
    }

    /**
     * @return Collection<int, Question>
     */
    private function createTfQuestions(Skill $skill, int $count = 4, string $correct = 'A', string $setId = 'QS-SCORE-TF', int $level = 2): Collection
    {
        $questions = collect();
        for ($i = 1; $i <= $count; $i++) {
            $questions->push(Question::create([
                'question_set_id' => $setId,
                'set_title' => 'Scoring TF Set',
                'skill_id' => $skill->id,
                'for_level' => $level,
                'question_text' => "Pernyataan benar/salah {$i}",
                'type' => 'true_false',
                'options' => ['A' => 'Benar', 'B' => 'Salah'],
                'correct_answer' => $correct,
                'default_weight' => 1,
                'status' => 'active',
            ]));
        }

        return $questions;
    }

    /**
     * @return Collection<int, Question>
     */
    private function createEssayQuestions(Skill $skill, int $count = 5, int $weight = 20, string $setId = 'QS-SCORE-ESS', int $level = 2, ?array $weights = null): Collection
    {
        $questions = collect();
        for ($i = 1; $i <= $count; $i++) {
            $questions->push(Question::create([
                'question_set_id' => $setId,
                'set_title' => 'Scoring Essay Set',
                'skill_id' => $skill->id,
                'for_level' => $level,
                'question_text' => "Scoring Essay Question {$i}?",
                'type' => 'essay',
                'options' => null,
                'correct_answer' => null,
                'default_weight' => $weights[$i - 1] ?? $weight,
                'status' => 'active',
            ]));
        }

        return $questions;
    }

    private function makeExam(Skill $skill, Collection $questions, int $passingScore = 70, string $title = 'Scoring Exam', int $targetLevel = 2): Exam
    {
        $exam = Exam::create([
            'skill_id' => $skill->id,
            'title' => $title.' '.uniqid(),
            'target_level' => $targetLevel,
            'passing_score' => $passingScore,
            'duration_minutes' => 60,
            'is_published' => true,
            'status' => 'active',
        ]);

        $weights = $questions->pluck('default_weight', 'id')
            ->map(fn ($w) => $w ?? 1)
            ->all();
        $exam->attachQuestionsWithSnapshot($questions->pluck('id')->all(), $weights);

        return $exam;
    }

    private function startedSession(Exam $exam, array $scenario): ExamSession
    {
        return ExamSession::create([
            'exam_id' => $exam->id,
            'employee_nik' => $scenario['employee']->nik,
            'status' => ExamSession::STATUS_STARTED,
            'started_at' => now(),
        ]);
    }

    private function answerMap(Collection $questions, string $answer): array
    {
        return $questions->mapWithKeys(fn ($q) => [$q->id => $answer])->all();
    }
}
