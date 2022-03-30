<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Http\Exception;

class ExpectationFailedHttpException extends HttpException
{
    public function __construct(string $message = 'Expectation failed', $code = 0, \Exception $previous = null)
    {
        parent::__construct(417, $message, $code, $previous);
    }
}
