<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Http\Exception;

class MethodFailureHttpException extends HttpException
{
    public function __construct(string $message = 'Method failure', $code = 0, \Exception $previous = null)
    {
        parent::__construct(424, $message, $code, $previous);
    }
}
