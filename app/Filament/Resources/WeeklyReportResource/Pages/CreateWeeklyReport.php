<?php

namespace App\Filament\Resources\WeeklyReportResource\Pages;

use App\Services\WeeklyReportNumberService;
use App\Filament\Resources\WeeklyReportResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWeeklyReport extends CreateRecord
{
    protected static string $resource = WeeklyReportResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['generated_by_user_id'] = auth()->id();
        $data['report_number'] = app(WeeklyReportNumberService::class)->nextForProject((int) $data['project_id']);

        return $data;
    }
}
