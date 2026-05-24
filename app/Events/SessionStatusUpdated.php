<?php

namespace App\Events;

use App\Models\ExamSession;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SessionStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ExamSession $session;
    public string $action;

    public function __construct(ExamSession $session, string $action = 'updated')
    {
        $this->session = $session;
        $this->action = $action;
    }

    public function broadcastOn(): array
    {
        $channels = [];

        if ($this->session->employee) {
            $channels[] = new Channel('employee.' . $this->session->employee->nik);
        }

        return $channels;
    }

    public function broadcastWith(): array
    {
        return [
            'session_id' => $this->session->id,
            'exam_title' => $this->session->exam?->title,
            'status' => $this->session->status,
            'score' => $this->session->score,
            'employee_nik' => $this->session->employee_nik,
            'action' => $this->action,
            'verified_at' => $this->session->verified_at?->toISOString(),
            'manager_decision' => $this->session->manager_decision,
        ];
    }
}
