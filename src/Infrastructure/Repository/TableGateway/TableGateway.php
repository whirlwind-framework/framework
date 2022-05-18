<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Repository\TableGateway;

use Whirlwind\Infrastructure\Persistence\ConditionBuilderInterface;
use Whirlwind\Infrastructure\Persistence\ConnectionInterface;
use Whirlwind\Infrastructure\Persistence\QueryFactoryInterface;
use Whirlwind\Infrastructure\Persistence\QueryInterface;

abstract class TableGateway implements TableGatewayInterface
{
    protected ConnectionInterface $connection;

    protected QueryFactoryInterface $queryFactory;

    protected ConditionBuilderInterface $conditionBuilder;

    protected string $collectionName;

    public function __construct(
        ConnectionInterface $connection,
        QueryFactoryInterface $queryFactory,
        ConditionBuilderInterface $conditionBuilder,
        string $collectionName
    ) {
        $this->connection = $connection;
        $this->queryFactory = $queryFactory;
        $this->conditionBuilder = $conditionBuilder;
        $this->collectionName = $collectionName;
    }

    protected function buildQueryOne(array $conditions): QueryInterface
    {
        $query = $this->queryFactory->create($this->connection);
        $query
            ->from($this->collectionName)
            ->where($conditions);
        return $query;
    }

    public function queryOne(array $conditions): ?array
    {
        $conditions = $this->conditionBuilder->build($conditions);
        $query = $this->buildQueryOne($conditions);
        $result = $query->one();
        return $result ?: null;
    }

    protected function buildQueryAll(array $conditions, array $order, int $limit, int $offset): QueryInterface
    {
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

        return $query;
    }

    public function queryAll(
        array $conditions,
        array $order = [],
        int $limit = 0,
        int $offset = 0
    ): array {
        $conditions = $this->conditionBuilder->build($conditions);
        $query = $this->buildQueryAll($conditions, $order, $limit, $offset);

        return $query->all();
    }
}
