<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'empresa_id',
        'sede_id',
        'asset_type_id',
        'name',
        'identifier',
        'status',
        'metadata',
        'photos',
    ];

    protected $casts = [
        'metadata' => 'array',
        'photos' => 'array',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    public function assetType()
    {
        return $this->belongsTo(AssetType::class);
    }

    public function dynamicInspections()
    {
        return $this->hasMany(DynamicInspection::class);
    }

    public function incidentes()
    {
        return $this->hasMany(Incidente::class);
    }
}
