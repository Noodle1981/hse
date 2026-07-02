<?php

namespace App\Events;

use App\Models\DynamicInspection;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InspectionCompleted
{
    use Dispatchable, SerializesModels;

    public DynamicInspection $inspection;

    public function __construct(DynamicInspection $inspection)
    {
        $this->inspection = $inspection;
    }
}
