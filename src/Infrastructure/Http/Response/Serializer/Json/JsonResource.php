<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Http\Response\Serializer\Json;

use Psr\Http\Message\ResponseInterface;
use Whirlwind\Infrastructure\Hydrator\Hydrator;

class JsonResource implements \JsonSerializable
{
    protected object $decorated;

    protected Hydrator $extractor;

    protected array $exclude = [];

    public function __construct(Hydrator $extractor)
    {
        $this->extractor = $extractor;
    }

    public function decorate(ResponseInterface $response, object $decorated): ResponseInterface
    {
        $this->decorated = $decorated;
        return $response;
    }

    protected function serialize(array $data): array
    {
        foreach ($this->exclude as $key) {
            unset($data[$key]);
        }
        return $data;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->serialize($this->extractor->extract($this->decorated));
    }
}
