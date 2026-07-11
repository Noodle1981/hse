<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DynamicChecklist extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category',        // Legacy field - mantener por compatibilidad
        'category_id',     // Nueva relación
        'subcategory_id',  // Nueva relación
        'registry_code',   // Código normativo (ej: EP-REG-008)
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
     * Categoría del checklist
     */
    /**
     * Categoría del checklist
     */
    public function categoryRelation(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Subcategoría del checklist
     */
    public function subcategoryRelation(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class, 'subcategory_id');
    }

    /**
     * Secciones del checklist (para agrupar preguntas)
     */
    public function sections(): HasMany
    {
        return $this->hasMany(ChecklistSection::class)->orderBy('order');
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
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Capacitaciones asociadas a este checklist
     */
    public function trainings(): HasMany
    {
        return $this->hasMany(Training::class, 'dynamic_checklist_id');
    }
}
