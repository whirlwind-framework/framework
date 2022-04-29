<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Repository\TableGateway;

class InMemoryTableGateway implements TableGatewayInterface
{
    protected $data;

    protected $primaryKeys;

    protected $lastId;

    public function __construct(array $data = [], array $primaryKeys = ['id'])
    {
        $this->data = $data;
        $this->primaryKeys = $primaryKeys;
        $this->lastId = 0;
        if (\count($this->primaryKeys) == 1 ) {
            $max = 0;
            foreach ($this->data as $item) {
                if (isset($item[$this->primaryKeys[0]]) and $item[$this->primaryKeys[0]] > $max) {
                    $max = $item[$this->primaryKeys[0]];
                }
            }
            $this->lastId = $max;
        }
    }

    public function queryOne(array $conditions, array $relations = []): ?array
    {
        foreach ($this->data as $item) {
            $found = true;
            foreach ($conditions as $key => $value) {
                if (!isset($item[$key]) or $item[$key] != $value) {
                    $found = false;
                }
            }
            if ($found) {
                return $item;
            }
        }
        return null;
    }

    public function queryAll(
        array $conditions,
        array $order = [],
        int $limit = 0,
        int $offset = 0,
        array $relations = []
    ): array {
        $result = [];
        foreach ($this->data as $item) {
            $found = true;
            foreach ($conditions as $key => $value) {
                if (!isset($item[$key]) or $item[$key] != $value) {
                    $found = false;
                }
            }
            if ($found) {
                $result[] = $item;
            }
        }
        if ($limit > 0) {
            $result = \array_slice($result, $offset, $limit);
        }
        return $result;
    }

    public function insert(array $data): ?array
    {
        if (\count($this->primaryKeys) == 0 and !isset($data[$this->primaryKeys[0]])) {
            $this->lastId++;
            $data[$this->primaryKeys[0]] = $this->lastId;
        }
        $this->data[] = $data;
        $result = [];
        foreach ($this->primaryKeys as $key) {
            $result[$key] = isset($data[$key]) ? $data[$key] : null;
        }
        return $result;
    }

    public function updateOne(array $data): void
    {
        $keys = [];
        foreach ($this->primaryKeys as $key) {
            if (isset($data[$key])) {
                $keys[$key] = $data[$key];
            }
        }
        foreach ($this->data as $index => $item) {
            $found = true;
            foreach ($keys as $key => $value) {
                if (!isset($item[$key]) or $item[$key] != $value) {
                    $found = false;
                }
            }
            if ($found) {
                foreach ($data as $fName => $fValue) {
                    $this->data[$index][$fName] = $fValue;
                }
                return;
            }
        }
    }

    public function deleteOne(array $data): void
    {
        $keys = [];
        foreach ($this->primaryKeys as $key) {
            if (isset($data[$key])) {
                $keys[$key] = $data[$key];
            }
        }
        foreach ($this->data as $index => $item) {
            $found = true;
            foreach ($keys as $key => $value) {
                if (!isset($item[$key]) or $item[$key] != $value) {
                    $found = false;
                }
            }
            if ($found) {
                unset($this->data[$index]);
                return;
            }
        }
    }

    public function updateAll(array $data, array $conditions): int
    {
        $modified = 0;
        foreach ($this->data as $index => $item) {
            $found = true;
            foreach ($conditions as $key => $value) {
                if (!isset($item[$key]) or $item[$key] != $value) {
                    $found = false;
                }
            }
            if ($found) {
                foreach ($data as $fName => $fValue) {
                    $this->data[$index][$fName] = $fValue;
                }
                $modified++;
            }
        }
        return $modified;
    }

    public function deleteAll(array $conditions): int
    {
        $modified = 0;
        foreach ($this->data as $index => $item) {
            $found = true;
            foreach ($conditions as $key => $value) {
                if (!isset($item[$key]) or $item[$key] != $value) {
                    $found = false;
                }
            }
            if ($found) {
                unset($this->data[$index]);
                $modified++;
            }
        }
        return $modified;

    }

    public function aggregate($column, $operator, array $conditions): string
    {
        $method = 'aggregate' . \ucfirst(\strtolower($operator));
        if (!\method_exists($this, $method)) {
            throw new \LogicException('Aggregation for operator ' . $operator . ' not implemented');
        }
        return $this->$method($column, $conditions);
    }

    public function aggregateCount(string $field = '', array $conditions = []): string
    {
        $count = 0;
        foreach ($this->data as $index => $item) {
            $found = true;
            foreach ($conditions as $key => $value) {
                if (!isset($item[$key]) or $item[$key] != $value) {
                    $found = false;
                }
            }
            if ($found) {
                $count++;
            }
        }
        return (string)$count;
    }

    public function aggregateSum(string $field, array $conditions = []): string
    {
        $sum = 0;
        foreach ($this->data as $index => $item) {
            $found = true;
            foreach ($conditions as $key => $value) {
                if (!isset($item[$key]) or $item[$key] != $value) {
                    $found = false;
                }
            }
            if ($found and isset($item[$field])) {
                $sum = $sum + $item[$field];
            }
        }
        return (string)$sum;
    }

    public function aggregateAverage(string $field, array $conditions = []): string
    {
        $values = [];
        foreach ($this->data as $index => $item) {
            $found = true;
            foreach ($conditions as $key => $value) {
                if (!isset($item[$key]) or $item[$key] != $value) {
                    $found = false;
                }
            }
            if ($found and isset($item[$field])) {
                $values[] = $item[$field];
            }
        }
        $sum = 0;
        foreach ($values as $val) {
            $sum = $sum + $val;
        }
        return (string)($sum / \count($values));
    }

    public function aggregateMin(string $field, array $conditions = []): string
    {
        $min = null;
        foreach ($this->data as $index => $item) {
            $found = true;
            foreach ($conditions as $key => $value) {
                if (!isset($item[$key]) or $item[$key] != $value) {
                    $found = false;
                }
            }
            if ($found and isset($item[$field])) {
                if (null === $min) {
                    $min = $item[$field];
                }
                if ($min > $item[$field]) {
                    $min = $item[$field];
                }
            }
        }
        return (string)$min;
    }

    public function aggregateMax(string $field, array $conditions = []): string
    {
        $max = null;
        foreach ($this->data as $index => $item) {
            $found = true;
            foreach ($conditions as $key => $value) {
                if (!isset($item[$key]) or $item[$key] != $value) {
                    $found = false;
                }
            }
            if ($found and isset($item[$field])) {
                if (null === $max) {
                    $max = $item[$field];
                }
                if ($max < $item[$field]) {
                    $max = $item[$field];
                }
            }
        }
        return (string)$max;
    }
}
