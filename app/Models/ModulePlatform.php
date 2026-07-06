<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModulePlatform extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id',
        'platform_name',
        'progress_percentage',
    ];

    protected $casts = [
        'progress_percentage' => 'integer',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function featureChecklists(): HasMany
    {
        return $this->hasMany(FeatureChecklist::class);
    }
}
