<?php declare(strict_types=1);

namespace Whirlwind\App\Emitter;

use Psr\Http\Message\ResponseInterface;

interface EmitterInterface
{
    public function emit(ResponseInterface $response) : bool;
}
