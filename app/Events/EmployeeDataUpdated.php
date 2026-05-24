<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeDataUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $action;
    public ?string $employeeNik;
    public ?string $employeeName;

    public function __construct(string $action, ?string $employeeNik = null, ?string $employeeName = null)
    {
        $this->action = $action;
        $this->employeeNik = $employeeNik;
        $this->employeeName = $employeeName;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('admin.dashboard'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'action' => $this->action,
            'employee_nik' => $this->employeeNik,
            'employee_name' => $this->employeeName,
            'timestamp' => now()->toISOString(),
        ];
    }
}
