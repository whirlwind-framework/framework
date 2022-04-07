<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Repository;

use Whirlwind\Domain\Repository\RepositoryInterface;
use Whirlwind\Infrastructure\Hydrator\Hydrator;
use Whirlwind\Infrastructure\Hydrator\Strategy\ObjectStrategy;
use Whirlwind\Infrastructure\Repository\Exception\InsertException;
use Whirlwind\Infrastructure\Repository\Exception\InvalidModelClassException;
use Whirlwind\Infrastructure\Repository\Exception\NotFoundException;
use Whirlwind\Infrastructure\Repository\Relation\Relation;
use Whirlwind\Infrastructure\Repository\Relation\RelationCollection;
use Whirlwind\Infrastructure\Repository\TableGateway\TableGatewayInterface;

class Repository implements RepositoryInterface
{
    protected $tableGateway;

    protected $hydrator;

    protected $modelClass;

    protected $relationCollection;

    public function __construct(
        TableGatewayInterface $tableGateway,
        Hydrator $hydrator,
        string $modelClass,
        RelationCollection $relationCollection = null
    ) {
        $this->tableGateway = $tableGateway;
        $this->hydrator = $hydrator;
        $this->modelClass = $modelClass;
        $this->relationCollection = $relationCollection;
    }

    protected function getRelation($name): Relation
    {
        if (\is_null($this->relationCollection)) {
            throw new \InvalidArgumentException("Relation $name do not exist");
        }
        $relation = $this->relationCollection->getRelationByProperty($name);
        if (!($relation instanceof Relation)) {
            throw new \InvalidArgumentException("Relation $name do not exist");
        }
        return $relation;
    }

    protected function normalizeResultSet(array $data, array $relations): array
    {
        /**
         * @var string $property
         * @var Relation $relation
         */
        foreach ($relations as $property => $relation) {
            $relationData = [];
            foreach ($data as $field => $value) {
                if (str_starts_with($field, $relation->getRelatedCollection() . '_relation_')) {
                    $fieldName = \str_replace(
                        $relation->getRelatedCollection() . '_relation_',
                        '',
                        $field
                    );
                    $relationData[$fieldName] = $value;
                }
            }
            $data[$property] = $relationData;
        }
        return $data;
    }

    protected function applyRelationStrategies(array $relations)
    {
        /**
         * @var string $property
         * @var Relation $relation
         */
        foreach ($relations as $property => $relation) {
            $this->hydrator->addStrategy($property, new ObjectStrategy($this->hydrator, $relation->getRelatedModel()));
        }
    }

    public function findOne(array $conditions = [], array $with = [], array $select = []): object
    {
        $relations = [];
        foreach ($with as $relationName) {
            $relations[$relationName] = $this->getRelation($relationName);
        }
        $data = $this->tableGateway->queryOne($conditions, $relations, $select);
        if (!\is_array($data)) {
            throw new NotFoundException();
        }
        if (!empty($relations)) {
            $data = $this->normalizeResultSet($data, $relations);
            $this->applyRelationStrategies($relations);
        }
        return $this->hydrator->hydrate($this->modelClass, $data);
    }

    public function findAll(
        array $conditions = [],
        array $order = [],
        int $limit = 0,
        int $offset = 0,
        array $with = [],
        array $select = []
    ): array {
        $result = [];
        $relations = [];
        foreach ($with as $relationName) {
            $relations[$relationName] = $this->getRelation($relationName);
        }
        if (!empty($relations)) {
            $this->applyRelationStrategies($relations);
        }
        $data = $this->tableGateway->queryAll($conditions, $order, $limit, $offset, $relations, $select);
        foreach ($data as $row) {
            if (!empty($relations)) {
                $row = $this->normalizeResultSet($row, $relations);
            }
            $result[] = $this->hydrator->hydrate($this->modelClass, $row);
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
