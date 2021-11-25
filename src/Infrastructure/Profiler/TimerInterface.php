<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Profiler;

interface TimerInterface
{
    public function getName(): string;

    public function getStart(): float;

    public function stop(): void;

    public function getTime(): float;

    public function getTags(): array;

    public function addTag(string $name, $value): void;
}
