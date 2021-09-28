<?php declare(strict_types=1);

namespace Whirlwind\Infrastructure\DataProvider;

use Whirlwind\Domain\DataProvider\DataProviderInterface;
use Whirlwind\Domain\DataProvider\PresenterDataProviderFactoryInterface;
use Whirlwind\Domain\Presenter\PresenterInterface;
use Whirlwind\Domain\Repository\RepositoryInterface;

class PresenterDataProviderFactory implements PresenterDataProviderFactoryInterface
{
    public function create(
        PresenterInterface $presenter,
        RepositoryInterface $repository,
        array $conditions = [],
        array $sortFields = [],
        int $limit = self::DEFAULT_LIMIT,
        int $page = 0
    ): DataProviderInterface {
        return new PresenterDataProvider(
            $presenter,
            $repository,
            $conditions,
            $sortFields,
            $limit,
            $page
        );
    }
}
