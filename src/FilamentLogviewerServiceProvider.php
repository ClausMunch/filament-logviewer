<?php

namespace Munch\FilamentLogviewer;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Munch\FilamentLogviewer\Resources\LogResource;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentLogviewerServiceProvider extends PackageServiceProvider implements Plugin
{
    public static string $name = 'filament-logviewer';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasConfigFile()
            ->hasViews();
    }

    public function packageBooted(): void
    {
        // Register custom CSS if needed
        FilamentAsset::register([
            Css::make('filament-logviewer-styles', __DIR__ . '/../resources/dist/filament-logviewer.css'),
        ], 'munch/filament-logviewer');
    }

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
