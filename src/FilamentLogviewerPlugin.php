<?php

namespace Munch\FilamentLogviewer;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Munch\FilamentLogviewer\Resources\LogResource;

class FilamentLogviewerPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-logviewer';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            LogResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        return filament(app(static::class)->getId());
    }
}
