<?php declare(strict_types=1);

namespace Whirlwind\Infrastructure\Repository\Rest\Exception;

use Throwable;

class ClientException extends \RuntimeException
{
    protected $httpCode;

    /**
     * ClientException constructor.
     * @param int $httpCode
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(int $httpCode, $message = "", $code = 0, Throwable $previous = null)
    {
        $this->httpCode = $httpCode;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return int
     */
    public function getHttpCode(): int
    {
        return $this->httpCode;
    }
}
