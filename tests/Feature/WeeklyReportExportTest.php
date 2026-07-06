<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use App\Models\WeeklyReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WeeklyReportExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_lead_can_export_a_weekly_report_pdf(): void
    {
        Storage::fake('public');

        $team = Team::query()->create(['name' => 'Backend Developer']);
        $lead = User::factory()->create([
            'name' => 'Lead',
            'email' => 'lead@example.test',
            'role' => 'lead',
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
            'executive_summary' => 'Summary text',
            'plan_of_actions' => 'Plan text',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($lead)->get(route('weekly-reports.export-pdf', $weeklyReport));

        $response->assertOk();
        $response->assertHeader('content-type');

        $weeklyReport->refresh();

        $this->assertNotNull($weeklyReport->generated_pdf_path);
        Storage::disk('public')->assertExists($weeklyReport->generated_pdf_path);
    }
}