<?php

namespace Munch\FilamentLogviewer;

use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentLogviewerServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-logviewer';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)->hasConfigFile()->hasViews();
    }

    public function packageBooted(): void
    {
        // Register custom CSS if needed
        FilamentAsset::register([
            Css::make('filament-logviewer-styles', __DIR__ . '/../resources/dist/filament-logviewer.css'),
        ], 'munch/filament-logviewer');
    }
}
