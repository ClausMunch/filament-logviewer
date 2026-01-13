<?php

namespace Munch\FilamentLogviewer\Resources\LogResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Munch\FilamentLogviewer\Resources\LogResource;
use Munch\FilamentLogviewer\Services\LogFileService;

class ListLogs extends ListRecords
{
    protected static string $resource = LogResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getTableRecords(): array
    {
        $service = new LogFileService();
        return $service->getLogFiles()->toArray();
    }
}
