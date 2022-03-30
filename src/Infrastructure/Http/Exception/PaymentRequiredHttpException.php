<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Http\Exception;

class PaymentRequiredHttpException extends HttpException
{
    public function __construct(string $message = 'Payment Required', $code = 0, \Exception $previous = null)
    {
        parent::__construct(402, $message, $code, $previous);
    }
}
