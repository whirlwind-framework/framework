<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Dto;

interface ArrayableInterface
{
    /**
     * @return array
     */
    public function toArray(): array;
}
