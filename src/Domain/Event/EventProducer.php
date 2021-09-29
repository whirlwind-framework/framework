<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Event;

abstract class EventProducer
{
    protected EventDispatcherInterface $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function produce(object $subject): void
    {
        $event = $this->createEvent($subject);
        $this->dispatcher->dispatch($event);
    }

    abstract protected function createEvent(object $subject): EventInterface;
}
