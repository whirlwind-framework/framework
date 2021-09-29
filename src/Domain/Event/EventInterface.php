<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Event;

interface EventInterface
{
    public function occurredOn(): \DateTimeImmutable;
}
