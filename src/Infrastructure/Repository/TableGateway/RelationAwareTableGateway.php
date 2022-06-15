<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Repository\TableGateway;

use Whirlwind\Infrastructure\Persistence\ConditionBuilderInterface;
use Whirlwind\Infrastructure\Persistence\ConnectionInterface;
use Whirlwind\Infrastructure\Persistence\QueryFactoryInterface;
use Whirlwind\Infrastructure\Persistence\QueryInterface;
use Whirlwind\Infrastructure\Repository\Relation\Relation;
use Whirlwind\Infrastructure\Repository\Relation\RelationCollection;

abstract class RelationAwareTableGateway extends TableGateway
{
    protected ?RelationCollection $relationCollection;

    public function __construct(
        ConnectionInterface $connection,
        QueryFactoryInterface $queryFactory,
        ConditionBuilderInterface $conditionBuilder,
        string $collectionName,
        RelationCollection $relationCollection
    ) {
        parent::__construct($connection, $queryFactory, $conditionBuilder, $collectionName);
        $this->relationCollection = $relationCollection;
    }

    protected function strStartsWith(string $haystack, string $needle): bool
    {
        return 0 === \strncmp($haystack, $needle, \strlen($needle));
    }

    protected function normalizeResultSet(array $data): array
    {
        /**
         * @var Relation $relation
         */
        foreach ($this->relationCollection as $relation) {
            $relationData = [];
            foreach ($data as $field => $value) {
                if ($this->strStartsWith($field, $relation->getRelatedCollection() . '_relation_')) {
                    $fieldName = \str_replace($relation->getRelatedCollection() . '_relation_', '', $field);
                    $relationData[$fieldName] = $value;
                }
            }
            $data[$relation->getProperty()] = $relationData;
        }
        return $data;
    }

    abstract protected function addRelationSelectList(QueryInterface $query): void;

    abstract protected function applyRelationStatements(QueryInterface $query): void;

    protected function buildQueryOne(array $conditions): QueryInterface
    {
        $query = parent::buildQueryOne($conditions);
        $this->addRelationSelectList($query);
        $this->applyRelationStatements($query);
        return $query;
    }

    public function queryOne(array $conditions): ?array
    {
        $result = parent::queryOne($conditions);
        if (\is_array($result)) {
            $result = $this->normalizeResultSet($result);
        }
        return $result;
    }

    protected function buildQueryAll(array $conditions, array $order, int $limit, int $offset): QueryInterface
    {
        $query = parent::buildQueryAll($conditions, $order, $limit, $offset);
        $this->addRelationSelectList($query);
        $this->applyRelationStatements($query);
        return $query;
    }

    public function queryAll(
        array $conditions,
        array $order = [],
        int $limit = 0,
        int $offset = 0
    ): array {
        $result = parent::queryAll($conditions, $order, $limit, $offset);
        foreach ($result as $key => $row) {
            $result[$key] = $this->normalizeResultSet($row);
        }
        return $result;
    }
}
