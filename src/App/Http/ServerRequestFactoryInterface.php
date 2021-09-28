<?php declare(strict_types=1);

namespace Whirlwind\App\Http;

use Psr\Http\Message\ServerRequestInterface;

interface ServerRequestFactoryInterface extends \Psr\Http\Message\ServerRequestFactoryInterface
{
    public static function fromGlobals(
        array $server = null,
        array $query = null,
        array $body = null,
        array $cookies = null,
        array $files = null
    ) : ServerRequestInterface;
}
