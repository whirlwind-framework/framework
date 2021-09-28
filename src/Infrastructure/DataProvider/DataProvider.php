<?php declare(strict_types=1);

namespace Whirlwind\Infrastructure\DataProvider;

use Whirlwind\Domain\DataProvider\DataProviderFactoryInterface;
use Whirlwind\Domain\DataProvider\DataProviderInterface;
use Whirlwind\Domain\DataProvider\PaginationInterface;
use Whirlwind\Domain\Repository\RepositoryInterface;

class DataProvider implements DataProviderInterface, \JsonSerializable
{
    protected $repository;

    protected $conditions;

    protected $sortFields;

    protected $limit;

    protected $page;

    protected $models;

    protected $pagination;

    protected $dataLoaded = false;

    public function __construct(
        RepositoryInterface $repository,
        array $conditions = [],
        array $sortFields = [],
        int $limit = DataProviderFactoryInterface::DEFAULT_LIMIT,
        int $page = 1
    ) {
        $this->repository = $repository;
        $this->conditions = $conditions;
        $this->sortFields = $sortFields;
        $this->limit = $limit;
        $this->page = $page;
    }

    protected function loadData()
    {
        $total = (int)$this->repository->aggregateCount('', $this->conditions);
        $offset = ($this->page - 1) * $this->limit;
        $this->models = $this->repository->findAll($this->conditions, $this->sortFields, $this->limit, $offset);
        $this->pagination = new Pagination($total, $this->limit, $this->page);
        $this->dataLoaded = true;
    }

    public function getModels(): array
    {
        if (!$this->dataLoaded) {
            $this->loadData();
        }
        return $this->models;
    }

    public function getPagination(): PaginationInterface
    {
        if (!$this->dataLoaded) {
            $this->loadData();
        }
        return $this->pagination;
    }

    public function jsonSerialize()
    {
        return [
            'items' => $this->models,
            'pagination' => $this->pagination
        ];
    }
}
