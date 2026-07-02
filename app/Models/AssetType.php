<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetType extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id',
        'category_id',
        'name',
        'description',
        'metadata_schema',
        'notification_emails',
    ];

    protected $casts = [
        'metadata_schema' => 'array',
        'notification_emails' => 'array',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}
