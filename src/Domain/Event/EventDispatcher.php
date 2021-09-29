<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Event;

final class EventDispatcher implements EventDispatcherInterface
{
    private array $subscribers = [];

    public function __construct(array $subscribers = [])
    {
        foreach ($subscribers as $subscriber) {
            $this->subscribe($subscriber);
        }
    }

    public function subscribe(EventSubscriberInterface $subscriber): void
    {
        $this->subscribers[] = $subscriber;
    }

    public function dispatch(EventInterface $event): void
    {
        foreach ($this->subscribers as $subscriber) {
            /** @var EventSubscriberInterface $subscriber */
            if ($subscriber->isSubscribedTo($event)) {
                $subscriber->handle($event);
            }
        }
    }
}
