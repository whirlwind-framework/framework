<?php declare(strict_types=1);

namespace Whirlwind\Infrastructure\DataProvider;

use Whirlwind\Domain\DataProvider\DataProviderFactoryInterface;
use Whirlwind\Domain\DataProvider\DataProviderInterface;
use Whirlwind\Domain\Repository\RepositoryInterface;

class DataProviderFactory implements DataProviderFactoryInterface
{
    public function create(
        RepositoryInterface $repository,
        array $conditions = [],
        array $sortFields = [],
        int $limit = self::DEFAULT_LIMIT,
        int $page = 0
    ): DataProviderInterface {
        return new DataProvider($repository, $conditions, $sortFields, $limit, $page);
    }
}
