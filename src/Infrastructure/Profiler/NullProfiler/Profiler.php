<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Profiler\NullProfiler;

use Whirlwind\Infrastructure\Profiler\AbstractProfiler;
use Whirlwind\Infrastructure\Profiler\TimerInterface;

class Profiler extends AbstractProfiler
{
    public function flush(): void
    {
        $this->timers = [];
    }

    public function startTimer(string $timerName, array $tags = []): TimerInterface
    {
        $timer = new Timer($timerName, $this->prepareTags($tags));
        $this->timers[$timerName] = $timer;
        return $timer;
    }
}
