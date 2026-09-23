<?php

namespace App\Http\Controllers\Admin\CBT;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Division;
use App\Models\ExamPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * ExamPeriodController
 *
 * Mengelola Periode Ujian (jadwal/rentang ujian) yang ditetapkan Manager.
 * - Read (index): bisa diakses Supervisor + Manager.
 * - Write (create/store/edit/update/destroy): khusus Manager (disaring route is.manager).
 */
class ExamPeriodController extends Controller
{
    /**
     * Tampilkan daftar periode ujian.
     */
    public function index()
    {
        $periods = ExamPeriod::with(['division.department', 'creator'])
            ->latest()
            ->get();

        return view('admin.cbt.exam-periods.index', compact('periods'));
    }

    /**
     * Validasi & konsistensi scope departemen/divisi.
     */
    private function validatedData(Request $request): array
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'division_id' => 'nullable|exists:divisions,id',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'notes' => 'nullable|string|max:1000',
        ]);

        if (! empty($validated['department_id']) && ! empty($validated['division_id'])) {
            $division = Division::find($validated['division_id']);
            if ($division && $division->department_id !== (int) $validated['department_id']) {
                throw ValidationException::withMessages([
                    'division_id' => 'Divisi terpilih tidak berada dalam departemen yang dipilih.',
                ]);
            }
        }

        return $validated;
    }

    /**
     * Tampilkan form tambah periode.
     */
    public function create()
    {
        $divisions = Division::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();

        return view('admin.cbt.exam-periods.create', compact('divisions', 'departments'));
    }

    /**
     * Simpan periode ujian baru.
     */
    public function store(Request $request)
    {
        $validated = $this->validatedData($request);

        $period = ExamPeriod::create([
            'name' => $validated['name'],
            'department_id' => $validated['department_id'] ?? null,
            'division_id' => $validated['division_id'] ?? null,
            'start_at' => $validated['start_at'],
            'end_at' => $validated['end_at'],
            'notes' => $validated['notes'] ?? null,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('cbt.admin.exam-periods.index')
            ->with('success', "Periode ujian '{$period->name}' berhasil dibuat.");
    }

    /**
     * Tampilkan form edit periode.
     */
    public function edit(ExamPeriod $period)
    {
        $divisions = Division::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();

        return view('admin.cbt.exam-periods.edit', compact('period', 'divisions', 'departments'));
    }

    /**
     * Perbarui periode ujian.
     */
    public function update(Request $request, ExamPeriod $period)
    {
        $validated = $this->validatedData($request);

        $period->update([
            'name' => $validated['name'],
            'department_id' => $validated['department_id'] ?? null,
            'division_id' => $validated['division_id'] ?? null,
            'start_at' => $validated['start_at'],
            'end_at' => $validated['end_at'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('cbt.admin.exam-periods.index')
            ->with('success', "Periode ujian '{$period->name}' berhasil diperbarui.");
    }

    /**
     * Hapus periode ujian.
     */
    public function destroy(ExamPeriod $period)
    {
        $name = $period->name;
        $period->delete();

        return redirect()->route('cbt.admin.exam-periods.index')
            ->with('success', "Periode ujian '{$name}' berhasil dihapus.");
    }
}