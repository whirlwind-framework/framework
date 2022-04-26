<?php

declare(strict_types=1);

namespace Whirlwind\App\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class JsonDecodeMiddleware implements MiddlewareInterface
{
    private const DEFAULT_JSON_DEPTH = 512;

    /** @throws \JsonException */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $handler->handle($request->withParsedBody($this->parseBody($request)));
    }

    /** @throws \JsonException */
    private function parseBody(ServerRequestInterface $request)
    {
        if (!$request->hasHeader('Content-Type')) {
            return $request->getParsedBody();
        }

        if (\strpos($request->getHeaderLine('Content-Type'), 'application/json') === false) {
            return $request->getParsedBody();
        }

        return \json_decode(
            (string)$request->getBody(),
            true,
            self::DEFAULT_JSON_DEPTH,
            JSON_THROW_ON_ERROR
        );
    }
}
