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
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $modelLabel = 'Log File';

    protected static ?string $pluralModelLabel = 'Logs';

    public static function getNavigationGroup(): ?string
    {
        return config('filament-logviewer.navigation.group', 'System');
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-logviewer.navigation.sort', 100);
    }

    public static function getNavigationIcon(): ?string
    {
        return config('filament-logviewer.navigation.icon', 'heroicon-o-document-text');
    }

    public static function table(Table $table): Table
    {
        $service = new LogFileService();
        $logFiles = $service->getLogFiles();

        return $table
            ->query(
                // Use a fake query builder since we're working with files
                \Illuminate\Database\Eloquent\Builder::getQuery()
            )
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
                    ->formatStateUsing(fn ($state) => LogFileService::formatFileSize($state))
                    ->sortable(),

                Tables\Columns\TextColumn::make('modified')
                    ->label('Last Modified')
                    ->dateTime(config('filament-logviewer.date_format', 'Y-m-d H:i:s'))
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('View')
                    ->icon('heroicon-m-eye')
                    ->color('primary')
                    ->url(fn ($record) => static::getUrl('view', ['filename' => $record['name']])),

                Tables\Actions\Action::make('empty')
                    ->label('Empty')
                    ->icon('heroicon-m-trash')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Empty Log File')
                    ->modalDescription('Are you sure you want to empty this log file? The file will remain but all content will be cleared.')
                    ->action(function ($record) use ($service) {
                        $service->emptyLogFile($record['name']);
                    })
                    ->successNotification(
                        fn () => \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('Log file emptied')
                            ->body('The log file has been successfully emptied.')
                    ),

                Tables\Actions\Action::make('delete')
                    ->label('Delete')
                    ->icon('heroicon-m-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Delete Log File')
                    ->modalDescription('Are you sure you want to delete this log file? This action cannot be undone.')
                    ->action(function ($record) use ($service) {
                        $service->deleteLogFile($record['name']);
                    })
                    ->successNotification(
                        fn () => \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('Log file deleted')
                            ->body('The log file has been successfully deleted.')
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('delete')
                    ->label('Delete Selected')
                    ->icon('heroicon-m-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Delete Log Files')
                    ->modalDescription('Are you sure you want to delete the selected log files? This action cannot be undone.')
                    ->action(function ($records) use ($service) {
                        foreach ($records as $record) {
                            $service->deleteLogFile($record['name']);
                        }
                    })
                    ->deselectRecordsAfterCompletion()
                    ->successNotification(
                        fn () => \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('Log files deleted')
                            ->body('The selected log files have been successfully deleted.')
                    ),
            ])
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
