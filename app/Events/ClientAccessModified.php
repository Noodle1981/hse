<?php

namespace App\Events;

use App\Models\Consultora;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClientAccessModified
{
    use Dispatchable, SerializesModels;

    public $consultant;

    public $action;

    public $changes;

    public $userId;

    public function __construct(Consultora $consultant, string $action, array $changes = [], ?int $userId = null)
    {
        $this->consultant = $consultant;
        $this->action = $action;
        $this->changes = $changes;
        $this->userId = $userId ?? auth()->id();
    }
}
