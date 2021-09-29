<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Event;

interface EventDispatcherInterface
{
    public function subscribe(EventSubscriberInterface $subscriber): void;

    public function dispatch(EventInterface $event): void;
}
