<?php

declare(strict_types=1);

namespace Whirlwind\App\Http\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Whirlwind\Infrastructure\Http\Exception\HttpException;
use Whirlwind\Infrastructure\Http\Exception\UnprocessableEntityHttpException;
use Whirlwind\Infrastructure\Http\Response\Serializer\SerializerInterface;

class HttpExceptionMiddleware implements MiddlewareInterface
{
    protected bool $showDebug;

    protected ResponseFactoryInterface $responseFactory;

    protected SerializerInterface $serializer;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        SerializerInterface $serializer,
        bool $showDebug = false
    ) {
        $this->responseFactory = $responseFactory;
        $this->serializer = $serializer;
        $this->showDebug = $showDebug;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (HttpException $e) {
            $response = $this->responseFactory->createResponse();
            $data = [
                'message' => $e->getMessage(),
                'status' => $e->getStatusCode(),
                'code' => $e->getCode()
            ];
            if ($e instanceof UnprocessableEntityHttpException) {
                $data['errors'] = $e->getErrorCollection();
            }
            if ($this->showDebug) {
                $data['stackTrace'] = $e->getTraceAsString();
            }
            $response
                ->withStatus($e->getStatusCode())
                ->getBody()
                ->write($this->serializer->serialize($data));
            return $response;
        } catch (\Throwable $e) {
            $response = $this->responseFactory->createResponse();
            $data = [
                'message' => $e->getMessage(),
                'status' => 500,
                'code' => $e->getCode()
            ];
            if ($this->showDebug) {
                $data['stackTrace'] = $e->getTraceAsString();
            }
            $response
                ->withStatus(500)
                ->getBody()
                ->write($this->serializer->serialize($data));
            return $response;
        }
    }
}
