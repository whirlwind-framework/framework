<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Dto;

class DtoCollectionFactory
{
    /**
     * @param array $items
     * @return DtoCollection
     */
    public function create(array $items = []): DtoCollection
    {
        return new DtoCollection($items);
    }
}
