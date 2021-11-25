<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Profiler;

abstract class AbstractProfiler implements ProfilerInterface
{
    protected $defaultTags;

    public function __construct(array $defaultTags = [])
    {
        $this->defaultTags = $defaultTags;
    }

    protected function prepareTags(array $tags = []): array
    {
        return \array_merge($this->defaultTags, $tags);
    }
}
