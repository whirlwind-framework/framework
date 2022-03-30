<?php

declare(strict_types=1);

namespace Whirlwind\App\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Whirlwind\Infrastructure\Http\Exception\HttpException;

class ExceptionLoggerMiddleware implements MiddlewareInterface
{
    protected LoggerInterface $logger;

    protected bool $enabled;

    public function __construct(LoggerInterface $logger, bool $enabled = true)
    {
        $this->logger = $logger;
        $this->enabled = $enabled;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (\Throwable $e) {
            $context = [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'class' => \get_class($e),
                'stackTrace' => $e->getTraceAsString()
            ];
            if ($e instanceof HttpException) {
                $context['status'] = $e->getStatusCode();
            }
            $this->logger->error($e->getMessage(), $context);
            throw $e;
        }
    }
}
