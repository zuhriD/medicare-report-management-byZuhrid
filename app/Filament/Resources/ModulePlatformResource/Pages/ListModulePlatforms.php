<?php

namespace App\Filament\Resources\ModulePlatformResource\Pages;

use App\Filament\Resources\ModulePlatformResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListModulePlatforms extends ListRecords
{
    protected static string $resource = ModulePlatformResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
