<?php

declare(strict_types=1);

namespace Bladecn;

use Bladecn\Commands\AddBladeRegistryCommand;
use Bladecn\Commands\ValidateBladeRegistryComfmand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class BladecnServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('bladecn-cli')
            ->hasConfigFile()
            ->hasCommand(AddBladeRegistryCommand::class)
            ->hasCommand(ValidateBladeRegistryComfmand::class);
    }
}
