<?php declare(strict_types=1);

namespace Whirlwind\Infrastructure\Profiler\NullProfiler;

use Whirlwind\Infrastructure\Profiler\AbstractProfiler;
use Whirlwind\Infrastructure\Profiler\TimerInterface;

class Profiler extends AbstractProfiler
{
    public function flush(): void
    {
    }

    public function startTimer(string $timerName, array $tags = []): TimerInterface
    {
        return new Timer($timerName);
    }

    public function stopTimer(TimerInterface $timer): void
    {
    }

    public function stopTimerByName(string $timerName): void
    {
    }
}
