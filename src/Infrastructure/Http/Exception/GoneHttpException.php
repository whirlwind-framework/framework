<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Http\Exception;

class GoneHttpException extends HttpException
{
    public function __construct(string $message = 'Gone', $code = 0, \Exception $previous = null)
    {
        parent::__construct(410, $message, $code, $previous);
    }
}
