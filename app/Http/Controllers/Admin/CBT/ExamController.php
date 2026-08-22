<?php

namespace App\Http\Controllers\Admin\CBT;

use App\Events\DashboardStatsUpdated;
use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * ExamController
 *
 * Admin controller for managing CBT Exams.
 * Handles CRUD operations for exams and question assignment.
 */
class ExamController extends Controller
{
    /**
     * Display a listing of all exams.
     */
    public function index()
    {
        $exams = Exam::with(['skill', 'examQuestions'])
            ->withCount('examQuestions')
            ->latest()
            ->get();

        $skills = Cache::remember('active_skills', 3600, function () {
            return Skill::where('is_active', true)->get();
        });

        return view('admin.cbt.exams.index', compact('exams', 'skills'));
    }

    /**
     * Show the form for creating a new exam.
     */
    public function create()
    {
        $divisions = \App\Models\Division::all();

        // Get question sets grouped by skill_id
        $questionSets = Question::select('question_set_id', 'set_title', 'skill_id', 'for_level', 'status')
            ->selectRaw('COUNT(*) as question_count')
            ->where('status', 'active')
            ->groupBy('question_set_id', 'set_title', 'skill_id', 'for_level', 'status')
            ->with('skill')
            ->get()
            ->groupBy('skill_id');

        return view('admin.cbt.exams.create', compact('divisions', 'questionSets'));
    }

    /**
     * Store a newly created exam.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'skill_id' => 'required|exists:skills,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_level' => 'required|integer|min:1|max:4',
            'passing_score' => 'required|integer|min:0|max:100',
            'duration_minutes' => 'required|integer|min:1|max:480',
            'is_published' => 'boolean',
            'questions' => 'array',
            'questions.*.id' => 'exists:questions,id',
        ]);

        // Ujian tidak boleh mencampur esai dengan PG / benar-salah
        if (! empty($validated['questions'])) {
            $types = Question::whereIn('id', collect($validated['questions'])->pluck('id'))->pluck('type');
            $hasEssay = $types->contains('essay');
            $hasAuto = $types->contains(fn ($t) => in_array($t, ['multiple_choice', 'true_false']));

            if ($hasEssay && $hasAuto) {
                return back()
                    ->withInput()
                    ->with('error', 'Ujian tidak boleh mencampur soal esai dengan pilihan ganda / benar-salah.');
            }
        }

        DB::beginTransaction();

        try {
            $exam = Exam::create([
                'skill_id' => $validated['skill_id'],
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'target_level' => $validated['target_level'],
                'passing_score' => $validated['passing_score'],
                'duration_minutes' => $validated['duration_minutes'],
                'is_published' => $validated['is_published'] ?? false,
            ]);

            // Attach questions (no weight, percentage-based scoring)
            if (! empty($validated['questions'])) {
                $questionIds = collect($validated['questions'])->pluck('id')->all();
                $weights = array_fill_keys($questionIds, 1);
                $exam->attachQuestionsWithSnapshot($questionIds, $weights);
            }

            DB::commit();

            DashboardStatsUpdated::dispatch();

            return redirect()
                ->route('cbt.admin.exams.show', $exam)
                ->with('success', 'Ujian berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Gagal membuat ujian: '.$e->getMessage());
        }
    }

    /**
     * Display the specified exam.
     */
    public function show(Exam $exam)
    {
        $exam->load(['skill', 'examQuestions', 'sessions.employee']);

        return view('admin.cbt.exams.show', compact('exam'));
    }

    /**
     * Show the form for editing the specified exam.
     */
    public function edit(Exam $exam)
    {
        $exam->load(['questions' => function ($query) {
            $query->orderBy('exam_question.order');
        }]);

        $skills = Cache::remember('active_skills', 3600, function () {
            return Skill::where('is_active', true)->get();
        });
        $availableQuestions = Question::where('status', 'active')
            ->where('skill_id', $exam->skill_id)
            ->where('for_level', '<=', $exam->target_level)
            ->get();

        return view('admin.cbt.exams.edit', compact('exam', 'skills', 'availableQuestions'));
    }

    /**
     * Update the specified exam.
     */
    public function update(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_level' => 'required|integer|min:1|max:4',
            'passing_score' => 'required|integer|min:0|max:100',
            'duration_minutes' => 'required|integer|min:1|max:480',
            'is_published' => 'boolean',
            'questions' => 'array',
            'questions.*.id' => 'exists:questions,id',
        ]);

        DB::beginTransaction();

        try {
            $exam->update([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'target_level' => $validated['target_level'],
                'passing_score' => $validated['passing_score'],
                'duration_minutes' => $validated['duration_minutes'],
                'is_published' => $validated['is_published'] ?? false,
            ]);

            // Sync questions (no weight, percentage-based scoring)
            if (isset($validated['questions'])) {
                $questionIds = collect($validated['questions'])->pluck('id')->all();
                $weights = array_fill_keys($questionIds, 1);
                $exam->syncQuestionsWithSnapshot($questionIds, $weights);
            }

            DB::commit();

            DashboardStatsUpdated::dispatch();

            return redirect()
                ->route('cbt.admin.exams.show', $exam)
                ->with('success', 'Ujian berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui ujian: '.$e->getMessage());
        }
    }

    /**
     * Remove the specified exam.
     */
    public function destroy(Exam $exam)
    {
        // Check if exam has sessions
        if ($exam->sessions()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus ujian yang sudah memiliki sesi!');
        }

        $exam->questions()->detach();
        $exam->delete();

        DashboardStatsUpdated::dispatch();

        return redirect()
            ->route('cbt.admin.exams.index')
            ->with('success', 'Ujian berhasil dihapus!');
    }

    /**
     * Toggle publish status.
     */
    public function togglePublish(Exam $exam)
    {
        $exam->update(['is_published' => ! $exam->is_published]);

        DashboardStatsUpdated::dispatch();

        $status = $exam->is_published ? 'dipublikasikan' : 'di-unpublish';

        return back()->with('success', "Ujian berhasil {$status}!");
    }
}
