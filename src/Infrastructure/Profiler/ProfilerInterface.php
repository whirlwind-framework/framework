<?php declare(strict_types=1);

namespace Whirlwind\Infrastructure\Profiler;

interface ProfilerInterface
{
    public function flush(): void;

    public function startTimer(string $timerName, array $tags = []): TimerInterface;

    public function stopTimer(TimerInterface $timer): void;

    public function stopTimerByName(string $timerName): void;
}
