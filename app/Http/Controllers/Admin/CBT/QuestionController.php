<?php

namespace App\Http\Controllers\Admin\CBT;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Skill;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * QuestionController
 * 
 * Admin controller for managing CBT Questions (Bank Soal).
 */
class QuestionController extends Controller
{
    /**
     * Display a listing of all questions.
     */
    public function index(Request $request)
    {
        // Group by question_set_id instead of individual questions
        $query = Question::select('question_set_id', 'set_title', 'skill_id', 'for_level', 'status')
            ->selectRaw('COUNT(*) as question_count')
            ->selectRaw('MAX(created_at) as created_at')
            ->with('skill')
            ->groupBy('question_set_id', 'set_title', 'skill_id', 'for_level', 'status');

        // Filter by division (via skill's division_id)
        if ($request->division_id) {
            $query->whereHas('skill', function($q) use ($request) {
                $q->where('division_id', $request->division_id);
            });
        }

        // Filter by skill
        if ($request->skill_id) {
            $query->where('skill_id', $request->skill_id);
        }

        // Filter by level
        if ($request->for_level) {
            $query->where('for_level', $request->for_level);
        }

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->type) {
            $query->where('type', $request->type);
        }

        $questionSets = $query->latest('created_at')->paginate(20);
        
        // Load positions for each set (from first question in each set)
        $questionSets->getCollection()->transform(function ($set) {
            $firstQuestion = Question::where('question_set_id', $set->question_set_id)
                ->with('positions')
                ->first();
            $set->targetPosition = $firstQuestion?->positions->first();
            return $set;
        });
        
        $divisions = Cache::remember('divisions_with_dept', 3600, function() {
            return Division::with('department')->orderBy('name')->get();
        });
        
        // Get skills - if division selected, only show skills from that division
        if ($request->division_id) {
            $skills = Cache::remember("skills_division_{$request->division_id}", 3600, function() use ($request) {
                return Skill::where('is_active', true)
                    ->where('division_id', $request->division_id)
                    ->get();
            });
        } else {
            $skills = Cache::remember('active_skills', 3600, function() {
                return Skill::where('is_active', true)->get();
            });
        }

        return view('admin.cbt.questions.index', compact('questionSets', 'skills', 'divisions'));
    }

    /**
     * Show the form for creating a new question.
     */
    public function create()
    {
        $divisions = Division::with('department')->orderBy('name')->get();
        
        return view('admin.cbt.questions.create', compact('divisions'));
    }

    /**
     * Store one or more questions.
     */
    public function store(Request $request)
    {
        // Check if multiple questions submitted
        if ($request->has('questions') && is_array($request->questions)) {
            $validated = $request->validate([
                'skill_id' => 'required|exists:skills,id',
                'for_level' => 'required|integer|min:1|max:4',
                'set_title' => 'nullable|string|max:255',
                'target_position' => 'nullable|exists:positions,id',
                'questions' => 'required|array|min:1',
                'questions.*.question_text' => 'required|string',
                'questions.*.type' => 'required|in:multiple_choice,true_false,essay',
                'questions.*.options' => 'nullable|array',
                'questions.*.correct_answer' => 'nullable|string',
            ]);

            // Generate unique question_set_id
            $skill = Skill::findOrFail($validated['skill_id']);
            $questionSetId = 'QS-' . date('YmdHis') . '-' . $validated['skill_id'];
            
            // Generate set_title
            $setTitle = $validated['set_title'] ?? ($skill->name . ' - Level ' . $validated['for_level']);

            $count = 0;
            foreach ($validated['questions'] as $q) {
                if (empty(trim($q['question_text']))) {
                    continue;
                }

                $options = [];
                if ($q['type'] === 'true_false') {
                    $options = ['A' => 'Benar', 'B' => 'Salah'];
                } elseif ($q['type'] === 'multiple_choice' && isset($q['options'])) {
                    $options = array_filter($q['options'], fn($v) => !empty($v));
                }

                $question = Question::create([
                    'question_set_id' => $questionSetId,
                    'set_title' => $setTitle,
                    'skill_id' => $validated['skill_id'],
                    'for_level' => $validated['for_level'],
                    'type' => $q['type'],
                    'question_text' => $q['question_text'],
                    'options' => !empty($options) ? $options : null,
                    'correct_answer' => $q['correct_answer'] ?? null,
                    'status' => 'active',
                ]);

                // Attach position if specified (empty = universal)
                if (!empty($validated['target_position'])) {
                    $question->positions()->attach($validated['target_position']);
                }

                $count++;
            }

            return redirect()
                ->route('cbt.admin.questions.index')
                ->with('success', "Set soal \"{$setTitle}\" dengan {$count} soal berhasil ditambahkan!");
        }

        // Single question (backward compatibility)
        $validated = $request->validate([
            'skill_id' => 'required|exists:skills,id',
            'question_text' => 'required|string',
            'for_level' => 'required|integer|min:1|max:4',
            'type' => 'required|in:multiple_choice,true_false,essay',
            'target_position' => 'nullable|exists:positions,id',
            'options' => 'required_if:type,multiple_choice|array',
            'correct_answer' => 'required_unless:type,essay|string',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validated['type'] === 'multiple_choice' && isset($validated['options'])) {
            $validated['options'] = array_filter($validated['options'], fn($v) => !empty($v));
        }

        if ($validated['type'] === 'true_false') {
            $validated['options'] = ['A' => 'Benar', 'B' => 'Salah'];
        }

        $question = Question::create($validated);

        // Attach position if specified
        if (!empty($validated['target_position'])) {
            $question->positions()->attach($validated['target_position']);
        }

        return redirect()
            ->route('cbt.admin.questions.index')
            ->with('success', 'Soal berhasil ditambahkan!');
    }

    /**
     * Display the specified question.
     */
    public function show(Question $question)
    {
        $question->load(['skill', 'exams']);
        return view('admin.cbt.questions.show', compact('question'));
    }

    /**
     * Show the form for editing the specified question.
     */
    public function edit(Question $question)
    {
        $skills = Skill::where('is_active', true)->get();
        $question->load('positions', 'skill.division');
        
        return view('admin.cbt.questions.edit', compact('question', 'skills'));
    }

    /**
     * Update the specified question.
     */
    public function update(Request $request, Question $question)
    {
        $validated = $request->validate([
            'skill_id' => 'required|exists:skills,id',
            'question_text' => 'required|string',
            'for_level' => 'required|integer|min:1|max:4',
            'type' => 'required|in:multiple_choice,true_false,essay',
            'target_position' => 'nullable|exists:positions,id',
            'options' => 'required_if:type,multiple_choice|array',
            'options.A' => 'required_if:type,multiple_choice|string',
            'options.B' => 'required_if:type,multiple_choice|string',
            'options.C' => 'required_if:type,multiple_choice|nullable|string',
            'options.D' => 'required_if:type,multiple_choice|nullable|string',
            'correct_answer' => 'required_unless:type,essay|string',
            'status' => 'required|in:active,inactive',
        ]);

        // Clean up options
        if ($validated['type'] === 'multiple_choice' && isset($validated['options'])) {
            $validated['options'] = array_filter($validated['options'], fn($v) => !empty($v));
        }

        if ($validated['type'] === 'true_false') {
            $validated['options'] = ['A' => 'Benar', 'B' => 'Salah'];
        }

        $question->update($validated);

        // Sync position (empty = detach all = universal)
        if (!empty($validated['target_position'])) {
            $question->positions()->sync([$validated['target_position']]);
        } else {
            $question->positions()->sync([]);
        }

        // Handle new questions if added
        $newQuestionsCount = 0;
        if ($request->has('new_questions') && is_array($request->new_questions)) {
            foreach ($request->new_questions as $q) {
                if (empty(trim($q['question_text'] ?? ''))) {
                    continue;
                }

                $options = [];
                if ($q['type'] === 'true_false') {
                    $options = ['A' => 'Benar', 'B' => 'Salah'];
                } elseif ($q['type'] === 'multiple_choice' && isset($q['options'])) {
                    $options = array_filter($q['options'], fn($v) => !empty($v));
                }

                Question::create([
                    'question_set_id' => $question->question_set_id,
                    'set_title' => $question->set_title,
                    'skill_id' => $validated['skill_id'],
                    'for_level' => $validated['for_level'],
                    'type' => $q['type'],
                    'question_text' => $q['question_text'],
                    'options' => !empty($options) ? $options : null,
                    'correct_answer' => $q['correct_answer'] ?? null,
                    'status' => $validated['status'],
                ]);
                $newQuestionsCount++;
            }
        }

        $message = 'Soal berhasil diperbarui!';
        if ($newQuestionsCount > 0) {
            $message .= " {$newQuestionsCount} soal baru berhasil ditambahkan.";
        }

        return redirect()
            ->route('cbt.admin.questions.show', $question)
            ->with('success', $message);
    }

    /**
     * Remove the specified question.
     */
    public function destroy(Question $question)
    {
        // Check if question is used in exams
        if ($question->exams()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus soal yang sudah digunakan dalam ujian!');
        }

        $question->delete();

        return redirect()
            ->route('cbt.admin.questions.index')
            ->with('success', 'Soal berhasil dihapus!');
    }

    /**
     * Bulk import questions.
     */
    public function bulkImport(Request $request)
    {
        $request->validate([
            'skill_id' => 'required|exists:skills,id',
            'for_level' => 'required|integer|min:1|max:4',
            'questions' => 'required|array|min:1',
            'questions.*.question_text' => 'required|string',
            'questions.*.options' => 'required|array',
            'questions.*.correct_answer' => 'required|string',
        ]);

        $skill_id = $request->skill_id;
        $for_level = $request->for_level;
        $count = 0;

        foreach ($request->questions as $q) {
            Question::create([
                'skill_id' => $skill_id,
                'for_level' => $for_level,
                'type' => 'multiple_choice',
                'question_text' => $q['question_text'],
                'options' => $q['options'],
                'correct_answer' => $q['correct_answer'],
                'status' => 'active',
            ]);
            $count++;
        }

        return response()->json([
            'success' => true,
            'message' => "{$count} soal berhasil diimport!",
        ]);
    }

    /**
     * Show the form for bulk creating questions.
     */
    public function bulkCreate()
    {
        $skills = Skill::where('is_active', true)->get();
        return view('admin.cbt.questions.bulk-create', compact('skills'));
    }

    /**
     * Store multiple questions at once.
     */
    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'skill_id' => 'required|exists:skills,id',
            'for_level' => 'required|integer|min:1|max:4',
            'questions' => 'required|array|min:1',
            'questions.*.question_text' => 'required|string',
            'questions.*.type' => 'required|in:multiple_choice,true_false,essay',
            'questions.*.options' => 'nullable|array',
            'questions.*.correct_answer' => 'nullable|string',
        ]);

        $skill_id = $validated['skill_id'];
        $for_level = $validated['for_level'];
        $count = 0;

        foreach ($validated['questions'] as $q) {
            // Skip empty questions
            if (empty(trim($q['question_text']))) {
                continue;
            }

            $options = [];
            if ($q['type'] === 'true_false') {
                $options = ['A' => 'Benar', 'B' => 'Salah'];
            } elseif ($q['type'] === 'multiple_choice' && isset($q['options'])) {
                $options = array_filter($q['options'], fn($v) => !empty($v));
            }

            Question::create([
                'skill_id' => $skill_id,
                'for_level' => $for_level,
                'type' => $q['type'],
                'question_text' => $q['question_text'],
                'options' => !empty($options) ? $options : null,
                'correct_answer' => $q['correct_answer'] ?? null,
                'status' => 'active',
            ]);
            $count++;
        }

        return redirect()
            ->route('cbt.admin.questions.index')
            ->with('success', "{$count} soal berhasil ditambahkan!");
    }

    /**
     * Get skills by division (API endpoint)
     */
    public function getSkillsByDivision($divisionId)
    {
        try {
            $division = Division::with(['skills' => function($query) {
                $query->where('is_active', true);
            }])->findOrFail($divisionId);

            $skills = $division->skills->map(function($skill) {
                return [
                    'id' => $skill->id,
                    'name' => $skill->name,
                ];
            });

            return response()->json([
                'success' => true,
                'skills' => $skills
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Division tidak ditemukan',
                'skills' => []
            ], 404);
        }
    }

    /**
     * Get questions from a question set (API endpoint)
     */
    public function getQuestionsFromSet($questionSetId)
    {
        try {
            $questions = Question::where('question_set_id', $questionSetId)
                ->where('status', 'active')
                ->select('id', 'question_text', 'type', 'for_level')
                ->get();

            return response()->json([
                'success' => true,
                'questions' => $questions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Set soal tidak ditemukan',
                'questions' => []
            ], 404);
        }
    }

    /**
     * Show all questions in a question set
     */
    public function showSet($questionSetId)
    {
        $questions = Question::where('question_set_id', $questionSetId)
            ->with('skill')
            ->get();

        if ($questions->isEmpty()) {
            return redirect()
                ->route('cbt.admin.questions.index')
                ->with('error', 'Set soal tidak ditemukan!');
        }

        $setInfo = $questions->first();

        return view('admin.cbt.questions.show-set', compact('questions', 'setInfo'));
    }

    /**
     * Show edit form for question set
     */
    public function editSet($questionSetId)
    {
        $questions = Question::where('question_set_id', $questionSetId)
            ->with(['skill.division.department', 'positions'])
            ->get();

        if ($questions->isEmpty()) {
            return redirect()
                ->route('cbt.admin.questions.index')
                ->with('error', 'Set soal tidak ditemukan!');
        }

        $setInfo = $questions->first();
        $divisions = Division::with('department')->orderBy('name')->get();
        $skills = Skill::where('is_active', true)->get();

        return view('admin.cbt.questions.edit-set', compact('questions', 'setInfo', 'divisions', 'skills'));
    }

    /**
     * Update entire question set
     */
    public function updateSet(Request $request, $questionSetId)
    {
        $questions = Question::where('question_set_id', $questionSetId)->get();

        if ($questions->isEmpty()) {
            return back()->with('error', 'Set soal tidak ditemukan!');
        }

        $validated = $request->validate([
            'set_title' => 'required|string|max:255',
            'skill_id' => 'required|exists:skills,id',
            'for_level' => 'required|integer|min:1|max:4',
            'status' => 'required|in:active,inactive,draft',
            'target_position' => 'nullable|exists:positions,id',
            'questions' => 'required|array|min:1',
            'questions.*.id' => 'nullable|exists:questions,id',
            'questions.*.question_text' => 'required|string',
            'questions.*.type' => 'required|in:multiple_choice,true_false,essay',
            'questions.*.options' => 'nullable|array',
            'questions.*.correct_answer' => 'nullable|string',
            'delete_questions' => 'nullable|array',
            'delete_questions.*' => 'exists:questions,id',
        ]);

        // Handle deletion of questions
        if ($request->has('delete_questions') && is_array($request->delete_questions)) {
            foreach ($request->delete_questions as $questionId) {
                $question = Question::find($questionId);
                
                if ($question && $question->question_set_id == $questionSetId) {
                    // Only delete if not used in exams
                    if (!$question->exams()->exists()) {
                        $question->delete();
                    }
                }
            }
        }

        // Update or create each question
        foreach ($validated['questions'] as $qData) {
            // Skip if this question is marked for deletion
            if (!empty($qData['id']) && in_array($qData['id'], $request->delete_questions ?? [])) {
                continue;
            }

            $options = [];
            if ($qData['type'] === 'true_false') {
                $options = ['A' => 'Benar', 'B' => 'Salah'];
            } elseif ($qData['type'] === 'multiple_choice' && isset($qData['options'])) {
                $options = array_filter($qData['options'], fn($v) => !empty($v));
            }

            $questionData = [
                'set_title' => $validated['set_title'],
                'skill_id' => $validated['skill_id'],
                'for_level' => $validated['for_level'],
                'status' => $validated['status'],
                'question_text' => $qData['question_text'],
                'type' => $qData['type'],
                'options' => !empty($options) ? $options : null,
                'correct_answer' => $qData['correct_answer'] ?? null,
            ];

            if (!empty($qData['id'])) {
                // Update existing question
                $question = Question::find($qData['id']);
                
                if ($question && $question->question_set_id == $questionSetId) {
                    $question->update($questionData);
                    
                    // Sync target position
                    if (!empty($validated['target_position'])) {
                        $question->positions()->sync([$validated['target_position']]);
                    } else {
                        $question->positions()->sync([]);
                    }
                }
            } else {
                // Create new question
                $questionData['question_set_id'] = $questionSetId;
                $newQuestion = Question::create($questionData);
                
                // Attach target position
                if (!empty($validated['target_position'])) {
                    $newQuestion->positions()->attach($validated['target_position']);
                }
            }
        }

        return redirect()
            ->route('cbt.admin.questions.show-set', $questionSetId)
            ->with('success', 'Set soal berhasil diperbarui!');
    }

    /**
     * Delete entire question set
     */
    public function destroySet($questionSetId)
    {
        $questions = Question::where('question_set_id', $questionSetId)->get();

        if ($questions->isEmpty()) {
            return back()->with('error', 'Set soal tidak ditemukan!');
        }

        // Check if any question is used in exams
        foreach ($questions as $question) {
            if ($question->exams()->exists()) {
                return back()->with('error', 'Tidak dapat menghapus set soal yang sudah digunakan dalam ujian!');
            }
        }

        $count = $questions->count();
        $setTitle = $questions->first()->set_title;

        Question::where('question_set_id', $questionSetId)->delete();

        return redirect()
            ->route('cbt.admin.questions.index')
            ->with('success', "Set soal \"{$setTitle}\" dengan {$count} soal berhasil dihapus!");
    }
}
