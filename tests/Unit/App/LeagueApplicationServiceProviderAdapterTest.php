<?php

declare(strict_types=1);

namespace Test\Unit\App;

use DG\BypassFinals;
use Laminas\Diactoros\ResponseFactory;
use League\Container\Container;
use League\Route\Strategy\ApplicationStrategy;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Whirlwind\App\Application\Adapter\LeagueApplicationFactoryAdapter;
use Whirlwind\App\Application\Adapter\LeagueApplicationServiceProviderAdapter;
use Whirlwind\App\Application\Application;
use Whirlwind\App\Emitter\Adapter\LaminasSapiEmitterAdapter;
use Whirlwind\App\Emitter\EmitterInterface;
use Whirlwind\App\Http\Adapter\LaminasServerRequestFactoryAdapter;
use Whirlwind\App\Http\ServerRequestFactoryInterface;
use Whirlwind\App\Router\Adapter\LeagueRouterAdapter;
use Whirlwind\App\Router\RouterInterface;

class LeagueApplicationServiceProviderAdapterTest extends TestCase
{
    private $serviceProvider;

    protected function setUp(): void
    {
        BypassFinals::enable();
        parent::setUp();

        $this->serviceProvider = (new LeagueApplicationServiceProviderAdapter())->setContainer(new Container());
        $this->serviceProvider->register();
    }

    public function testProvidesResponseFactoryInterface(): void
    {
        $this->assertTrue($this->serviceProvider->provides(ResponseFactoryInterface::class));
        $this->assertInstanceOf(
            ResponseFactory::class,
            $this->serviceProvider->getContainer()->get(ResponseFactoryInterface::class)
        );
    }

    public function testProvidesServerRequestFactoryInterface(): void
    {
        $this->assertTrue($this->serviceProvider->provides(ServerRequestFactoryInterface::class));
        $this->assertInstanceOf(
            LaminasServerRequestFactoryAdapter::class,
            $this->serviceProvider->getContainer()->get(ServerRequestFactoryInterface::class)
        );
    }

    public function testProvidesRouterInterface(): void
    {
        $this->assertTrue($this->serviceProvider->provides(RouterInterface::class));
        $this->assertInstanceOf(
            LeagueRouterAdapter::class,
            $this->serviceProvider->getContainer()->get(RouterInterface::class)
        );

        $this->assertInstanceOf(
            ApplicationStrategy::class,
            $this->serviceProvider->getContainer()->get(RouterInterface::class)->getStrategy()
        );
    }

    public function testEmitterInterface(): void
    {
        $this->assertTrue($this->serviceProvider->provides(EmitterInterface::class));
        $this->assertInstanceOf(
            LaminasSapiEmitterAdapter::class,
            $this->serviceProvider->getContainer()->get(EmitterInterface::class)
        );
    }

}
