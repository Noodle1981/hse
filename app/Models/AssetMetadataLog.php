<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetMetadataLog extends Model
{
    use HasFactory;

    // Desactivamos timestamps estándar porque solo usamos created_at
    public $timestamps = false;

    protected $fillable = [
        'asset_id',
        'dynamic_inspection_id',
        'field_key',
        'old_value',
        'new_value',
        'changed_by',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function inspection()
    {
        return $this->belongsTo(DynamicInspection::class, 'dynamic_inspection_id');
    }

    public function changedByUser()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
