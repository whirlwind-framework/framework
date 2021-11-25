<?php

declare(strict_types=1);

namespace Whirlwind\Domain\DataProvider;

interface DataProviderInterface
{
    public function getModels(): array;

    public function getPagination(): PaginationInterface;
}
