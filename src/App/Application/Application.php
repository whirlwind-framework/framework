<?php

declare(strict_types=1);

namespace Whirlwind\App\Application;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Whirlwind\App\Emitter\EmitterInterface;
use Whirlwind\App\Http\ServerRequestFactoryInterface;
use Whirlwind\App\Router\RouterInterface;

class Application implements RequestHandlerInterface
{
    protected ContainerInterface $container;

    protected RouterInterface $router;

    public function __construct(
        ContainerInterface $container
    ) {
        $this->container = $container;
        $this->router = $this->container->get(RouterInterface::class);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->router->dispatch($request);
    }

    public function run(?ServerRequestInterface $request = null): void
    {
        if (!$request) {
            $factory = $this->container->get(ServerRequestFactoryInterface::class);
            $request = $factory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
        }
        $response = $this->handle($request);
        $emitter = $this->container->get(EmitterInterface::class);
        $emitter->emit($response);
    }

    public function map(string $method, string $path, $handler): MiddlewareInterface
    {
        return $this->router->map($method, $path, $handler);
    }

    public function addMidleware($middleware)
    {
        if ($middleware instanceof MiddlewareInterface) {
            return $this->router->middleware($middleware);
        }
        return $this->router->lazyMiddleware($middleware);
    }
}
