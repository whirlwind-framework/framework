<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Profiler\MongoProfiler;

use Whirlwind\Infrastructure\Profiler\AbstractProfiler;
use Whirlwind\Infrastructure\Profiler\TimerInterface;
use MongoDB\BSON\ObjectId;
use Whirlwind\Infrastructure\Persistence\Mongo\MongoConnection;

class Profiler extends AbstractProfiler
{
    /**
     * @var TimerInterface[]
     */
    protected $timers = [];

    protected $connection;

    protected $collectionName;

    public function __construct(MongoConnection $connection, string $collectionName, array $defaultTags = [])
    {
        \register_shutdown_function([$this, 'flush']);
        $this->connection = $connection;
        $this->collectionName = $collectionName;
        parent::__construct($defaultTags);
    }

    public function flush(): void
    {
        foreach ($this->timers as $timer) {
            /** @var Timer $timer */
            $data = [
                '_id' => new ObjectId(),
                'timer' => $timer->getName(),
                'start' => $timer->getStart(),
                'duration' => $timer->getTime(),
                'tags' => $timer->getTags()
            ];
            $connection = $this->connection;
            try {
                $connection->getCollection($this->collectionName)->insert($data);
            } catch (\Exception $e) {
            }
        }
        $this->timers = [];
    }

    public function startTimer(string $timerName, array $tags = []): TimerInterface
    {
        $timer = new Timer($timerName, $this->prepareTags($tags));
        $this->timers[$timerName] = $timer;
        return $timer;
    }

    public function stopTimer(TimerInterface $timer): void
    {
        /** @var Timer $timer */
        $timer->stop();
    }

    public function stopTimerByName(string $timerName): void
    {
        if (isset($this->timers[$timerName])) {
            $this->stopTimer($this->timers[$timerName]);
        }
    }
}
