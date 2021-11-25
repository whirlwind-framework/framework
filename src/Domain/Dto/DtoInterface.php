<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Dto;

interface DtoInterface extends \JsonSerializable
{
    public function toArray(): array;
}
