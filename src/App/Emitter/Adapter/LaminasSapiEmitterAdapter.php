<?php

declare(strict_types=1);

namespace Whirlwind\App\Emitter\Adapter;

use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Whirlwind\App\Emitter\EmitterInterface;

class LaminasSapiEmitterAdapter extends SapiEmitter implements EmitterInterface
{
}
