<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Http\Exception;

class NotFoundHttpException extends HttpException
{
    public function __construct($message = null, $code = 0, \Exception $previous = null)
    {
        parent::__construct(404, $message, $code, $previous);
    }
}
