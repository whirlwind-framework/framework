<?php

declare(strict_types=1);

namespace Whirlwind\App\Application\Adapter;

use Laminas\Diactoros\ResponseFactory;
use League\Container\ReflectionContainer;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;
use League\Route\Strategy\StrategyInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Whirlwind\App\Application\ApplicationServiceProviderInterface;
use Whirlwind\App\Http\Adapter\LaminasServerRequestFactoryAdapter;
use Whirlwind\App\Http\ServerRequestFactoryInterface;
use Whirlwind\App\Emitter\EmitterInterface;
use Whirlwind\App\Emitter\Adapter\LaminasSapiEmitterAdapter;
use Whirlwind\App\Router\Adapter\LeagueRouterAdapter;
use Whirlwind\App\Router\RouterInterface;

class LeagueApplicationServiceProviderAdapter extends AbstractServiceProvider implements
    BootableServiceProviderInterface,
    ApplicationServiceProviderInterface
{
    protected $provides = [
        ResponseFactoryInterface::class,
        ServerRequestFactoryInterface::class,
        RouterInterface::class,
        EmitterInterface::class,
    ];

    public function boot(): void
    {
        $this->getContainer()->delegate(
            (new ReflectionContainer(false))
        );
    }

    public function register(): void
    {
        $container = $this->getContainer();
        $container->add(
            ServerRequestFactoryInterface::class,
            LaminasServerRequestFactoryAdapter::class
        )->setShared();

        $container->add(
            ResponseFactoryInterface::class,
            ResponseFactory::class
        )->setShared();

        $container->add(
            RouterInterface::class,
            function () use ($container) {
                $strategy = $container->get(StrategyInterface::class)->setContainer($container);
                return (new LeagueRouterAdapter())->setStrategy($strategy);
            }
        )->setShared();

        $container->add(
            EmitterInterface::class,
            LaminasSapiEmitterAdapter::class
        )->setShared();
    }

    public function provides(string $id): bool
    {
        return in_array($id, $this->provides, true);
    }
}
