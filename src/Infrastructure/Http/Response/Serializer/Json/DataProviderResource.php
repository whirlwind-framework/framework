<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Http\Response\Serializer\Json;

use Whirlwind\Domain\DataProvider\DataProviderInterface;
use Whirlwind\Infrastructure\Hydrator\Hydrator;

class DataProviderResource extends JsonResource
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
        if (!($decorated instanceof DataProviderInterface)) {
            throw new \InvalidArgumentException('Decorated object must implement DataProviderInterface');
        }
        parent::decorate($decorated);
    }

    public function jsonSerialize()
    {
        $result[$this->collectionEnvelope] = [];
        if (!($this->decorated instanceof DataProviderInterface)) {
            return $result;
        }
        foreach ($this->decorated->getModels() as $model) {
            $result[$this->collectionEnvelope][] = $this->serializer->decorate($model);
        }
        return $result;
    }
}
