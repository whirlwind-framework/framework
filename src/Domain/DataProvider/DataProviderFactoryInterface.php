<?php

declare(strict_types=1);

namespace Whirlwind\Domain\DataProvider;

use Whirlwind\Domain\Repository\RepositoryInterface;

interface DataProviderFactoryInterface
{
    public const DEFAULT_LIMIT = 20;

    public function create(
        RepositoryInterface $repository,
        array $conditions = [],
        array $sortFields = [],
        int $limit = self::DEFAULT_LIMIT,
        int $page = 0
    ): DataProviderInterface;
}
