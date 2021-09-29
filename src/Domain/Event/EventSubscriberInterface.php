<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Event;

interface EventSubscriberInterface
{
    public function handle(EventInterface $event): void;

    public function isSubscribedTo(EventInterface $event): bool;
}
