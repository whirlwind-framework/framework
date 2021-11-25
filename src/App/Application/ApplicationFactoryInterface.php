<?php

declare(strict_types=1);

namespace Whirlwind\App\Application;

use Psr\Container\ContainerInterface;

interface ApplicationFactoryInterface
{
    public static function create(ContainerInterface $container): Application;
}
