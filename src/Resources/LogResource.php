<?php

namespace Munch\FilamentLogviewer\Resources;

use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Munch\FilamentLogviewer\Resources\LogResource\Pages;
use Munch\FilamentLogviewer\Services\LogFileService;

class LogResource extends Resource
{
    protected static ?string $model = null;

    protected static ?string $modelLabel = 'Log File';

    protected static ?string $pluralModelLabel = 'Logs';

    protected static ?string $navigationLabel = 'Logs';

    public static function getModel(): string
    {
        return static::$model ?? 'Munch\FilamentLogviewer\Models\LogFile';
    }

    public static function getNavigationGroup(): ?string
    {
        return config('filament-logviewer.navigation.group', 'Settings');
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-logviewer.navigation.sort', 100);
    }

    public static function getNavigationIcon(): string
    {
        return config('filament-logviewer.navigation.icon', 'heroicon-o-document-text');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('File Name')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-document')
                    ->iconColor('primary'),

                Tables\Columns\TextColumn::make('size')
                    ->label('Size')
                    ->formatStateUsing(fn($state) => LogFileService::formatFileSize($state))
                    ->sortable(),

                Tables\Columns\TextColumn::make('modified')
                    ->label('Last Modified')
                    ->dateTime(config('filament-logviewer.date_format', 'Y-m-d H:i:s'))
                    ->sortable(),
            ])
            ->actions([
                \Filament\Actions\Action::make('view')
                    ->label('View')
                    ->icon('heroicon-m-eye')
                    ->record(fn($record) => $record)
                    ->color('primary')
                    ->url(fn($record) => static::getUrl('view', ['filename' => base64_encode($record['name'])])),

                \Filament\Actions\Action::make('empty')
                    ->label('Empty')
                    ->icon('heroicon-m-trash')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Empty Log File')
                    ->modalDescription(
                        'Are you sure you want to empty this log file? The file will remain but all content will be cleared.',
                    )
                    ->action(function ($record) {
                        $service = new LogFileService();
                        $service->emptyLogFile($record['name']);
                    })
                    ->successNotification(
                        fn() => \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('Log file emptied')
                            ->body('The log file has been successfully emptied.'),
                    ),

                \Filament\Actions\Action::make('delete')
                    ->label('Delete')
                    ->icon('heroicon-m-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Delete Log File')
                    ->modalDescription('Are you sure you want to delete this log file? This action cannot be undone.')
                    ->action(function ($record) {
                        $service = new LogFileService();
                        $service->deleteLogFile($record['name']);
                    })
                    ->successNotification(
                        fn() => \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('Log file deleted')
                            ->body('The log file has been successfully deleted.'),
                    ),
            ])
            ->bulkActions([])
            ->emptyStateHeading('No log files found')
            ->emptyStateDescription('There are no log files in the storage/logs directory.')
            ->emptyStateIcon('heroicon-o-document-text');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLogs::route('/'),
            'view' => Pages\ViewLog::route('/{filename}'),
        ];
    }

    // Override to work with array data instead of Eloquent models
    public static function can(string $action, $record = null): bool
    {
        return true;
    }
}
