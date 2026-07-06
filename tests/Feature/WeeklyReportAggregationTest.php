<?php

namespace Tests\Feature;

use App\Models\DailyReport;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use App\Models\WeeklyReport;
use App\Services\WeeklyReportAggregationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WeeklyReportAggregationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_refreshes_individual_summaries_from_daily_reports(): void
    {
        $team = Team::query()->create(['name' => 'Backend Developer']);
        $lead = User::factory()->create([
            'name' => 'Lead',
            'email' => 'lead@example.test',
            'role' => 'lead',
            'team_id' => $team->id,
        ]);
        $developer = User::factory()->create([
            'name' => 'Developer',
            'email' => 'dev@example.test',
            'role' => 'developer',
            'team_id' => $team->id,
        ]);
        $project = Project::query()->create([
            'name' => 'Medicare',
            'code' => 'MED',
        ]);
        $project->leads()->attach($lead->id);

        $weeklyReport = WeeklyReport::query()->create([
            'project_id' => $project->id,
            'generated_by_user_id' => $lead->id,
            'report_number' => 1,
            'period_start' => now()->startOfWeek()->toDateString(),
            'period_end' => now()->endOfWeek()->toDateString(),
            'topic' => 'Weekly Progress',
            'status' => 'draft',
        ]);

        DailyReport::query()->create([
            'user_id' => $developer->id,
            'project_id' => $project->id,
            'module_id' => null,
            'linked_feature_id' => null,
            'report_date' => now()->startOfWeek()->addDay()->toDateString(),
            'progress_text' => 'Completed API update.',
        ]);

        app(WeeklyReportAggregationService::class)->refreshDraft($weeklyReport);

        $this->assertDatabaseHas('weekly_report_individual_summaries', [
            'weekly_report_id' => $weeklyReport->id,
            'user_id' => $developer->id,
        ]);

        $summary = $weeklyReport->fresh()->individualSummaries->first();

        $this->assertStringContainsString('Completed API update.', $summary->summary_text);
    }
}