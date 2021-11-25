<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Entity;

interface CreatedAtInterface
{
    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt(): \DateTimeImmutable;
}
