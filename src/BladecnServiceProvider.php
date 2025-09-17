<?php

declare(strict_types=1);

namespace Bladecn;

use Bladecn\Commands\AddBladeRegistryCommand;
use Bladecn\Commands\ListBladeRegistryCommand;
use Illuminate\Foundation\Application;
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
                ListBladeRegistryCommand::class,
            ]);
    }

    public function registeringPackage(): void
    {
        $this->app->singleton(RegistryManager::class, function (Application $app) {
            return new RegistryManager(RegistryConfig::make(), $app);
        });
    }
}
