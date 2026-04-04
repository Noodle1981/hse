<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class HallazgoFoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'hallazgo_id',
        'tipo',
        'ruta_archivo',
        'descripcion',
    ];

    // ============================================
    // CONSTANTES
    // ============================================

    // Tipos de foto
    public const TIPO_ANTES = 'antes';
    public const TIPO_DURANTE = 'durante';
    public const TIPO_DESPUES = 'despues';

    // ============================================
    // RELACIONES
    // ============================================

    /**
     * El hallazgo al que pertenece esta foto
     */
    public function hallazgo(): BelongsTo
    {
        return $this->belongsTo(Hallazgo::class);
    }

    // ============================================
    // SCOPES
    // ============================================

    /**
     * Fotos tomadas antes de la corrección
     */
    public function scopeAntes($query)
    {
        return $query->where('tipo', self::TIPO_ANTES);
    }

    /**
     * Fotos tomadas durante la corrección
     */
    public function scopeDurante($query)
    {
        return $query->where('tipo', self::TIPO_DURANTE);
    }

    /**
     * Fotos tomadas después de la corrección
     */
    public function scopeDespues($query)
    {
        return $query->where('tipo', self::TIPO_DESPUES);
    }

    // ============================================
    // MÉTODOS AUXILIARES
    // ============================================

    /**
     * Obtener la URL completa de la foto (Accessor)
     */
    public function getUrlAttribute(): string
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $storage */
        $storage = Storage::disk('public');
        return $storage->url($this->ruta_archivo);
    }

    /**
     * Verificar si el archivo existe
     */
    public function archivoExiste(): bool
    {
        return Storage::exists($this->ruta_archivo);
    }

    /**
     * Eliminar el archivo físico
     */
    public function eliminarArchivo(): bool
    {
        if ($this->archivoExiste()) {
            return Storage::delete($this->ruta_archivo);
        }

        return false;
    }

    /**
     * Obtener el tamaño del archivo en bytes
     */
    public function getTamano(): ?int
    {
        if ($this->archivoExiste()) {
            return Storage::size($this->ruta_archivo);
        }

        return null;
    }

    /**
     * Obtener el tamaño formateado (KB, MB, etc.)
     */
    public function getTamanoFormateado(): string
    {
        $bytes = $this->getTamano();

        if ($bytes === null) {
            return 'N/A';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Event: Al eliminar el modelo, eliminar también el archivo
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($foto) {
            $foto->eliminarArchivo();
        });
    }
}

