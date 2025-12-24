<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DynamicChecklist extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category',
        'is_active',
        'is_draft',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_draft' => 'boolean',
    ];

    /**
     * Las preguntas de este checklist
     */
    public function questions(): HasMany
    {
        return $this->hasMany(DynamicQuestion::class)->orderBy('order');
    }

    /**
     * Las inspecciones (ejecuciones) de este checklist
     */
    public function inspections(): HasMany
    {
        return $this->hasMany(DynamicInspection::class);
    }

    /**
     * Scope para obtener solo checklists activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para filtrar por categoría
     */
    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
}
