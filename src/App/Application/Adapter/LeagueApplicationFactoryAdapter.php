<?php declare(strict_types=1);

namespace Whirlwind\App\Application\Adapter;

use League\Container\Container;
use Psr\Container\ContainerInterface;
use Whirlwind\App\Application\Application;
use Whirlwind\App\Application\ApplicationFactoryInterface;

class LeagueApplicationFactoryAdapter implements ApplicationFactoryInterface
{
    public static function create(ContainerInterface $container): Application
    {
        if (!($container instanceof Container)) {
            $c = $container;
            $container = new Container();
            $container->delegate($c);
        }
        $container->addServiceProvider(LeagueApplicationServiceProviderAdapter::class);
        return new Application($container);
    }
}
