<?php

declare(strict_types=1);

namespace Bladecn\Tests;

use Bladecn\BladecnServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\Attributes\WithMigration;
use Orchestra\Testbench\TestCase as Orchestra;

#[WithMigration('cache')]
class TestCase extends Orchestra
{
    use RefreshDatabase;

    /**
     * @param  Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            BladecnServiceProvider::class,
        ];
    }
}
