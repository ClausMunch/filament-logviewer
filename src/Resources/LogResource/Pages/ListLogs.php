<?php

namespace Munch\FilamentLogviewer\Resources\LogResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Munch\FilamentLogviewer\Resources\LogResource;
use Munch\FilamentLogviewer\Services\LogFileService;
use Illuminate\Support\Collection;

class ListLogs extends ListRecords
{
    protected static string $resource = LogResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTableRecords(): Collection
    {
        $service = new LogFileService();
        return $service->getLogFiles()->map(function ($record) {
            $record['__key'] = $record['name'];
            return $record;
        });
    }

    public function getTableRecordKey($record): string
    {
        return $record['name'];
    }

    public function table(Table $table): Table
    {
        return static::$resource::table($table)
            ->recordAction(null)
            ->recordUrl(null);
    }

    public function resolveTableRecord($key): ?array
    {
        $records = $this->getTableRecords();
        return $records->firstWhere('__key', $key);
    }
}
