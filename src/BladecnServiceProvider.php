<?php

declare(strict_types=1);

namespace Bladecn;

use Bladecn\Commands\AddBladeRegistryCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class BladecnServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('bladecn-laravel')
            ->hasConfigFile()
            ->hasCommands([
                AddBladeRegistryCommand::class,
            ]);
    }
}
