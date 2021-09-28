<?php declare(strict_types=1);

namespace Whirlwind\Infrastructure\DataProvider;

use Whirlwind\Domain\DataProvider\DataProviderFactoryInterface;
use Whirlwind\Domain\Presenter\PresenterInterface;
use Whirlwind\Domain\Repository\RepositoryInterface;

class PresenterDataProvider extends DataProvider
{
    protected $presenter;

    public function __construct(
        PresenterInterface $presenter,
        RepositoryInterface $repository,
        array $conditions = [],
        array $sortFields = [],
        int $limit = DataProviderFactoryInterface::DEFAULT_LIMIT,
        int $page = 1
    ) {
        $this->presenter = $presenter;
        parent::__construct($repository, $conditions, $sortFields, $limit, $page);
    }

    protected function loadData()
    {
        parent::loadData();
        foreach ($this->models as $key => $value) {
            $this->models[$key] = $this->presenter->decorate($value);
        }
    }
}
