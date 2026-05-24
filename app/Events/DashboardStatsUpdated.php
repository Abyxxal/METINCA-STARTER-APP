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
        $pendingVerification = \App\Models\ExamSession::where('status', 'submitted')->count();
        $pendingApproval = \App\Models\ExamSession::where('status', 'verified_pass')
            ->where(function($q) { $q->where('manager_decision', 'pending')->orWhereNull('manager_decision'); })
            ->count();

        $this->stats = [
            'pending_verification' => $pendingVerification,
            'pending_approval' => $pendingApproval,
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
