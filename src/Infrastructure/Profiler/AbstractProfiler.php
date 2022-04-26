<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Profiler;

abstract class AbstractProfiler implements ProfilerInterface
{
    protected $defaultTags;

    protected $timers = [];

    public function __construct(array $defaultTags = [])
    {
        $this->defaultTags = $defaultTags;
    }

    protected function prepareTags(array $tags = []): array
    {
        return \array_merge($this->defaultTags, $tags);
    }

    public function stopTimer(TimerInterface $timer): void
    {
        $timer->stop();
    }

    public function stopTimerByName(string $timerName): void
    {
        if (isset($this->timers[$timerName])) {
            $this->stopTimer($this->timers[$timerName]);
        }
    }
}
