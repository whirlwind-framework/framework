<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Http\Response\Serializer\Json;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Whirlwind\Infrastructure\Http\Response\Serializer\SerializerInterface;

class JsonSerializer implements SerializerInterface
{
    protected ContainerInterface $container;

    protected array $decorators = [];

    public function __construct(ContainerInterface $container, array $decorators = [])
    {
        $this->container = $container;
        $this->decorators = \array_merge($this->decorators, $decorators);
        foreach ($this->decorators as $decorator) {
            if (!($decorator instanceof JsonResource)) {
                throw new \InvalidArgumentException("Decorator $decorator is not of JsonResource type");
            }
        }
    }

    public function serialize(ServerRequestInterface $request, ResponseInterface $response, $data): ResponseInterface
    {
        if (\is_object($data) and isset($this->decorators[\get_class($data)])) {
            /** @var JsonResource $decorator */
            $decorator = $this->container->get($this->decorators[\get_class($data)]);
            $response = $decorator->decorate($response, $data);
            $data = $decorator;
        }
        $data = \json_encode($data);
        $response->getBody()->write($data);
        return $response;
    }
}
