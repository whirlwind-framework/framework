<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Http\Exception;

class NotAcceptableHttpException extends HttpException
{
    public function __construct(string $message = 'Not Acceptable', $code = 0, \Exception $previous = null)
    {
        parent::__construct(406, $message, $code, $previous);
    }
}
