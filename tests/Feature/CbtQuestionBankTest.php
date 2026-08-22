<?php

namespace Tests\Feature;

use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsCbtScenario;
use Tests\TestCase;

class CbtQuestionBankTest extends TestCase
{
    use BuildsCbtScenario, RefreshDatabase;

    // ============================================
    // CREATE (multi question set)
    // ============================================

    public function test_admin_can_create_question_set_with_multiple_questions(): void
    {
        $scenario = $this->buildCbtScenario();
        $admin = $scenario['admin'];
        $skill = $scenario['skill'];

        $this->actingAs($admin)
            ->post(route('cbt.admin.questions.store'), [
                'skill_id' => $skill->id,
                'for_level' => 2,
                'set_title' => 'CMM Level 2 - Batch Baru',
                'questions' => [
                    [
                        'question_text' => 'Pertanyaan 1?',
                        'type' => 'multiple_choice',
                        'options' => ['A' => 'Opsi A', 'B' => 'Opsi B', 'C' => 'Opsi C', 'D' => 'Opsi D'],
                        'correct_answer' => 'A',
                    ],
                    [
                        'question_text' => 'Pertanyaan 2?',
                        'type' => 'multiple_choice',
                        'options' => ['A' => 'Opsi A', 'B' => 'Opsi B', 'C' => 'Opsi C', 'D' => 'Opsi D'],
                        'correct_answer' => 'B',
                    ],
                    [
                        'question_text' => 'Pertanyaan 3?',
                        'type' => 'multiple_choice',
                        'options' => ['A' => 'Opsi A', 'B' => 'Opsi B', 'C' => 'Opsi C', 'D' => 'Opsi D'],
                        'correct_answer' => 'C',
                    ],
                ],
            ])
            ->assertRedirect(route('cbt.admin.questions.index'))
            ->assertSessionHas('success');

        $created = Question::where('set_title', 'CMM Level 2 - Batch Baru')->get();

        $this->assertCount(3, $created);
        $setIds = $created->pluck('question_set_id')->unique();
        $this->assertCount(1, $setIds, 'Semua soal dalam satu set harus berbagi question_set_id yang sama.');
        $this->assertNotEmpty($setIds->first());
        $this->assertDatabaseHas('questions', [
            'question_text' => 'Pertanyaan 1?',
            'status' => 'active',
        ]);
    }

    public function test_create_question_set_generates_default_set_title(): void
    {
        $scenario = $this->buildCbtScenario();
        $admin = $scenario['admin'];
        $skill = $scenario['skill'];

        $this->actingAs($admin)
            ->post(route('cbt.admin.questions.store'), [
                'skill_id' => $skill->id,
                'for_level' => 2,
                'questions' => [
                    ['question_text' => 'Tanpa judul set 1?', 'type' => 'essay', 'default_weight' => 100],
                ],
            ])
            ->assertRedirect(route('cbt.admin.questions.index'));

        $this->assertDatabaseHas('questions', [
            'question_text' => 'Tanpa judul set 1?',
            'set_title' => $skill->name.' - Level 2',
        ]);
    }

    // ============================================
    // CREATE (single question - backward compat)
    // ============================================

    public function test_admin_can_create_single_question(): void
    {
        $scenario = $this->buildCbtScenario();
        $admin = $scenario['admin'];
        $skill = $scenario['skill'];

        $this->actingAs($admin)
            ->post(route('cbt.admin.questions.store'), [
                'skill_id' => $skill->id,
                'for_level' => 1,
                'question_text' => 'Apa itu CMM?',
                'type' => 'multiple_choice',
                'options' => ['A' => 'Alat ukur', 'B' => 'Mesin', 'C' => 'Proses', 'D' => 'Bahan'],
                'correct_answer' => 'A',
                'status' => 'active',
            ])
            ->assertRedirect(route('cbt.admin.questions.index'));

        $this->assertDatabaseHas('questions', [
            'question_text' => 'Apa itu CMM?',
            'status' => 'active',
        ]);
    }

    public function test_create_question_requires_skill_and_level(): void
    {
        $scenario = $this->buildCbtScenario();
        $admin = $scenario['admin'];

        $this->actingAs($admin)
            ->post(route('cbt.admin.questions.store'), [
                'question_text' => 'Tanpa skill?',
                'type' => 'essay',
                'status' => 'active',
            ])
            ->assertSessionHasErrors(['skill_id', 'for_level']);
    }

    // ============================================
    // UPDATE
    // ============================================

    public function test_admin_can_update_question(): void
    {
        $scenario = $this->buildCbtScenario();
        $admin = $scenario['admin'];
        $skill = $scenario['skill'];
        $question = $scenario['mcQuestions']->first();

        $this->actingAs($admin)
            ->put(route('cbt.admin.questions.update', $question), [
                'skill_id' => $skill->id,
                'question_text' => 'Pertanyaan diperbarui?',
                'for_level' => 2,
                'type' => 'multiple_choice',
                'options' => ['A' => 'Opsi A', 'B' => 'Opsi B', 'C' => 'Opsi C', 'D' => 'Opsi D'],
                'correct_answer' => 'B',
                'status' => 'active',
            ])
            ->assertRedirect(route('cbt.admin.questions.show', $question));

        $this->assertDatabaseHas('questions', [
            'id' => $question->id,
            'question_text' => 'Pertanyaan diperbarui?',
            'correct_answer' => 'B',
        ]);
    }

    public function test_admin_can_update_question_set(): void
    {
        $scenario = $this->buildCbtScenario();
        $admin = $scenario['admin'];
        $skill = $scenario['skill'];
        $first = $scenario['mcQuestions']->first();

        $this->actingAs($admin)
            ->put(route('cbt.admin.questions.update-set', 'QS-MC-TEST'), [
                'set_title' => 'MC Set Diperbarui',
                'skill_id' => $skill->id,
                'for_level' => 2,
                'status' => 'active',
                'questions' => [
                    [
                        'id' => $first->id,
                        'question_text' => 'Diubah lewat set?',
                        'type' => 'multiple_choice',
                        'options' => ['A' => 'Opsi A', 'B' => 'Opsi B', 'C' => 'Opsi C', 'D' => 'Opsi D'],
                        'correct_answer' => 'A',
                    ],
                ],
            ])
            ->assertRedirect(route('cbt.admin.questions.show-set', 'QS-MC-TEST'));

        $this->assertDatabaseHas('questions', [
            'id' => $first->id,
            'set_title' => 'MC Set Diperbarui',
            'question_text' => 'Diubah lewat set?',
        ]);
    }

    public function test_updating_mc_set_preserves_existing_weights_when_weight_not_submitted(): void
    {
        $scenario = $this->buildCbtScenario();
        $admin = $scenario['admin'];
        $skill = $scenario['skill'];

        $payload = $scenario['mcQuestions']->map(fn ($q) => [
            'id' => $q->id,
            'question_text' => 'Diubah lewat set?',
            'type' => 'multiple_choice',
            'options' => ['A' => 'Opsi A', 'B' => 'Opsi B', 'C' => 'Opsi C', 'D' => 'Opsi D'],
            'correct_answer' => 'A',
        ])->values()->all();

        $this->actingAs($admin)
            ->put(route('cbt.admin.questions.update-set', 'QS-MC-TEST'), [
                'set_title' => 'MC Set Diperbarui',
                'skill_id' => $skill->id,
                'for_level' => 2,
                'status' => 'active',
                'questions' => $payload,
            ])
            ->assertRedirect(route('cbt.admin.questions.show-set', 'QS-MC-TEST'));

        $weights = Question::where('question_set_id', 'QS-MC-TEST')
            ->pluck('default_weight')
            ->map(fn ($w) => (int) $w)
            ->all();

        $this->assertCount(4, $weights);
        $this->assertSame([1, 1, 1, 1], $weights);
    }

    // ============================================
    // DELETE - history tetap utuh (snapshot)
    // ============================================

    public function test_exam_question_pivot_stores_question_snapshot(): void
    {
        $scenario = $this->buildCbtScenario();
        $exam = $scenario['mcExam'];
        $question = $scenario['mcQuestions']->first();

        $this->assertDatabaseHas('exam_question', [
            'exam_id' => $exam->id,
            'question_id' => $question->id,
            'question_text' => $question->question_text,
            'type' => 'multiple_choice',
            'correct_answer' => 'A',
            'for_level' => 2,
            'skill_id' => $question->skill_id,
        ]);

        $pivot = $exam->examQuestions()->where('question_id', $question->id)->first();
        $this->assertNotNull($pivot);
        $this->assertEquals($question->options, $pivot->options);
    }

    public function test_delete_question_keeps_exam_history_intact(): void
    {
        $scenario = $this->buildCbtScenario();
        $admin = $scenario['admin'];
        $question = $scenario['mcQuestions']->first();
        $exam = $scenario['mcExam'];

        $this->assertDatabaseHas('exam_question', ['exam_id' => $exam->id, 'question_id' => $question->id]);

        $this->actingAs($admin)
            ->delete(route('cbt.admin.questions.destroy', $question))
            ->assertSessionHas('success');

        // Soal benar-benar terhapus dari Bank Soal...
        $this->assertDatabaseMissing('questions', ['id' => $question->id]);

        // ...namun pivot, jawaban, dan ujian (history) tetap utuh via snapshot
        $this->assertDatabaseHas('exams', ['id' => $exam->id]);
        $this->assertDatabaseHas('exam_question', [
            'exam_id' => $exam->id,
            'question_id' => $question->id,
            'question_text' => 'MC question 1?',
            'type' => 'multiple_choice',
            'correct_answer' => 'A',
        ]);
    }

    public function test_delete_question_set_keeps_exam_history_intact(): void
    {
        $scenario = $this->buildCbtScenario();
        $admin = $scenario['admin'];
        $exam = $scenario['mcExam'];
        $questionIds = $scenario['mcQuestions']->pluck('id');

        $this->actingAs($admin)
            ->delete(route('cbt.admin.questions.destroy-set', 'QS-MC-TEST'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('questions', ['question_set_id' => 'QS-MC-TEST']);
        $this->assertDatabaseHas('exams', ['id' => $exam->id]);

        // Semua pivot tetap ada lengkap dengan snapshot-nya
        foreach ($questionIds as $index => $questionId) {
            $this->assertDatabaseHas('exam_question', [
                'exam_id' => $exam->id,
                'question_id' => $questionId,
                'question_text' => 'MC question '.($index + 1).'?',
                'type' => 'multiple_choice',
            ]);
        }
    }

    public function test_delete_question_keeps_session_and_answer_records(): void
    {
        $scenario = $this->buildCbtScenario();
        $admin = $scenario['admin'];
        $question = $scenario['mcQuestions']->first();
        $exam = $scenario['mcExam'];
        $employee = $scenario['employee'];

        $session = $this->makeSession($exam, $employee, 'submitted');
        $session->answers()->create([
            'question_id' => $question->id,
            'selected_answer' => 'A',
            'is_correct' => true,
            'score_earned' => 1,
        ]);

        $this->actingAs($admin)
            ->delete(route('cbt.admin.questions.destroy', $question))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('exam_sessions', ['id' => $session->id, 'status' => 'submitted']);
        $this->assertDatabaseHas('exam_answers', [
            'exam_session_id' => $session->id,
            'question_id' => $question->id,
            'score_earned' => 1,
        ]);

        // Halaman detail sesi (history) tetap bisa dibuka setelah soal dihapus
        $this->actingAs($admin)
            ->get(route('cbt.admin.sessions.show', $session))
            ->assertOk()
            ->assertSee('MC question 1?');
    }

    public function test_admin_can_delete_unused_question_set(): void
    {
        $scenario = $this->buildCbtScenario();
        $admin = $scenario['admin'];
        $skill = $scenario['skill'];

        $this->actingAs($admin)
            ->post(route('cbt.admin.questions.store'), [
                'skill_id' => $skill->id,
                'for_level' => 1,
                'set_title' => 'Set Yang Belum Dipakai',
                'questions' => [
                    ['question_text' => 'Soal tak terpakai 1?', 'type' => 'essay', 'default_weight' => 50],
                    ['question_text' => 'Soal tak terpakai 2?', 'type' => 'essay', 'default_weight' => 50],
                ],
            ])
            ->assertRedirect(route('cbt.admin.questions.index'));

        $setId = Question::where('set_title', 'Set Yang Belum Dipakai')->value('question_set_id');
        $this->assertNotEmpty($setId);

        $this->actingAs($admin)
            ->delete(route('cbt.admin.questions.destroy-set', $setId))
            ->assertRedirect(route('cbt.admin.questions.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('questions', ['question_set_id' => $setId]);
    }

    // ============================================
    // SET CONSTRAINTS (correct_answer, no mix, essay weight)
    // ============================================

    public function test_multi_store_rejects_mc_question_without_correct_answer(): void
    {
        $scenario = $this->buildCbtScenario();
        $admin = $scenario['admin'];
        $skill = $scenario['skill'];

        $this->actingAs($admin)
            ->post(route('cbt.admin.questions.store'), [
                'skill_id' => $skill->id,
                'for_level' => 2,
                'set_title' => 'Set Tanpa Kunci',
                'questions' => [
                    [
                        'question_text' => 'MC Tanpa Kunci Jawaban?',
                        'type' => 'multiple_choice',
                        'options' => ['A' => 'Opsi A', 'B' => 'Opsi B', 'C' => 'Opsi C', 'D' => 'Opsi D'],
                    ],
                ],
            ])
            ->assertSessionHasErrors('questions.0.correct_answer');

        $this->assertDatabaseMissing('questions', [
            'question_text' => 'MC Tanpa Kunci Jawaban?',
        ]);
    }

    public function test_multi_store_rejects_mixed_set(): void
    {
        $scenario = $this->buildCbtScenario();
        $admin = $scenario['admin'];
        $skill = $scenario['skill'];

        $this->actingAs($admin)
            ->post(route('cbt.admin.questions.store'), [
                'skill_id' => $skill->id,
                'for_level' => 2,
                'set_title' => 'Set Campuran',
                'questions' => [
                    [
                        'question_text' => 'MC 1?',
                        'type' => 'multiple_choice',
                        'options' => ['A' => 'Opsi A', 'B' => 'Opsi B', 'C' => 'Opsi C', 'D' => 'Opsi D'],
                        'correct_answer' => 'A',
                    ],
                    [
                        'question_text' => 'Esai 1?',
                        'type' => 'essay',
                        'default_weight' => 100,
                    ],
                ],
            ])
            ->assertSessionHasErrors('questions');

        $this->assertDatabaseMissing('questions', [
            'set_title' => 'Set Campuran',
        ]);
    }

    public function test_multi_store_rejects_essay_set_with_weight_not_100(): void
    {
        $scenario = $this->buildCbtScenario();
        $admin = $scenario['admin'];
        $skill = $scenario['skill'];

        $this->actingAs($admin)
            ->post(route('cbt.admin.questions.store'), [
                'skill_id' => $skill->id,
                'for_level' => 2,
                'set_title' => 'Set Bobot Salah',
                'questions' => [
                    ['question_text' => 'Esai 1?', 'type' => 'essay', 'default_weight' => 40],
                    ['question_text' => 'Esai 2?', 'type' => 'essay', 'default_weight' => 40],
                ],
            ])
            ->assertSessionHasErrors('questions');

        $this->assertDatabaseMissing('questions', [
            'set_title' => 'Set Bobot Salah',
        ]);
    }
}
