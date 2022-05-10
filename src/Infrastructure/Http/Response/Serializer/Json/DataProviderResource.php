<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Http\Response\Serializer\Json;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Whirlwind\Domain\DataProvider\DataProviderInterface;

class DataProviderResource extends JsonResource
{
    protected ?string $modelDecorator;

    protected string $collectionEnvelope;

    protected ContainerInterface $container;

    protected $result;

    public function __construct(
        ContainerInterface $container,
        ?string $modelDecorator,
        string $collectionEnvelope = 'items'
    ) {
        if (null !== $modelDecorator and !\is_a($modelDecorator, JsonResource::class, true)) {
            throw new \InvalidArgumentException("Decorator $modelDecorator is not of JsonResource type");
        }
        $this->container = $container;
        $this->modelDecorator = $modelDecorator;
        $this->collectionEnvelope = $collectionEnvelope;
        $this->result = [$this->collectionEnvelope => []];
    }

    public function decorate(ResponseInterface $response, object $decorated): ResponseInterface
    {
        if (!($decorated instanceof DataProviderInterface)) {
            throw new \InvalidArgumentException('Decorated object must implement DataProviderInterface');
        }
        $response = $response
            ->withAddedHeader('X-Pagination-Total-Count', $decorated->getPagination()->getTotal())
            ->withAddedHeader('X-Pagination-Page-Count', $decorated->getPagination()->getNumberOfPages())
            ->withAddedHeader('X-Pagination-Current-Page', $decorated->getPagination()->getPage())
            ->withAddedHeader('X-Pagination-Per-Page', $decorated->getPagination()->getPageSize());
        foreach ($decorated->getModels() as $model) {
            $item = $model;
            if (null !== $this->modelDecorator) {
                /** @var JsonResource $item */
                $item = $this->container->get($this->modelDecorator);
                $response = $item->decorate($response, $model);
            }
            $this->result[$this->collectionEnvelope][] = $item;
        }
        return $response;
    }

    public function jsonSerialize()
    {
        return $this->result;
    }
}
