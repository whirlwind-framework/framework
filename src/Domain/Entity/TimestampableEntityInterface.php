<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Entity;

interface TimestampableEntityInterface extends CreatedAtInterface
{
    /**
     * @return \DateTimeImmutable|null
     */
    public function getUpdatedAt(): ?\DateTimeImmutable;
}
