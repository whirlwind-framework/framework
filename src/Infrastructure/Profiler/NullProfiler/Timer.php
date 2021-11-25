<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Profiler\NullProfiler;

use Whirlwind\Infrastructure\Profiler\TimerInterface;

class Timer implements TimerInterface
{
    protected $name;

    protected $start;

    protected $time;

    protected $stoped;

    /**
     * @var array
     */
    protected $tags;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStart(): float
    {
        return $this->start;
    }

    public function stop(): void
    {
        if (false === $this->stoped) {
            $this->time = \microtime(true) - $this->start;
            $this->stoped = true;
        }
    }

    public function getTime(): float
    {
        if (false === $this->stoped) {
            $this->stop();
        }
        return $this->time;
    }

    /**
     * @return array
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @param string $name
     * @param $value
     * @return void
     */
    public function addTag(string $name, $value): void
    {
        if ($this->tags !== null && !\array_key_exists($name, $this->tags)) {
            $this->tags[$name] = $value;
        }
    }
}
