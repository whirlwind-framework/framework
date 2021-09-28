<?php declare(strict_types=1);

namespace Whirlwind\Infrastructure\Persistence;

use Whirlwind\Infrastructure\Persistence\ConnectionInterface;
use Whirlwind\Infrastructure\Persistence\QueryInterface;
use Whirlwind\Infrastructure\Persistence\ExpressionInterface;

abstract class Query implements QueryInterface
{
    protected ConnectionInterface $connection;

    protected $where;

    protected $limit;

    protected $offset;

    protected $orderBy;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function where($condition): self
    {
        $this->where = $condition;
        return $this;
    }

    public function andWhere($condition): self
    {
        if ($this->where === null) {
            $this->where = $condition;
        } else {
            $this->where = ['and', $this->where, $condition];
        }
        return $this;
    }

    public function orWhere($condition): self
    {
        if ($this->where === null) {
            $this->where = $condition;
        } else {
            $this->where = ['or', $this->where, $condition];
        }
        return $this;
    }

    public function filterWhere(array $condition): self
    {
        $condition = $this->filterCondition($condition);
        if ($condition !== []) {
            $this->where($condition);
        }
        return $this;
    }

    public function andFilterWhere(array $condition): self
    {
        $condition = $this->filterCondition($condition);
        if ($condition !== []) {
            $this->andWhere($condition);
        }
        return $this;
    }

    public function orFilterWhere(array $condition): self
    {
        $condition = $this->filterCondition($condition);
        if ($condition !== []) {
            $this->orWhere($condition);
        }

        return $this;
    }

    protected function filterCondition($condition)
    {
        if (!\is_array($condition)) {
            return $condition;
        }

        if (!isset($condition[0])) {
            foreach ($condition as $name => $value) {
                if ($this->isEmpty($value)) {
                    unset($condition[$name]);
                }
            }
            return $condition;
        }

        $operator = \array_shift($condition);

        switch (\strtoupper($operator)) {
            case 'NOT':
            case 'AND':
            case 'OR':
                foreach ($condition as $i => $operand) {
                    $subCondition = $this->filterCondition($operand);
                    if ($this->isEmpty($subCondition)) {
                        unset($condition[$i]);
                    } else {
                        $condition[$i] = $subCondition;
                    }
                }
                if (empty($condition)) {
                    return [];
                }
                break;
            case 'BETWEEN':
            case 'NOT BETWEEN':
                if (\array_key_exists(1, $condition) && \array_key_exists(2, $condition)) {
                    if ($this->isEmpty($condition[1]) || $this->isEmpty($condition[2])) {
                        return [];
                    }
                }
                break;
            default:
                if (\array_key_exists(1, $condition) && $this->isEmpty($condition[1])) {
                    return [];
                }
        }

        \array_unshift($condition, $operator);

        return $condition;
    }

    protected function isEmpty($value)
    {
        return $value === '' || $value === [] || $value === null || \is_string($value) && \trim($value) === '';
    }

    public function orderBy($columns): self
    {
        $this->orderBy = $this->normalizeOrderBy($columns);
        return $this;
    }

    public function addOrderBy($columns): self
    {
        $columns = $this->normalizeOrderBy($columns);
        if ($this->orderBy === null) {
            $this->orderBy = $columns;
        } else {
            $this->orderBy = \array_merge($this->orderBy, $columns);
        }
        return $this;
    }

    protected function normalizeOrderBy($columns)
    {
        if ($columns instanceof ExpressionInterface) {
            return [$columns];
        }

        if (\is_array($columns)) {
            return $columns;
        }

        $columns = \preg_split('/\s*,\s*/', \trim($columns), -1, PREG_SPLIT_NO_EMPTY);
        $result = [];
        foreach ($columns as $column) {
            if (\preg_match('/^(.*?)\s+(asc|desc)$/i', $column, $matches)) {
                $result[$matches[1]] = \strcasecmp($matches[2], 'desc') ? SORT_ASC : SORT_DESC;
            } else {
                $result[$column] = SORT_ASC;
            }
        }

        return $result;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }
}
