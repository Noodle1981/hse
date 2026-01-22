<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChecklistAssignment extends Model
{
    protected $fillable = [
        'dynamic_checklist_id',
        'user_id',
        'sede_id',
        'empresa_id',
        'frequency',
        'due_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    // Relaciones
    public function checklist(): BelongsTo
    {
        return $this->belongsTo(DynamicChecklist::class, 'dynamic_checklist_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class);
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeReassigned($query)
    {
        return $query->where('status', 'reassigned');
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                     ->where('status', '!=', 'completed');
    }

    public function scopeForEmpresa($query, int $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopeForSede($query, int $sedeId)
    {
        return $query->where('sede_id', $sedeId);
    }

    // Helpers
    public function isOverdue(): bool
    {
        return $this->due_date && 
               $this->due_date->isPast() && 
               $this->status !== 'completed';
    }

    public function getStatusBadgeColor(): string
    {
        if ($this->isOverdue()) {
            return 'red';
        }

        return match($this->status) {
            'pending' => 'yellow',
            'in_progress' => 'blue',
            'completed' => 'green',
            'reassigned' => 'orange',
            default => 'gray',
        };
    }

    public function getFrequencyLabel(): string
    {
        return match($this->frequency) {
            'once' => 'Única vez',
            'weekly' => 'Semanal',
            'monthly' => 'Mensual',
            'quarterly' => 'Trimestral',
            'yearly' => 'Anual',
            default => $this->frequency,
        };
    }
}
