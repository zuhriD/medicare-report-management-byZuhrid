<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class WeeklyReportNumberService
{
    public function nextForProject(int $projectId): int
    {
        return DB::transaction(function () use ($projectId) {
            $sequence = DB::table('weekly_report_number_sequences')
                ->where('project_id', $projectId)
                ->lockForUpdate()
                ->first();

            if ($sequence === null) {
                DB::table('weekly_report_number_sequences')->insert([
                    'project_id' => $projectId,
                    'last_number' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                return 1;
            }

            $nextNumber = $sequence->last_number + 1;

            DB::table('weekly_report_number_sequences')
                ->where('project_id', $projectId)
                ->update([
                    'last_number' => $nextNumber,
                    'updated_at' => now(),
                ]);

            return $nextNumber;
        });
    }
}
