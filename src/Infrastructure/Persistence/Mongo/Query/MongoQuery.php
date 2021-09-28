<?php declare(strict_types=1);

namespace Whirlwind\Infrastructure\Persistence\Mongo\Query;

use Whirlwind\Infrastructure\Persistence\Mongo\MongoConnection;
use Whirlwind\Infrastructure\Persistence\Query;

class MongoQuery extends Query
{
    protected $select = [];

    protected $from;

    protected $options = [];

    public function __construct(MongoConnection $connection)
    {
        parent::__construct($connection);
    }

    public function select(array $fields): self
    {
        $this->select = $fields;
        return $this;
    }

    public function from($collection): self
    {
        $this->from = $collection;
        return $this;
    }

    public function options($options): self
    {
        $this->options = $options;
        return $this;
    }

    public function addOptions($options): self
    {
        if (\is_array($this->options)) {
            $this->options = \array_merge($this->options, $options);
        } else {
            $this->options = $options;
        }
        return $this;
    }

    public function andFilterCompare($name, $value, $defaultOperator = '='): self
    {
        $matches = [];
        if (\preg_match('/^(<>|>=|>|<=|<|=)/', $value, $matches)) {
            $op = $matches[1];
            $value = \substr($value, \strlen($op));
        } else {
            $op = $defaultOperator;
        }
        return $this->andFilterWhere([$op, $name, $value]);
    }

    public function getCollection()
    {
        return $this->connection->getCollection($this->from);
    }

    public function buildCursor()
    {
        $options = $this->options;
        if (!empty($this->orderBy)) {
            $options['sort'] = $this->orderBy;
        }
        $options['limit'] = $this->limit;
        $options['skip'] = $this->offset;
        $cursor = $this->getCollection()->find($this->composeCondition(), $this->select, $options);
        return $cursor;
    }

    protected function fetchRows($cursor, $all = true)
    {
        $result = $this->fetchRowsInternal($cursor, $all);
        return $result;
    }

    protected function fetchRowsInternal($cursor, $all)
    {
        $result = [];
        if ($all) {
            foreach ($cursor as $row) {
                $result[] = $row;
            }
        } else {
            if ($row = \current($cursor->toArray())) {
                $result = $row;
            } else {
                $result = false;
            }
        }
        return $result;
    }

    public function all(): array
    {
        if (!empty($this->emulateExecution)) {
            return [];
        }
        $cursor = $this->buildCursor();
        $rows = $this->fetchRows($cursor, true);
        return $rows;
    }

    public function one()
    {
        $cursor = $this->buildCursor();
        return $this->fetchRows($cursor, false);
    }

    public function scalar()
    {
        $originSelect = (array)$this->select;
        if (!isset($originSelect['_id']) && array_search('_id', $originSelect, true) === false) {
            $this->select['_id'] = false;
        }
        $cursor = $this->buildCursor();
        $row = $this->fetchRows($cursor, false);
        if (empty($row)) {
            return false;
        }
        return \reset($row);
    }

    public function modify($update, $options = [])
    {
        $collection = $this->getCollection();
        if (!empty($this->orderBy)) {
            $options['sort'] = $this->orderBy;
        }
        $options['fields'] = $this->select;
        return $collection->findAndModify($this->composeCondition(), $update, $options);
    }

    public function count($q = '*'): int
    {
        $collection = $this->getCollection();
        return (int)$collection->count($this->where, $this->options);
    }

    public function exists(): bool
    {
        $cursor = $this->buildCursor();
        foreach ($cursor as $row) {
            return true;
        }
        return false;
    }

    public function sum($q)
    {
        return $this->aggregate($q, 'sum');
    }

    public function average($q)
    {
        return $this->aggregate($q, 'avg');
    }

    public function min($q)
    {
        return $this->aggregate($q, 'min');
    }

    public function max($q)
    {
        return $this->aggregate($q, 'max');
    }

    public function aggregate($column, $operator)
    {
        $collection = $this->getCollection();
        $pipelines = [];
        if ($this->where !== null) {
            $pipelines[] = ['$match' => $this->where];
        }
        $pipelines[] = [
            '$group' => [
                '_id' => '1',
                'total' => [
                    '$' . $operator => '$' . $column
                ],
            ]
        ];
        $result = $collection->aggregate($pipelines);
        if (\array_key_exists(0, $result)) {
            return $result[0]['total'];
        }
        return null;
    }

    public function distinct($q)
    {
        $collection = $this->getCollection();
        if ($this->where !== null) {
            $condition = $this->where;
        } else {
            $condition = [];
        }
        $result = $collection->distinct($q, $condition);
        if ($result === false) {
            return [];
        }
        return $result;
    }

    protected function composeCondition()
    {
        if ($this->where === null) {
            return [];
        }
        return $this->where;
    }
}
