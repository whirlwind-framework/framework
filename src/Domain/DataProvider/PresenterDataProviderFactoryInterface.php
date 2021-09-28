<?php declare(strict_types=1);

namespace Whirlwind\Domain\DataProvider;

use Whirlwind\Domain\Presenter\PresenterInterface;
use Whirlwind\Domain\Repository\RepositoryInterface;

interface PresenterDataProviderFactoryInterface
{
    public const DEFAULT_LIMIT = 20;

    public function create(
        PresenterInterface $presenter,
        RepositoryInterface $repository,
        array $conditions = [],
        array $sortFields = [],
        int $limit = self::DEFAULT_LIMIT,
        int $page = 0
    ): DataProviderInterface;
}
