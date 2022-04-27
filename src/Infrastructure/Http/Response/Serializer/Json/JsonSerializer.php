<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Http\Response\Serializer\Json;

use Psr\Container\ContainerInterface;
use Whirlwind\Domain\Collection\CollectionInterface;
use Whirlwind\Domain\DataProvider\DataProviderInterface;
use Whirlwind\Infrastructure\Http\Response\Serializer\SerializerInterface;

class JsonSerializer implements SerializerInterface
{
    protected ContainerInterface $container;

    private array $decorators = [
        DataProviderInterface::class => DataProviderResource::class,
        CollectionResource::class => CollectionResource::class
    ];

    public function __construct(ContainerInterface $container, array $decorators = [])
    {
        $this->container = $container;
        \array_merge($this->decorators, $decorators);
    }

    public function decorate(object $object): object
    {
        if (isset($this->decorators[\get_class($object)])) {
            $decorator = $this->container->get($this->decorators[\get_class($object)]);
            $decorator->decorate($object);
            return $decorator;
        }
        return $object;
    }

    public function serialize($data)
    {
        if (\is_object($data)) {
            $data = $this->decorate($data);
        }
        return \json_encode($data);
    }
}
