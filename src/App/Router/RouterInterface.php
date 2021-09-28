<?php declare(strict_types=1);

namespace Whirlwind\App\Router;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

interface RouterInterface extends RequestHandlerInterface
{
    public function dispatch(ServerRequestInterface $request): ResponseInterface;

    public function map(string $method, string $path, $handler): MiddlewareInterface;

    public function middleware(MiddlewareInterface $middleware);

    public function lazyMiddleware(string $middleware);
}
