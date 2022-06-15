<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Repository;

use Whirlwind\Domain\Repository\RepositoryInterface;
use Whirlwind\Domain\Repository\ResultFactoryInterface;
use Whirlwind\Domain\Repository\ResultInterface;
use Whirlwind\Infrastructure\Hydrator\Hydrator;
use Whirlwind\Infrastructure\Repository\Exception\InsertException;
use Whirlwind\Infrastructure\Repository\Exception\InvalidModelClassException;
use Whirlwind\Infrastructure\Repository\Exception\NotFoundException;
use Whirlwind\Infrastructure\Repository\TableGateway\TableGatewayInterface;

class Repository implements RepositoryInterface
{
    protected TableGatewayInterface $tableGateway;

    protected Hydrator $hydrator;

    protected string $modelClass;

    protected ResultFactoryInterface $resultFactory;

    public function __construct(
        TableGatewayInterface $tableGateway,
        Hydrator $hydrator,
        string $modelClass,
        ResultFactoryInterface $resultFactory
    ) {
        $this->tableGateway = $tableGateway;
        $this->hydrator = $hydrator;
        $this->modelClass = $modelClass;
        $this->resultFactory = $resultFactory;
    }

    public function findOne(array $conditions = []): object
    {
        $data = $this->tableGateway->queryOne($conditions);
        if (!\is_array($data)) {
            throw new NotFoundException();
        }
        return $this->hydrator->hydrate($this->modelClass, $data);
    }

    public function findAll(
        array $conditions = [],
        array $order = [],
        int $limit = 0,
        int $offset = 0
    ): ResultInterface {
        $result = $this->resultFactory->create(
            $this->tableGateway->queryAll($conditions, $order, $limit, $offset)
        );
        foreach ($result as $key => $row) {
            $result[$key] = $this->hydrator->hydrate($this->modelClass, $row);
        }
        return $result;
    }

    public function aggregate($column, $operator, array $conditions): string
    {
        return $this->tableGateway->aggregate($column, $operator, $conditions);
    }

    public function aggregateCount(string $field = '', array $conditions = []): string
    {
        return $this->tableGateway->aggregateCount($field, $conditions);
    }

    public function aggregateSum(string $field, array $conditions): string
    {
        return $this->tableGateway->aggregateSum($field, $conditions);
    }

    public function aggregateAverage(string $field, array $conditions): string
    {
        return $this->tableGateway->aggregateAverage($field, $conditions);
    }

    public function aggregateMin(string $field, array $conditions): string
    {
        return $this->tableGateway->aggregateMin($field, $conditions);
    }

    public function aggregateMax(string $field, array $conditions): string
    {
        return $this->tableGateway->aggregateMax($field, $conditions);
    }

    protected function validateModelClass(object $model)
    {
        if (!($model instanceof $this->modelClass)) {
            throw new InvalidModelClassException(
                'Invalid model class: ' . get_class($model) . '. Expected ' . $this->modelClass
            );
        }
    }

    public function insert(object $model): void
    {
        $this->validateModelClass($model);
        $data = $this->hydrator->extract($model);
        $primaryKeys = $this->tableGateway->insert($data);
        if (!\is_array($primaryKeys)) {
            throw new InsertException($data);
        }
        $this->hydrator->hydrate($model, $primaryKeys);
    }

    public function update(object $model): void
    {
        $this->validateModelClass($model);
        $data = $this->hydrator->extract($model);
        $this->tableGateway->updateOne($data);
    }

    public function delete(object $model): void
    {
        $this->validateModelClass($model);
        $data = $this->hydrator->extract($model);
        $this->tableGateway->deleteOne($data);
    }

    public function updateAll(array $data, array $conditions): int
    {
        return $this->tableGateway->updateAll($data, $conditions);
    }

    public function deleteAll(array $conditions): int
    {
        return $this->tableGateway->deleteAll($conditions);
    }

    /**
     * @param array $data
     * @return object
     */
    public function create(array $data = []): object
    {
        return $this->hydrator->hydrate($this->modelClass, $data);
    }

    /**
     * @param object $entity
     * @param array $data
     */
    public function populate(object $entity, array $data): void
    {
        $this->hydrator->hydrate($entity, $data);
    }
}
