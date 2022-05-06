<?php

declare(strict_types=1);

namespace Test\Unit\Infrastructure\Http\Response\Serializer\Json\Stub;

use Whirlwind\Infrastructure\Http\Response\Serializer\Json\JsonResource;

class UserResourceStub extends JsonResource
{
    protected array $exclude = ['password'];
}
