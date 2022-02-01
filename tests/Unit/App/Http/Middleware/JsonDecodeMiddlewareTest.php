<?php

declare(strict_types=1);

namespace Test\Unit\App\Http\Middleware;

use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\StreamFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Whirlwind\App\Http\Middleware\JsonDecodeMiddleware;

final class JsonDecodeMiddlewareTest extends TestCase
{
    /** @dataProvider requestProvider */
    public function testWithJsonContentType(ServerRequestInterface $request, $expectedBody): void
    {
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler
            ->method('handle')
            ->with($this->callback(function (ServerRequestInterface $request) use ($expectedBody): bool {
                return ($request->getMethod() === 'POST')
                    && ((string) $request->getUri() === '/some/uri')
                    && ($request->getParsedBody() === $expectedBody);
            }))
            ->willReturn($response = $this->createMock(ResponseInterface::class));

        $middleware = new JsonDecodeMiddleware();

        $this->assertSame($response, $middleware->process($request, $handler));
    }

    public function requestProvider(): iterable
    {
        $httpFactory = new ServerRequestFactory();
        $streamFactory = new StreamFactory();

        yield 'application/json Content-Type' => [
            $httpFactory
                ->createServerRequest('POST', '/some/uri')
                ->withBody($streamFactory->createStream('{"some":"json"}'))
                ->withHeader('Content-Type', 'application/json'),
            ['some' => 'json']
        ];

        yield 'unknown Content-Type' => [
            $httpFactory
                ->createServerRequest('POST', '/some/uri')
                ->withBody($streamFactory->createStream('{"some":"json"}'))
                ->withHeader('Content-Type', 'application/xml'),
            null
        ];

        yield 'missing Content-Type' => [
            $httpFactory
                ->createServerRequest('POST', '/some/uri')
                ->withBody($streamFactory->createStream('{"some":"json"}'))
                ->withoutHeader('Content-Type'),
            null
        ];

        yield 'missing Content-Type, parsed body already given' => [
            $httpFactory
                ->createServerRequest('POST', '/some/uri')
                ->withBody($streamFactory->createStream('{"some":"json"}'))
                ->withParsedBody($c = new \stdClass())
                ->withoutHeader('Content-Type'),
            $c
        ];

        yield 'application/json;charset=UTF-8 Content-Type' => [
            $httpFactory
                ->createServerRequest('POST', '/some/uri')
                ->withBody($streamFactory->createStream('{"some":"json"}'))
                ->withHeader('Content-Type', 'application/json;charset=UTF-8'),
            ['some' => 'json']
        ];
    }
}
