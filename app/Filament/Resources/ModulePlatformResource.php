<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ModulePlatformResource\Pages;
use App\Filament\Resources\ModulePlatformResource\RelationManagers;
use App\Filament\Resources\ModulePlatformResource\RelationManagers\FeatureChecklistsRelationManager;
use App\Models\ModulePlatform;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ModulePlatformResource extends Resource
{
    protected static ?string $model = ModulePlatform::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('module_id')
                    ->relationship('module', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('platform_name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('progress_percentage')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('module.name')
                    ->label('Module')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('platform_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('progress_percentage')
                    ->suffix('%')
                    ->sortable(),
            ])
            ->filters([
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            FeatureChecklistsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListModulePlatforms::route('/'),
            'create' => Pages\CreateModulePlatform::route('/create'),
            'edit' => Pages\EditModulePlatform::route('/{record}/edit'),
        ];
    }
}
