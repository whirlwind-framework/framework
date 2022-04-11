<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Repository\TableGateway;

use MongoDB\BSON\ObjectId;
use Whirlwind\Infrastructure\Persistence\Mongo\ConditionBuilder\ConditionBuilder;
use Whirlwind\Infrastructure\Persistence\Mongo\MongoConnection;
use Whirlwind\Infrastructure\Repository\Exception\DeleteException;
use Whirlwind\Infrastructure\Repository\Exception\UpdateException;
use Whirlwind\Infrastructure\Persistence\Mongo\Query\MongoQuery;
use Whirlwind\Infrastructure\Persistence\Mongo\Query\MongoQueryFactory;

class MongoTableGateway implements TableGatewayInterface
{
    protected $connection;

    protected $queryFactory;

    protected $conditionBuilder;

    protected $collectionName;

    public function __construct(
        MongoConnection $connection,
        MongoQueryFactory $queryFactory,
        ConditionBuilder $conditionBuilder,
        string $collectionName
    ) {
        $this->connection = $connection;
        $this->queryFactory = $queryFactory;
        $this->conditionBuilder = $conditionBuilder;
        $this->collectionName = $collectionName;
    }

    public function queryOne(array $conditions, array $relations = []): ?array
    {
        $conditions = $this->conditionBuilder->build($conditions);
        /** @var \Whirlwind\Infrastructure\Persistence\Mongo\Query\MongoQuery $query */
        $query = $this->queryFactory->create($this->connection);
        $query
            ->from($this->collectionName)
            ->where($conditions);
        $result = $query->one();
        return $result ?: null;
    }

    public function insert(array $data): ?array
    {
        if (!isset($data['_id'])) {
            $data['_id'] = new ObjectId();
        } elseif ($this->isObjectId($data['_id'])) {
            $data['_id'] = new ObjectId($data['_id']);
        }
        $newId = $this->connection->getCollection($this->collectionName)->insert($data);
        return ['_id' => $newId];
    }

    private function isObjectId($oid): bool
    {
        return \is_string($oid) && 24 === \strlen($oid) && 24 === \strspn($oid, '0123456789ABCDEFabcdef');
    }

    public function updateOne(array $data): void
    {
        if (empty($data['_id'])) {
            throw new UpdateException($data, "Primary key _id not provided");
        }
        $conditions = ['_id' => $data['_id']];
        unset($data['_id']);
        $this->updateAll($data, $conditions);
    }


    public function updateAll(array $data, array $conditions): int
    {
        $conditions = $this->conditionBuilder->build($conditions);
        return $this->connection->getCollection($this->collectionName)->update($conditions, $data);
    }

    public function deleteOne(array $data): void
    {
        if (empty($data['_id'])) {
            throw new DeleteException($data, "Primary key _id not provided");
        }
        $conditions = ['_id' => $data['_id']];
        $this->deleteAll($conditions);
    }

    public function deleteAll(array $conditions): int
    {
        $conditions = $this->conditionBuilder->build($conditions);
        return $this->connection->getCollection($this->collectionName)->remove($conditions);
    }

    public function queryAll(
        array $conditions,
        array $order = [],
        int $limit = 0,
        int $offset = 0,
        array $relations = []
    ): array {
        $conditions = $this->conditionBuilder->build($conditions);
        /** @var \Whirlwind\Infrastructure\Persistence\Mongo\Query\MongoQuery $query */
        $query = $this->queryFactory->create($this->connection);
        $query
            ->from($this->collectionName)
            ->where($conditions);

        if ($limit > 0) {
            $query->limit($limit)->offset($offset);
        }

        if (!empty($order)) {
            $query->orderBy($order);
        }

        return $query->all();
    }

    public function aggregate($column, $operator, array $conditions): string
    {
        $conditions = $this->conditionBuilder->build($conditions);
        /** @var \Whirlwind\Infrastructure\Persistence\Mongo\Query\MongoQuery $query */
        $query = $this->queryFactory->create($this->connection);
        return (string)$query->from($this->collectionName)->where($conditions)->aggregate($column, $operator);
    }

    public function aggregateCount(string $field = '', array $conditions = []): string
    {
        $conditions = $this->conditionBuilder->build($conditions);
        /** @var MongoQuery $query */
        $query = $this->queryFactory->create($this->connection);
        return (string)$query->from($this->collectionName)->where($conditions)->count('*');
    }

    public function aggregateSum(string $field, array $conditions = []): string
    {
        $conditions = $this->conditionBuilder->build($conditions);
        /** @var MongoQuery $query */
        $query = $this->queryFactory->create($this->connection);
        return (string)$query->from($this->collectionName)->where($conditions)->sum($field);
    }

    public function aggregateAverage(string $field, array $conditions = []): string
    {
        $conditions = $this->conditionBuilder->build($conditions);
        /** @var \Whirlwind\Infrastructure\Persistence\Mongo\Query\MongoQuery $query */
        $query = $this->queryFactory->create($this->connection);
        return (string)$query->from($this->collectionName)->where($conditions)->average($field);
    }

    public function aggregateMin(string $field, array $conditions = []): string
    {
        $conditions = $this->conditionBuilder->build($conditions);
        /** @var MongoQuery $query */
        $query = $this->queryFactory->create($this->connection);
        return (string)$query->from($this->collectionName)->where($conditions)->min($field);
    }

    public function aggregateMax(string $field, array $conditions = []): string
    {
        $conditions = $this->conditionBuilder->build($conditions);
        /** @var \Whirlwind\Infrastructure\Persistence\Mongo\Query\MongoQuery $query */
        $query = $this->queryFactory->create($this->connection);
        return (string)$query->from($this->collectionName)->where($conditions)->max($field);
    }
}
