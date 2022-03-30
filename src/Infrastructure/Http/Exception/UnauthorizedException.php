<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Http\Exception;

class UnauthorizedException extends HttpException
{
    public function __construct(string $message = 'Unauthorized', $code = 0, \Exception $previous = null)
    {
        parent::__construct(401, $message, $code, $previous);
    }
}
