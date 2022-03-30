<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Http\Exception;

class RequestedRangeUnsatisfiableHttpException extends HttpException
{
    public function __construct(string $message = 'Requested range unsatisfiable', $code = 0, \Exception $previous = null)
    {
        parent::__construct(416, $message, $code, $previous);
    }
}
