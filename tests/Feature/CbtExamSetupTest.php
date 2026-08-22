<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\ExamSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsCbtScenario;
use Tests\TestCase;

class CbtExamSetupTest extends TestCase
{
    use BuildsCbtScenario, RefreshDatabase;

    public function test_admin_can_create_exam_from_questions(): void
    {
        $scenario = $this->buildCbtScenario();
        $admin = $scenario['admin'];
        $skill = $scenario['skill'];
        $questions = $scenario['mcQuestions'];

        $response = $this->actingAs($admin)
            ->post(route('cbt.admin.exams.store'), [
                'skill_id' => $skill->id,
                'title' => 'CMM Level 2 Upgrade Test',
                'target_level' => 2,
                'passing_score' => 75,
                'duration_minutes' => 60,
                'is_published' => true,
                'questions' => $questions->map(fn ($q) => ['id' => $q->id])->all(),
            ]);

        $exam = Exam::where('title', 'CMM Level 2 Upgrade Test')->first();
        $this->assertNotNull($exam);
        $response->assertRedirect(route('cbt.admin.exams.show', $exam));

        $this->assertEquals($skill->id, $exam->skill_id);
        $this->assertEquals(2, $exam->target_level);
        $this->assertEquals(75, $exam->passing_score);
        $this->assertTrue($exam->is_published);
        $this->assertCount(4, $exam->questions);
        $this->assertEquals(
            $questions->pluck('id')->sort()->values()->all(),
            $exam->questions()->pluck('questions.id')->sort()->values()->all()
        );
    }

    public function test_exam_store_requires_title_and_target_level(): void
    {
        $scenario = $this->buildCbtScenario();
        $admin = $scenario['admin'];

        $this->actingAs($admin)
            ->post(route('cbt.admin.exams.store'), [
                'skill_id' => $scenario['skill']->id,
                'passing_score' => 70,
                'duration_minutes' => 60,
            ])
            ->assertSessionHasErrors(['title', 'target_level']);
    }

    public function test_admin_can_update_exam_and_sync_questions(): void
    {
        $scenario = $this->buildCbtScenario();
        $admin = $scenario['admin'];
        $exam = $scenario['mcExam'];
        $two = $scenario['mcQuestions']->take(2);

        $this->actingAs($admin)
            ->put(route('cbt.admin.exams.update', $exam), [
                'title' => 'MC Exam Diperbarui',
                'target_level' => 2,
                'passing_score' => 70,
                'duration_minutes' => 45,
                'is_published' => true,
                'questions' => $two->map(fn ($q) => ['id' => $q->id])->all(),
            ])
            ->assertRedirect(route('cbt.admin.exams.show', $exam));

        $exam->refresh();
        $this->assertEquals('MC Exam Diperbarui', $exam->title);
        $this->assertEquals(45, $exam->duration_minutes);
        $this->assertCount(2, $exam->questions);
    }

    public function test_admin_can_toggle_publish_status(): void
    {
        $scenario = $this->buildCbtScenario();
        $admin = $scenario['admin'];
        $exam = $scenario['mcExam'];

        $this->assertTrue($exam->is_published);

        $this->actingAs($admin)
            ->post(route('cbt.admin.exams.toggle-publish', $exam))
            ->assertRedirect();

        $this->assertFalse($exam->refresh()->is_published);

        $this->actingAs($admin)
            ->post(route('cbt.admin.exams.toggle-publish', $exam))
            ->assertRedirect();

        $this->assertTrue($exam->refresh()->is_published);
    }

    public function test_cannot_delete_exam_with_sessions(): void
    {
        $scenario = $this->buildCbtScenario();
        $admin = $scenario['admin'];
        $exam = $scenario['mcExam'];

        $this->makeSession($exam, $scenario['employee'], ExamSession::STATUS_ASSIGNED);

        $this->actingAs($admin)
            ->delete(route('cbt.admin.exams.destroy', $exam))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('exams', ['id' => $exam->id]);
    }

    public function test_admin_can_delete_exam_without_sessions(): void
    {
        $scenario = $this->buildCbtScenario();
        $admin = $scenario['admin'];
        $exam = $scenario['mcExam'];

        $this->actingAs($admin)
            ->delete(route('cbt.admin.exams.destroy', $exam))
            ->assertRedirect(route('cbt.admin.exams.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('exams', ['id' => $exam->id]);
    }
}
