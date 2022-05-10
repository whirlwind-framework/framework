<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Http\Response\Serializer\Json;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Whirlwind\Domain\Collection\CollectionInterface;

class CollectionResource extends JsonResource
{
    protected $container;

    protected string $modelDecorator;

    protected string $collectionEnvelope;

    protected $result;

    public function __construct(
        ContainerInterface $container,
        string $modelDecorator,
        string $collectionEnvelope = 'items'
    ) {
        if (!\is_a($modelDecorator, JsonResource::class, true)) {
            throw new \InvalidArgumentException("Decorator $modelDecorator is not of JsonResource type");
        }
        $this->container = $container;
        $this->modelDecorator = $modelDecorator;
        $this->collectionEnvelope = $collectionEnvelope;
        $this->result = [$this->collectionEnvelope => []];
    }

    public function decorate(ResponseInterface $response, object $decorated): ResponseInterface
    {
        if (!($decorated instanceof CollectionInterface)) {
            throw new \InvalidArgumentException('Decorated object must implement CollectionInterface');
        }
        foreach ($this->decorated as $model) {
            /** @var JsonResource $decorator */
            $decorator = $this->container->get($this->modelDecorator);
            $response = $decorator->decorate($response, $model);
            $this->result[$this->collectionEnvelope][] = $decorator;
        }
        return $response;
    }

    public function jsonSerialize()
    {
        return $this->result;
    }
}
