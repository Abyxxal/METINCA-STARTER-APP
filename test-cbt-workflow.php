<?php

/**
 * Test CBT Full Workflow
 * 
 * Flow:
 * 1. Manager create exam (Level 1)
 * 2. Create exam session/period
 * 3. Employee register & take exam
 * 4. Submit answers (one pass, one fail)
 * 5. Manager verify essay answers
 * 6. Manager approve/reject level upgrade
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Employee;
use App\Models\Skill;
use App\Models\Question;
use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\ExamAnswer;
use App\Models\ExamPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

echo "=== CBT WORKFLOW TEST ===\n\n";

DB::beginTransaction();

try {
    // 1. Setup: Get manager user
    echo "1. Setup Manager...\n";
    $manager = User::where('role', 'manager')->first();
    if (!$manager) {
        $manager = User::where('role', 'admin')->first();
    }
    echo "   Manager: {$manager->name} (ID: {$manager->id})\n\n";

    // 2. Get or create test employees
    echo "2. Setup Employees...\n";
    $employees = Employee::where('status', 'Aktif')->limit(2)->get();
    
    if ($employees->count() < 2) {
        echo "   Need at least 2 employees. Creating...\n";
        
        // Create employee 1
        $emp1 = Employee::create([
            'nik' => 'TEST001',
            'name' => 'Test Employee Pass',
            'department_id' => 1,
            'division_id' => 1,
            'position_id' => 1,
            'status' => 'Aktif',
            'join_date' => now(),
        ]);
        
        User::create([
            'name' => 'Test Employee Pass',
            'email' => 'testpass@test.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'employee_nik' => 'TEST001',
            'password_changed_at' => now(),
        ]);
        
        // Create employee 2
        $emp2 = Employee::create([
            'nik' => 'TEST002',
            'name' => 'Test Employee Fail',
            'department_id' => 1,
            'division_id' => 1,
            'position_id' => 1,
            'status' => 'Aktif',
            'join_date' => now(),
        ]);
        
        User::create([
            'name' => 'Test Employee Fail',
            'email' => 'testfail@test.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'employee_nik' => 'TEST002',
            'password_changed_at' => now(),
        ]);
        
        $employees = collect([$emp1, $emp2]);
    }
    
    $empPass = $employees[0];
    $empFail = $employees[1];
    
    echo "   Employee 1 (Pass): {$empPass->name} (NIK: {$empPass->nik})\n";
    echo "   Employee 2 (Fail): {$empFail->name} (NIK: {$empFail->nik})\n\n";

    // 3. Get skill and questions
    echo "3. Setup Skill & Questions...\n";
    $skill = Skill::where('is_active', true)->first();
    
    // Get mix of MC and essay questions
    $mcQuestions = Question::where('skill_id', $skill->id)
        ->where('type', 'multiple_choice')
        ->where('for_level', 1)
        ->limit(3)
        ->get();
    
    $essayQuestions = Question::where('skill_id', $skill->id)
        ->where('type', 'essay')
        ->where('for_level', 1)
        ->limit(2)
        ->get();
    
    if ($mcQuestions->count() < 3 || $essayQuestions->count() < 2) {
        echo "   Need more questions. Creating...\n";
        
        // Create MC questions
        for ($i = $mcQuestions->count(); $i < 3; $i++) {
            $mcQuestions->push(Question::create([
                'skill_id' => $skill->id,
                'question_text' => "Test MC Question " . ($i + 1),
                'type' => 'multiple_choice',
                'for_level' => 1,
                'options' => json_encode([
                    'A' => 'Option A',
                    'B' => 'Option B (Correct)',
                    'C' => 'Option C',
                    'D' => 'Option D',
                ]),
                'correct_answer' => 'B',
                'weight' => 10,
            ]));
        }
        
        // Create essay questions
        for ($i = $essayQuestions->count(); $i < 2; $i++) {
            $essayQuestions->push(Question::create([
                'skill_id' => $skill->id,
                'question_text' => "Test Essay Question " . ($i + 1) . ": Explain the concept.",
                'type' => 'essay',
                'for_level' => 1,
                'weight' => 20,
            ]));
        }
    }
    
    echo "   Skill: {$skill->name}\n";
    echo "   MC Questions: {$mcQuestions->count()}\n";
    echo "   Essay Questions: {$essayQuestions->count()}\n\n";

    // 4. Create Exam
    echo "4. Creating Exam...\n";
    $exam = Exam::create([
        'title' => 'Test Exam Level 1 - ' . now()->format('Y-m-d H:i:s'),
        'description' => 'Test exam for workflow validation',
        'skill_id' => $skill->id,
        'target_level' => 1,
        'passing_score' => 70,
        'duration_minutes' => 30,
        'is_published' => true,
        'created_by' => $manager->id,
    ]);
    
    // Attach questions
    $order = 1;
    foreach ($mcQuestions as $q) {
        $exam->questions()->attach($q->id, [
            'weight' => 10,
            'order' => $order++,
        ]);
    }
    foreach ($essayQuestions as $q) {
        $exam->questions()->attach($q->id, [
            'weight' => 20,
            'order' => $order++,
        ]);
    }
    
    echo "   Exam ID: {$exam->id}\n";
    echo "   Title: {$exam->title}\n";
    echo "   Passing Score: {$exam->passing_score}%\n";
    echo "   Total Questions: " . $exam->questions()->count() . "\n\n";

    // 5. Create Exam Period (optional - exam can be taken anytime if published)
    echo "5. Exam Period not required for test - skipping...\n\n";

    // 6. Employee 1 takes exam (PASS scenario)
    echo "6. Employee 1 takes exam (PASS)...\n";
    $session1 = ExamSession::create([
        'exam_id' => $exam->id,
        'employee_nik' => $empPass->nik,
        'status' => ExamSession::STATUS_ASSIGNED,
    ]);
    
    // Start exam
    $session1->update([
        'status' => ExamSession::STATUS_STARTED,
        'started_at' => now(),
    ]);
    
    // Answer MC questions (all correct)
    foreach ($mcQuestions as $q) {
        ExamAnswer::create([
            'exam_session_id' => $session1->id,
            'question_id' => $q->id,
            'selected_answer' => $q->correct_answer,
            'is_correct' => true,
            'score_earned' => 10,
        ]);
    }
    
    // Answer essay questions
    foreach ($essayQuestions as $q) {
        ExamAnswer::create([
            'exam_session_id' => $session1->id,
            'question_id' => $q->id,
            'selected_answer' => 'Good answer that demonstrates understanding.',
            'is_correct' => false, // Pending verification
            'score_earned' => 0,
        ]);
    }
    
    // Submit
    $session1->update([
        'status' => ExamSession::STATUS_SUBMITTED,
        'finished_at' => now(),
    ]);
    
    echo "   Session ID: {$session1->id}\n";
    echo "   MC Score: 30/30 (100%)\n";
    echo "   Essay: Pending verification\n\n";

    // 7. Employee 2 takes exam (FAIL scenario)
    echo "7. Employee 2 takes exam (FAIL)...\n";
    $session2 = ExamSession::create([
        'exam_id' => $exam->id,
        'employee_nik' => $empFail->nik,
        'status' => ExamSession::STATUS_ASSIGNED,
    ]);
    
    // Start exam
    $session2->update([
        'status' => ExamSession::STATUS_STARTED,
        'started_at' => now(),
    ]);
    
    // Answer MC questions (only 1 correct)
    $mcArray = $mcQuestions->toArray();
    ExamAnswer::create([
        'exam_session_id' => $session2->id,
        'question_id' => $mcArray[0]['id'],
        'selected_answer' => $mcArray[0]['correct_answer'],
        'is_correct' => true,
        'score_earned' => 10,
    ]);
    ExamAnswer::create([
        'exam_session_id' => $session2->id,
        'question_id' => $mcArray[1]['id'],
        'selected_answer' => 'A', // Wrong
        'is_correct' => false,
        'score_earned' => 0,
    ]);
    ExamAnswer::create([
        'exam_session_id' => $session2->id,
        'question_id' => $mcArray[2]['id'],
        'selected_answer' => 'C', // Wrong
        'is_correct' => false,
        'score_earned' => 0,
    ]);
    
    // Answer essay questions (weak)
    foreach ($essayQuestions as $q) {
        ExamAnswer::create([
            'exam_session_id' => $session2->id,
            'question_id' => $q->id,
            'selected_answer' => 'Short weak answer.',
            'is_correct' => false,
            'score_earned' => 0,
        ]);
    }
    
    // Submit
    $session2->update([
        'status' => ExamSession::STATUS_SUBMITTED,
        'finished_at' => now(),
    ]);
    
    echo "   Session ID: {$session2->id}\n";
    echo "   MC Score: 10/30 (33%)\n";
    echo "   Essay: Pending verification\n\n";

    // 8. Manager verifies essays
    echo "8. Manager verifies essay answers...\n";
    
    // Session 1 essays - High scores
    foreach ($session1->answers()->whereHas('question', fn($q) => $q->where('type', 'essay'))->get() as $answer) {
        $answer->update([
            'is_correct' => true,
            'score_earned' => 18, // 90% of 20
            'verified_by' => $manager->id,
            'verified_at' => now(),
        ]);
    }
    
    // Session 2 essays - Low scores
    foreach ($session2->answers()->whereHas('question', fn($q) => $q->where('type', 'essay'))->get() as $answer) {
        $answer->update([
            'is_correct' => false,
            'score_earned' => 8, // 40% of 20
            'verified_by' => $manager->id,
            'verified_at' => now(),
        ]);
    }
    
    // Calculate final scores
    $session1->score = round(($session1->answers()->sum('score_earned') / ($exam->questions()->sum('weight') ?: 1)) * 100);
    $session1->save();
    
    $session2->score = round(($session2->answers()->sum('score_earned') / ($exam->questions()->sum('weight') ?: 1)) * 100);
    $session2->save();
    
    echo "   Session 1 Final Score: {$session1->score}% (Threshold: {$exam->passing_score}%)\n";
    echo "   Session 2 Final Score: {$session2->score}% (Threshold: {$exam->passing_score}%)\n\n";

    // 9. Update session status based on scores
    echo "9. Updating session status...\n";
    if ($session1->score >= $exam->passing_score) {
        $session1->update(['status' => ExamSession::STATUS_VERIFIED_PASS]);
        echo "   Session 1: PASS → Pending Manager Approval\n";
    } else {
        $session1->update(['status' => ExamSession::STATUS_VERIFIED_FAIL]);
        echo "   Session 1: FAIL\n";
    }
    
    if ($session2->score >= $exam->passing_score) {
        $session2->update(['status' => ExamSession::STATUS_VERIFIED_PASS]);
        echo "   Session 2: PASS → Pending Manager Approval\n";
    } else {
        $session2->update(['status' => ExamSession::STATUS_VERIFIED_FAIL]);
        echo "   Session 2: FAIL\n";
    }
    
    echo "\n";

    // 10. Manager qualitative assessment & approval/rejection
    echo "10. Manager qualitative assessment...\n";
    
    if ($session1->status === ExamSession::STATUS_VERIFIED_PASS) {
        echo "   Session 1: Manager APPROVES level upgrade\n";
        
        // Create qualitative assessment
        DB::table('manager_assessments')->insert([
            'exam_session_id' => $session1->id,
            'sop_understanding' => 'memenuhi',
            'competency_application' => 'memenuhi',
            'independence' => 'memenuhi',
            'problem_solving' => 'memenuhi',
            'readiness' => 'memenuhi',
            'qualitative_score' => 100,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $session1->update([
            'status' => ExamSession::STATUS_APPROVED,
            'manager_decision' => ExamSession::DECISION_APPROVED,
            'manager_notes' => 'Excellent performance. Ready for level upgrade.',
            'decided_by' => $manager->id,
            'decided_at' => now(),
        ]);
        
        // Update employee competency
        DB::table('employee_competencies')->updateOrInsert(
            [
                'employee_nik' => $empPass->nik,
                'skill_id' => $skill->id,
            ],
            [
                'level' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        
        // Log history
        $compId = DB::table('employee_competencies')
            ->where('employee_nik', $empPass->nik)
            ->where('skill_id', $skill->id)
            ->value('id');
        
        DB::table('employee_competency_histories')->insert([
            'employee_competency_id' => $compId,
            'previous_level' => 0,
            'new_level' => 1,
            'change_type' => 'up',
            'change_source' => 'exam',
            'changed_by' => $manager->id,
            'exam_session_id' => $session1->id,
            'notes' => 'Promoted via exam',
            'created_at' => now(),
        ]);
        
        echo "   → Employee '{$empPass->name}' promoted to Level 1\n";
    }
    
    if ($session2->status === ExamSession::STATUS_VERIFIED_FAIL) {
        echo "   Session 2: Score below threshold - REJECTED\n";
        
        $session2->update([
            'status' => ExamSession::STATUS_REJECTED,
            'manager_decision' => ExamSession::DECISION_REJECTED,
            'manager_notes' => 'Score below passing threshold. Please retake the exam.',
            'decided_by' => $manager->id,
            'decided_at' => now(),
        ]);
        
        echo "   → Employee '{$empFail->name}' needs to retake exam\n";
    }

    echo "\n=== TEST COMPLETED SUCCESSFULLY ===\n";
    echo "\nSummary:\n";
    echo "- Exam Created: {$exam->title}\n";
    echo "- Employee 1 ({$empPass->name}): Score {$session1->score}% → APPROVED → Level 1\n";
    echo "- Employee 2 ({$empFail->name}): Score {$session2->score}% → REJECTED\n";
    echo "\nTest data created. Check in admin panel:\n";
    echo "- Exams: /cbt/admin/exams\n";
    echo "- Sessions: /cbt/admin/sessions\n";
    echo "- Competency Matrix: /cbt/admin/competency-matrix\n";
    
    DB::commit();
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
