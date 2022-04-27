<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Http\Response\Serializer\Json;

use Whirlwind\Domain\Collection\CollectionInterface;
use Whirlwind\Infrastructure\Hydrator\Hydrator;

class CollectionResource extends JsonResource
{
    protected $serializer;

    protected string $collectionEnvelope;

    public function __construct(Hydrator $extractor, JsonSerializer $serializer, string $collectionEnvelope = 'items')
    {
        $this->serializer = $serializer;
        $this->collectionEnvelope = $collectionEnvelope;
        parent::__construct($extractor);
    }

    public function decorate(object $decorated): void
    {
        if (!($decorated instanceof CollectionInterface)) {
            throw new \InvalidArgumentException('Decorated object must implement CollectionInterface');
        }
        parent::decorate($decorated);
    }

    public function jsonSerialize()
    {
        $result[$this->collectionEnvelope] = [];
        if (!($this->decorated instanceof CollectionInterface)) {
            return $result;
        }
        foreach ($this->decorated as $model) {
            $result[$this->collectionEnvelope][] = $this->serializer->decorate($model);
        }
        return $result;
    }
}
