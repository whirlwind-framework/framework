<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Http\Response\Serializer;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface SerializerInterface
{
    public function serialize(ServerRequestInterface $request, ResponseInterface $response, $data): ResponseInterface;
}
