<?php

declare(strict_types=1);

namespace Test\Unit\Infrastructure\Profiler\NullProfiler;

use PHPUnit\Framework\TestCase;
use Whirlwind\Infrastructure\Profiler\NullProfiler\Timer;
use Whirlwind\Infrastructure\Profiler\NullProfiler\Profiler;

class ProfilerTest extends TestCase
{
    protected $profiler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->profiler = new Profiler();
    }

    public function testTimer()
    {
        $tags = ['tag1' => '1', 'tag2' => '2'];
        $timerName = 'myTimer';
        $timer = $this->profiler->startTimer('myTimer', $tags);
        $this->assertEquals($timerName, $timer->getName());
        $timer->addTag('tag3', '3');
        $this->assertEquals(['tag1' => '1', 'tag2' => '2', 'tag3' => '3'], $timer->getTags());
        $this->profiler->stopTimerByName($timerName);
        $this->assertGreaterThan(0, $timer->getTime());
    }
}
