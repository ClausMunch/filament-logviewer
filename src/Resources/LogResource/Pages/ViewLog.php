<?php

namespace Munch\FilamentLogviewer\Resources\LogResource\Pages;

use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Munch\FilamentLogviewer\Resources\LogResource;
use Munch\FilamentLogviewer\Services\LogFileService;

class ViewLog extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = LogResource::class;

    protected string $view = 'filament-logviewer::pages.view-log';

    public string $filename;

    public array $fileInfo = [];

    public function mount(string $filename): void
    {
        $this->filename = $filename;

        $logPath = config('filament-logviewer.path', storage_path('logs'));
        $filePath = $logPath . '/' . $filename;

        if (File::exists($filePath)) {
            $this->fileInfo = [
                'name' => $filename,
                'size' => LogFileService::formatFileSize(File::size($filePath)),
                'modified' => \Carbon\Carbon::createFromTimestamp(File::lastModified($filePath))->format(config(
                    'filament-logviewer.date_format',
                    'Y-m-d H:i:s',
                )),
            ];
        }
    }

    public function table(Table $table): Table
    {
        $service = new LogFileService();
        $entries = $service->parseLogFile($this->filename);

        return $table
            ->query(
                // Fake query - we'll override getTableRecords
                \Illuminate\Database\Eloquent\Builder::getQuery(),
            )
            ->columns([
                TextColumn::make('timestamp')
                    ->label('Timestamp')
                    ->dateTime(config('filament-logviewer.date_format', 'Y-m-d H:i:s'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('level')
                    ->label('Level')
                    ->badge()
                    ->color(fn(string $state): string => LogFileService::getLevelColor($state))
                    ->formatStateUsing(fn(string $state): string => LogFileService::getLevelLabel($state))
                    ->sortable(),

                TextColumn::make('environment')
                    ->label('Env')
                    ->badge()
                    ->color('gray')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('message')
                    ->label('Message')
                    ->searchable()
                    ->limit(100)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 100 ? $state : null;
                    })
                    ->wrap(),
            ])
            ->filters([
                SelectFilter::make('level')
                    ->label('Log Level')
                    ->options([
                        'emergency' => 'Emergency',
                        'alert' => 'Alert',
                        'critical' => 'Critical',
                        'error' => 'Error',
                        'warning' => 'Warning',
                        'notice' => 'Notice',
                        'info' => 'Info',
                        'debug' => 'Debug',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        // This is handled in getTableRecords
                        return $query;
                    }),

                Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DateTimePicker::make('from')->label('From'),
                        \Filament\Forms\Components\DateTimePicker::make('until')->label('Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        // This is handled in getTableRecords
                        return $query;
                    }),
            ])
            ->defaultSort('timestamp', 'desc')
            ->paginated([10, 25, 50, 100])
            ->defaultPaginationPageOption(config('filament-logviewer.per_page', 50))
            ->poll('30s')
            ->striped()
            ->emptyStateHeading('No log entries found')
            ->emptyStateDescription('This log file is empty or contains no parseable entries.');
    }

    public function getTableRecords(): Collection
    {
        $service = new LogFileService();
        $entries = $service->parseLogFile($this->filename);

        // Apply filters
        $filters = $this->getTableFilters();

        if (!empty($filters['level']['value'])) {
            $entries = $entries->filter(function ($entry) use ($filters) {
                return $entry['level'] === $filters['level']['value'];
            });
        }

        if (!empty($filters['created_at']['from'])) {
            $from = \Carbon\Carbon::parse($filters['created_at']['from']);
            $entries = $entries->filter(function ($entry) use ($from) {
                return $entry['timestamp']->gte($from);
            });
        }

        if (!empty($filters['created_at']['until'])) {
            $until = \Carbon\Carbon::parse($filters['created_at']['until']);
            $entries = $entries->filter(function ($entry) use ($until) {
                return $entry['timestamp']->lte($until);
            });
        }

        // Apply search
        $search = $this->getTableSearch();
        if ($search) {
            $entries = $entries->filter(function ($entry) use ($search) {
                return (
                    str_contains(strtolower($entry['message']), strtolower($search))
                    || str_contains(strtolower($entry['context']), strtolower($search))
                );
            });
        }

        return $entries->values();
    }

    public function getTitle(): string
    {
        return 'View Log: ' . $this->filename;
    }
}
