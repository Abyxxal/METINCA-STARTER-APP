<?php

namespace App\View\Composers;

use App\Models\ExamSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * SidebarComposer
 *
 * Memasok data badge/dropdown sidebar & topbar layout admin
 * (dipindahkan dari blok @php di layouts/app.blade.php agar views
 * bebas logika/query — remediasi arsitektur MVC R1).
 *
 * Perilaku dijaga identik dengan implementasi sebelumnya.
 */
class SidebarComposer
{
    public function compose(View $view): void
    {
        if (! Auth::check()) {
            return;
        }

        $user = Auth::user();

        // Sidebar admin: jumlah sesi menunggu verifikasi
        $pendingCount = ExamSession::where('status', 'submitted')->count();

        // Sidebar manajer: lulus verifikasi namun keputusan manajer masih pending
        $pendingApprovalCount = ExamSession::where('status', 'verified_pass')
            ->where(function ($q) {
                $q->where('manager_decision', 'pending')->orWhereNull('manager_decision');
            })
            ->count();

        // Topbar dropdown: daftar sesi menunggu verifikasi (+ relasi utk tampilan)
        $pendingSessions = ExamSession::where('status', 'submitted')
            ->with(['employee', 'exam'])
            ->orderBy('submitted_at', 'desc')
            ->limit(5)
            ->get();

        // Paritas perilaku blade lama: $pendingCount di-reassign dari koleksi dropdown
        $pendingCount = $pendingSessions->count();

        // Sidebar karyawan: ujian yang harus/sedang dikerjakan
        $pendingExamCount = 0;
        if ($user->employee) {
            $pendingExamCount = ExamSession::where('employee_nik', $user->employee->nik)
                ->whereIn('status', ['assigned', 'started'])
                ->count();
        }

        $view->with(compact('pendingCount', 'pendingApprovalCount', 'pendingSessions', 'pendingExamCount'));
    }
}
