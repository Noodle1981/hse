<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class IncidenteFoto extends Model
{
    use HasFactory;

    protected $table = 'incidente_fotos';

    protected $fillable = [
        'incidente_id',
        'ruta_archivo',
        'descripcion',
    ];

    /**
     * El incidente al que pertenece esta foto
     */
    public function incidente(): BelongsTo
    {
        return $this->belongsTo(Incidente::class);
    }

    /**
     * Obtener la URL completa de la foto
     */
    public function getUrl(): string
    {
        return Storage::url($this->ruta_archivo);
    }

    /**
     * Eliminar el archivo físico al borrar el registro
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($foto) {
            if (Storage::exists($foto->ruta_archivo)) {
                Storage::delete($foto->ruta_archivo);
            }
        });
    }
}
