<?php

namespace App\Filament\Resources\ModulePlatformResource\Pages;

use App\Filament\Resources\ModulePlatformResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditModulePlatform extends EditRecord
{
    protected static string $resource = ModulePlatformResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
