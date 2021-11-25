<?php

declare(strict_types=1);

namespace Whirlwind\App\Http\Adapter;

use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\ServerRequestFactory;
use Whirlwind\App\Http\ServerRequestFactoryInterface;

class LaminasServerRequestFactoryAdapter extends ServerRequestFactory implements ServerRequestFactoryInterface
{
    public static function fromGlobals(
        array $server = null,
        array $query = null,
        array $body = null,
        array $cookies = null,
        array $files = null
    ): ServerRequest {
        return parent::fromGlobals($server, $query, $body, $cookies, $files);
    }
}
