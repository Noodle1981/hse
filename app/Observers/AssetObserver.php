<?php

namespace App\Observers;

use App\Models\Asset;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AssetObserver
{
    /**
     * Handle the Asset "updated" event.
     */
    public function updated(Asset $asset): void
    {
        $dirty = $asset->getDirty();
        $original = array_intersect_key($asset->getOriginal(), $dirty);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'asset_updated',
            'target_type' => Asset::class,
            'target_id' => (string) $asset->id,
            'payload' => [
                'dirty' => $dirty,
                'original' => $original,
            ],
        ]);
    }
}
