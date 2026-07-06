<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WeeklyReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'generated_by_user_id',
        'report_number',
        'period_start',
        'period_end',
        'topic',
        'executive_summary',
        'plan_of_actions',
        'status',
        'generated_pdf_path',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by_user_id');
    }

    public function individualSummaries(): HasMany
    {
        return $this->hasMany(WeeklyReportIndividualSummary::class);
    }
}
