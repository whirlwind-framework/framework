<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Dto;

use Whirlwind\Domain\Collection\Collection;

class DtoCollection extends Collection implements ArrayableInterface
{
    /**
     * DtoCollection constructor.
     * @param array $items
     * @param string $entityClass
     */
    public function __construct(array $items = [], string $entityClass = DtoInterface::class)
    {
        if ($entityClass !== DtoInterface::class && !\is_subclass_of($entityClass, DtoInterface::class)) {
            throw new \InvalidArgumentException('Class must implement DtoInterface');
        }

        parent::__construct($entityClass, $items);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return \array_map(static function (ArrayableInterface $dto) {
            return $dto->toArray();
        }, $this->items);
    }
}
