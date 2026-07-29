<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DashboardStatsUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $stats;

    public function __construct()
    {
        $employee = new \App\Models\Employee;
        $question = new \App\Models\Question;
        $session = new \App\Models\ExamSession;
        $carbon = new \Carbon\Carbon;

        $pendingVerification = $session->where('status', 'submitted')->count();
        $pendingApproval = $session->where('status', 'verified_pass')
            ->where(function($q) { $q->where('manager_decision', 'pending')->orWhereNull('manager_decision'); })
            ->count();

        $this->stats = [
            'total_employees'        => $employee->where('status', 'Aktif')->count(),
            'total_questions'        => $question->where('status', 'active')
                                        ->whereNotNull('question_set_id')
                                        ->distinct()
                                        ->count('question_set_id'),
            'pending_verification'   => $pendingVerification,
            'pending_approval'       => $pendingApproval,
            'active_exams_this_month'=> $session->whereMonth('created_at', $carbon::now()->month)
                                        ->whereYear('created_at', $carbon::now()->year)
                                        ->whereIn('status', ['assigned', 'started', 'submitted'])
                                        ->count(),
        ];
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('admin.dashboard'),
        ];
    }

    public function broadcastWith(): array
    {
        return $this->stats;
    }
}
