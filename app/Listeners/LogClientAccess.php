<?php

namespace App\Listeners;

use App\Events\ClientAccessModified;
use App\Models\AuditLog;

class LogClientAccess
{
    public function handle(ClientAccessModified $event): void
    {
        AuditLog::create([
            'user_id' => $event->userId,
            'action' => $event->action,
            'target_type' => get_class($event->consultant),
            'target_id' => $event->consultant->id,
            'payload' => $event->changes,
        ]);
    }
}
