<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeatureChecklist extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_platform_id',
        'feature_name',
        'status',
        'notes',
    ];

    public function modulePlatform(): BelongsTo
    {
        return $this->belongsTo(ModulePlatform::class);
    }
}
