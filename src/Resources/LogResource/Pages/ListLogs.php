<?php

namespace Munch\FilamentLogviewer\Resources\LogResource\Pages;

use Filament\Resources\Pages\ListRecords;
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
        return $service->getLogFiles();
    }
}
