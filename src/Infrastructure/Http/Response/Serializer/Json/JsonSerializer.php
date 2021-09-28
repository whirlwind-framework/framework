<?php declare(strict_types=1);

namespace Whirlwind\Infrastructure\Http\Response\Serializer\Json;

use Psr\Container\ContainerInterface;
use Whirlwind\Domain\Collection\CollectionInterface;
use Whirlwind\Domain\DataProvider\DataProviderInterface;
use Whirlwind\Infrastructure\Http\Response\Serializer\SerializerInterface;

class JsonSerializer implements SerializerInterface
{
    protected ContainerInterface $container;

    protected array $decorators = [];

    protected string $collectionEnvelope = 'items';

    public function __construct(ContainerInterface $container, array $decorators = [])
    {
        $this->container = $container;
        \array_merge($this->decorators, $decorators);
    }

    protected function decorate(object $object): object
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
        $result = $data;
        if ($data instanceof DataProviderInterface) {
            $result = [];
            foreach ($data->getModels() as $model) {
                $result[$this->collectionEnvelope][] = $this->decorate($model);
            }
        } elseif ($data instanceof CollectionInterface) {
            $result = [];
            foreach ($data as $model) {
                $result[$this->collectionEnvelope][] = $this->decorate($model);
            }
        } elseif (\is_object($data)) {
            $result = $this->decorate($data);
        }
        return \json_encode($result);
    }
}
