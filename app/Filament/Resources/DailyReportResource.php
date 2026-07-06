<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DailyReportResource\Pages;
use App\Models\DailyReport;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

class DailyReportResource extends Resource
{
    protected static ?string $model = DailyReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('user_id')
                    ->default(fn (): ?int => auth()->id()),
                Select::make('project_id')
                    ->relationship('project', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('module_id')
                    ->relationship('module', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Select::make('linked_feature_id')
                    ->relationship('linkedFeature', 'feature_name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                DatePicker::make('report_date')
                    ->default(now())
                    ->required(),
                Textarea::make('progress_text')
                    ->required()
                    ->rows(6)
                    ->columnSpanFull(),
                FileUpload::make('image_path')
                    ->label('Image')
                    ->image()
                    ->disk(fn (): string => self::publicFileDisk())
                    ->directory('daily-reports')
                    ->imagePreviewHeight('200')
                    ->getUploadedFileUsing(function (FileUpload $component, string $file): ?array {
                        /** @var FilesystemAdapter $storage */
                        $storage = $component->getDisk();

                        if (! $storage->exists($file)) {
                            return null;
                        }

                        return [
                            'name' => basename($file),
                            'size' => $storage->size($file),
                            'type' => $storage->mimeType($file),
                            'url' => self::publicStorageUrl($file, $component->getDiskName()),
                        ];
                    })
                    ->maxSize(5120)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('report_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Developer')
                    ->searchable(),
                TextColumn::make('project.name')
                    ->label('Project')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('module.name')
                    ->label('Module')
                    ->toggleable(),
                ImageColumn::make('image_path')
                    ->label('Image')
                    ->getStateUsing(fn (DailyReport $record): ?string => $record->image_path
                        ? self::publicStorageUrl($record->image_path, self::publicFileDisk())
                        : null)
                    ->square()
                    ->toggleable(),
                TextColumn::make('progress_text')
                    ->limit(60),
            ])
            ->filters([
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    private static function publicFileDisk(): string
    {
        return config('filesystems.public_disk', 'public');
    }

    private static function publicStorageUrl(string $path, string $disk): string
    {
        if (filter_var($path, FILTER_VALIDATE_URL) !== false) {
            return $path;
        }

        if ($disk !== 'public') {
            $diskConfig = config("filesystems.disks.{$disk}", []);

            if (filled($diskConfig['url'] ?? null)) {
                return rtrim($diskConfig['url'], '/').'/'.ltrim($path, '/');
            }

            if ($disk === 'gcs' && filled($diskConfig['bucket'] ?? null)) {
                $prefix = trim($diskConfig['path_prefix'] ?? '', '/');
                $prefixedPath = trim($prefix.'/'.$path, '/');

                return 'https://storage.googleapis.com/'.$diskConfig['bucket'].'/'.$prefixedPath;
            }

            return Storage::disk($disk)->url($path);
        }

        return request()->getSchemeAndHttpHost().'/storage/'.ltrim($path, '/');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user?->role === 'developer') {
            return $query->where('user_id', $user->id);
        }

        if ($user?->role === 'lead') {
            return $query->whereHas('project.leads', fn (Builder $leadQuery) => $leadQuery->whereKey($user->id));
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDailyReports::route('/'),
            'create' => Pages\CreateDailyReport::route('/create'),
            'edit' => Pages\EditDailyReport::route('/{record}/edit'),
        ];
    }
}
