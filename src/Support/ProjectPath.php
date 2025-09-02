<?php

declare(strict_types=1);

namespace Bladecn\Support;

use Composer\Autoload\ClassLoader;

final readonly class ProjectPath
{
    public static function get(): string
    {
        return dirname(array_keys(ClassLoader::getRegisteredLoaders())[0]);
    }
}
