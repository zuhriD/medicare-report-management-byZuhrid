<?php

namespace Database\Seeders;

use App\Models\DailyReport;
use App\Models\FeatureChecklist;
use App\Models\Module;
use App\Models\ModulePlatform;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use App\Models\WeeklyReport;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $teams = collect([
            'Mobile Developer',
            'Web Developer',
            'Backend Developer',
            'UI/UX Designer',
            'Data Entry',
        ])->mapWithKeys(function (string $teamName): array {
            $team = Team::query()->create(['name' => $teamName]);

            return [$teamName => $team];
        });

        $admin = User::factory()->create([
            'name' => 'MRM Admin',
            'email' => 'admin@mrm.test',
            'role' => 'admin',
            'team_id' => $teams['Web Developer']->id,
        ]);

        $leadPrimary = User::factory()->create([
            'name' => 'Primary Lead',
            'email' => 'lead1@mrm.test',
            'role' => 'lead',
            'team_id' => $teams['Backend Developer']->id,
        ]);

        $leadSecondary = User::factory()->create([
            'name' => 'Secondary Lead',
            'email' => 'lead2@mrm.test',
            'role' => 'lead',
            'team_id' => $teams['Mobile Developer']->id,
        ]);

        $developerA = User::factory()->create([
            'name' => 'Mobile Dev One',
            'email' => 'dev1@mrm.test',
            'role' => 'developer',
            'team_id' => $teams['Mobile Developer']->id,
        ]);

        $developerB = User::factory()->create([
            'name' => 'Backend Dev One',
            'email' => 'dev2@mrm.test',
            'role' => 'developer',
            'team_id' => $teams['Backend Developer']->id,
        ]);

        $project = Project::query()->create([
            'name' => 'Medicare',
            'code' => 'MED',
        ]);

        $project->leads()->attach([$leadPrimary->id, $leadSecondary->id]);

        $modules = collect([
            ['name' => 'Home Nursing', 'code' => 'HN', 'status' => 'ongoing', 'phase' => 'development'],
            ['name' => 'Doctor Home Visit', 'code' => 'DHV', 'status' => 'ongoing', 'phase' => 'development'],
            ['name' => 'Visit Clinic', 'code' => 'VC', 'status' => 'completed', 'phase' => 'testing'],
        ])->map(function (array $moduleData) use ($project): Module {
            return Module::query()->create([
                'project_id' => $project->id,
                ...$moduleData,
            ]);
        });

        $platforms = collect([
            [$modules[0], 'Admin Website', 85],
            [$modules[0], 'User App', 75],
            [$modules[1], 'API', 70],
            [$modules[1], 'User App', 60],
            [$modules[2], 'Admin Website', 100],
        ])->map(function (array $item): ModulePlatform {
            [$module, $platformName, $progress] = $item;

            return ModulePlatform::query()->create([
                'module_id' => $module->id,
                'platform_name' => $platformName,
                'progress_percentage' => $progress,
            ]);
        });

        collect([
            [$platforms[0], 'Create patient', 'done'],
            [$platforms[0], 'Select patient', 'in_progress'],
            [$platforms[1], 'Place order', 'done'],
            [$platforms[2], 'Create visit request', 'in_progress'],
            [$platforms[3], 'View order summary', 'not_started'],
            [$platforms[4], 'Regression test pass', 'done'],
        ])->each(function (array $item): void {
            [$platform, $featureName, $status] = $item;

            FeatureChecklist::query()->create([
                'module_platform_id' => $platform->id,
                'feature_name' => $featureName,
                'status' => $status,
            ]);
        });

        $periodStart = Carbon::now()->startOfWeek();
        $periodEnd = Carbon::now()->endOfWeek();

        $weeklyReport = WeeklyReport::query()->create([
            'project_id' => $project->id,
            'generated_by_user_id' => $leadPrimary->id,
            'report_number' => 1,
            'period_start' => $periodStart->toDateString(),
            'period_end' => $periodEnd->toDateString(),
            'topic' => 'Sprint progress and feature readiness',
            'executive_summary' => 'Weekly progress is stable across mobile and backend workstreams.',
            'plan_of_actions' => 'Complete pending checks and prepare regression coverage.',
            'status' => 'published',
            'generated_pdf_path' => null,
        ]);

        DailyReport::query()->create([
            'user_id' => $developerA->id,
            'project_id' => $project->id,
            'module_id' => $modules[0]->id,
            'linked_feature_id' => null,
            'report_date' => $periodStart->copy()->addDay()->toDateString(),
            'progress_text' => 'Implemented patient selection flow and started UI verification.',
        ]);

        DailyReport::query()->create([
            'user_id' => $developerA->id,
            'project_id' => $project->id,
            'module_id' => $modules[0]->id,
            'linked_feature_id' => null,
            'report_date' => $periodStart->copy()->addDays(2)->toDateString(),
            'progress_text' => 'Fixed edge case on user app order placement.',
        ]);

        DailyReport::query()->create([
            'user_id' => $developerB->id,
            'project_id' => $project->id,
            'module_id' => $modules[1]->id,
            'linked_feature_id' => null,
            'report_date' => $periodStart->copy()->addDay()->toDateString(),
            'progress_text' => 'Updated visit request API and aligned payload validation.',
        ]);

        DailyReport::query()->create([
            'user_id' => $developerB->id,
            'project_id' => $project->id,
            'module_id' => null,
            'linked_feature_id' => null,
            'report_date' => $periodStart->copy()->addDays(3)->toDateString(),
            'progress_text' => 'Handled general bug fixes and QA follow-up notes.',
        ]);
    }
}
