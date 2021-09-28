<?php declare(strict_types=1);

namespace Whirlwind\Infrastructure\Persistence\Mongo\Query;

use MongoDB\BSON\Javascript;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\Regex;
use MongoDB\Driver\Exception\InvalidArgumentException;
use Whirlwind\Infrastructure\Persistence\Mongo\MongoConnection;

class MongoQueryBuilder
{
    protected $connection;

    public function __construct(MongoConnection $connection)
    {
        $this->connection = $connection;
    }

    public function createCollection($collectionName, array $options = []): array
    {
        $document = \array_merge(['create' => $collectionName], $options);
        if (isset($document['indexOptionDefaults'])) {
            $document['indexOptionDefaults'] = (object) $document['indexOptionDefaults'];
        }
        if (isset($document['storageEngine'])) {
            $document['storageEngine'] = (object) $document['storageEngine'];
        }
        if (isset($document['validator'])) {
            $document['validator'] = (object) $document['validator'];
        }
        return $document;
    }

    public function dropDatabase(): array
    {
        return ['dropDatabase' => 1];
    }

    public function dropCollection($collectionName): array
    {
        return ['drop' => $collectionName];
    }

    public function listIndexes($collectionName, $options = []): array
    {
        return \array_merge(['listIndexes' => $collectionName], $options);
    }

    public function dropIndexes($collectionName, $index): array
    {
        return [
            'dropIndexes' => $collectionName,
            'index' => $index,
        ];
    }

    public function generateIndexName($columns): string
    {
        $parts = [];
        foreach ($columns as $column => $order) {
            $parts[] = $column . '_' . $order;
        }
        return \implode('_', $parts);
    }

    public function createIndexes($databaseName, $collectionName, $indexes): array
    {
        $normalizedIndexes = [];
        foreach ($indexes as $index) {
            if (!isset($index['key'])) {
                throw new \InvalidArgumentException('"key" is required for index specification');
            }
            $index['key'] = $this->buildSortFields($index['key']);
            if (!isset($index['ns'])) {
                if ($databaseName === null) {
                    $databaseName = $this->connection->getDefaultDatabaseName();
                }
                $index['ns'] = $databaseName . '.' . $collectionName;
            }
            if (!isset($index['name'])) {
                $index['name'] = $this->generateIndexName($index['key']);
            }
            $normalizedIndexes[] = $index;
        }
        return [
            'createIndexes' => $collectionName,
            'indexes' => $normalizedIndexes,
        ];
    }

    public function count($collectionName, $condition = [], $options = []): array
    {
        $document = ['count' => $collectionName];
        if (!empty($condition)) {
            $document['query'] = (object) $this->buildCondition($condition);
        }
        return \array_merge($document, $options);
    }

    public function findAndModify($collectionName, $condition = [], $update = [], $options = []): array
    {
        $document = \array_merge(['findAndModify' => $collectionName], $options);
        if (!empty($condition)) {
            $options['query'] = $this->buildCondition($condition);
        }
        if (!empty($update)) {
            $options['update'] = $update;
        }
        if (isset($options['fields'])) {
            $options['fields'] = $this->buildSelectFields($options['fields']);
        }
        if (isset($options['sort'])) {
            $options['sort'] = $this->buildSortFields($options['sort']);
        }
        foreach (['fields', 'query', 'sort', 'update'] as $name) {
            if (isset($options[$name])) {
                $document[$name] = (object) $options[$name];
            }
        }
        return $document;
    }

    public function distinct($collectionName, $fieldName, $condition = [], $options = []): array
    {
        $document = \array_merge(
            [
                'distinct' => $collectionName,
                'key' => $fieldName,
            ],
            $options
        );
        if (!empty($condition)) {
            $document['query'] = $this->buildCondition($condition);
        }
        return $document;
    }

    public function group($collectionName, $keys, $initial, $reduce, $options = []): array
    {
        if (!($reduce instanceof Javascript)) {
            $reduce = new Javascript((string) $reduce);
        }
        if (isset($options['condition'])) {
            $options['cond'] = $this->buildCondition($options['condition']);
            unset($options['condition']);
        }
        if (isset($options['finalize'])) {
            if (!($options['finalize'] instanceof Javascript)) {
                $options['finalize'] = new Javascript((string) $options['finalize']);
            }
        }
        if (isset($options['keyf'])) {
            $options['$keyf'] = $options['keyf'];
            unset($options['keyf']);
        }
        if (isset($options['$keyf'])) {
            if (!($options['$keyf'] instanceof Javascript)) {
                $options['$keyf'] = new Javascript((string) $options['$keyf']);
            }
        }
        $document = [
            'group' => \array_merge(
                [
                    'ns' => $collectionName,
                    'key' => $keys,
                    'initial' => $initial,
                    '$reduce' => $reduce,
                ],
                $options
            )
        ];
        return $document;
    }

    public function mapReduce($collectionName, $map, $reduce, $out, $condition = [], $options = []): array
    {
        if (!($map instanceof Javascript)) {
            $map = new Javascript((string) $map);
        }
        if (!($reduce instanceof Javascript)) {
            $reduce = new Javascript((string) $reduce);
        }
        $document = [
            'mapReduce' => $collectionName,
            'map' => $map,
            'reduce' => $reduce,
            'out' => $out
        ];
        if (!empty($condition)) {
            $document['query'] = $this->buildCondition($condition);
        }
        if (!empty($options)) {
            $document = \array_merge($document, $options);
        }
        return $document;
    }

    public function aggregate($collectionName, $pipelines, $options = []): array
    {
        foreach ($pipelines as $key => $pipeline) {
            if (isset($pipeline['$match'])) {
                $pipelines[$key]['$match'] = $this->buildCondition($pipeline['$match']);
            }
        }
        $document = \array_merge(
            [
                'aggregate' => $collectionName,
                'pipeline' => $pipelines,
                'allowDiskUse' => false,
            ],
            $options
        );
        return $document;
    }

    public function explain($collectionName, $query): array
    {
        $query = \array_merge(
            ['find' => $collectionName],
            $query
        );
        if (isset($query['filter'])) {
            $query['filter'] = (object) $this->buildCondition($query['filter']);
        }
        if (isset($query['projection'])) {
            $query['projection'] = $this->buildSelectFields($query['projection']);
        }
        if (isset($query['sort'])) {
            $query['sort'] = $this->buildSortFields($query['sort']);
        }
        return [
            'explain' => $query,
        ];
    }

    public function listDatabases($condition = [], $options = []): array
    {
        $document = \array_merge(['listDatabases' => 1], $options);
        if (!empty($condition)) {
            $document['filter'] = (object)$this->buildCondition($condition);
        }
        return $document;
    }

    public function listCollections($condition = [], $options = []): array
    {
        $document = \array_merge(['listCollections' => 1], $options);
        if (!empty($condition)) {
            $document['filter'] = (object)$this->buildCondition($condition);
        }
        return $document;
    }

    public function buildSelectFields($fields): array
    {
        $selectFields = [];
        foreach ((array)$fields as $key => $value) {
            if (\is_int($key)) {
                $selectFields[$value] = true;
            } else {
                $selectFields[$key] = \is_scalar($value) ? (bool)$value : $value;
            }
        }
        return $selectFields;
    }

    public function buildSortFields($fields): array
    {
        $sortFields = [];
        foreach ((array)$fields as $key => $value) {
            if (\is_int($key)) {
                $sortFields[$value] = +1;
            } else {
                if ($value === SORT_ASC) {
                    $value = +1;
                } elseif ($value === SORT_DESC) {
                    $value = -1;
                }
                $sortFields[$key] = $value;
            }
        }
        return $sortFields;
    }

    protected function normalizeConditionKeyword($key): string
    {
        static $map = [
            'AND' => '$and',
            'OR' => '$or',
            'IN' => '$in',
            'NOT IN' => '$nin',
        ];
        $matchKey = \strtoupper($key);
        if (\array_key_exists($matchKey, $map)) {
            return $map[$matchKey];
        }
        return $key;
    }

    protected function ensureMongoId($rawId)
    {
        if (\is_array($rawId)) {
            $result = [];
            foreach ($rawId as $key => $value) {
                $result[$key] = $this->ensureMongoId($value);
            }
            return $result;
        } elseif (\is_object($rawId)) {
            if ($rawId instanceof ObjectID) {
                return $rawId;
            } else {
                $rawId = (string) $rawId;
            }
        }
        try {
            $mongoId = new ObjectID($rawId);
        } catch (InvalidArgumentException $e) {
            $mongoId = $rawId;
        }
        return $mongoId;
    }

    public function buildCondition($condition): array
    {
        static $builders = [
            'NOT' => 'buildNotCondition',
            'AND' => 'buildAndCondition',
            'OR' => 'buildOrCondition',
            'BETWEEN' => 'buildBetweenCondition',
            'NOT BETWEEN' => 'buildBetweenCondition',
            'IN' => 'buildInCondition',
            'NOT IN' => 'buildInCondition',
            'REGEX' => 'buildRegexCondition',
            'LIKE' => 'buildLikeCondition',
        ];
        if (!\is_array($condition)) {
            throw new \InvalidArgumentException('Condition should be an array.');
        } elseif (empty($condition)) {
            return [];
        }
        if (isset($condition[0])) {
            $operator = \strtoupper($condition[0]);
            if (isset($builders[$operator])) {
                $method = $builders[$operator];
            } else {
                $operator = $condition[0];
                $method = 'buildSimpleCondition';
            }
            \array_shift($condition);
            return $this->$method($operator, $condition);
        }
        return $this->buildHashCondition($condition);
    }

    protected function isIndexed($array, $consecutive = false)
    {
        if (!\is_array($array)) {
            return false;
        }
        if (empty($array)) {
            return true;
        }
        if ($consecutive) {
            return \array_keys($array) === \range(0, \count($array) - 1);
        }
        foreach ($array as $key => $value) {
            if (!\is_int($key)) {
                return false;
            }
        }
        return true;
    }

    public function buildHashCondition($condition): array
    {
        $result = [];
        foreach ($condition as $name => $value) {
            if (\strncmp('$', $name, 1) === 0) {
                $result[$name] = $value;
            } else {
                if (\is_array($value)) {
                    if ($this->isIndexed($value)) {
                        $result = \array_merge($result, $this->buildInCondition('IN', [$name, $value]));
                    } else {
                        $result[$name] = $value;
                    }
                } else {
                    if ($name == '_id') {
                        $value = $this->ensureMongoId($value);
                    }
                    $result[$name] = $value;
                }
            }
        }
        return $result;
    }

    public function buildNotCondition($operator, $operands): array
    {
        if (\count($operands) !== 2) {
            throw new \InvalidArgumentException("Operator '$operator' requires two operands.");
        }
        list($name, $value) = $operands;
        $result = [];
        if (\is_array($value)) {
            $result[$name] = ['$not' => $this->buildCondition($value)];
        } else {
            if ($name == '_id') {
                $value = $this->ensureMongoId($value);
            }
            $result[$name] = ['$ne' => $value];
        }
        return $result;
    }

    public function buildAndCondition($operator, $operands): array
    {
        $operator = $this->normalizeConditionKeyword($operator);
        $parts = [];
        foreach ($operands as $operand) {
            $parts[] = $this->buildCondition($operand);
        }
        return [$operator => $parts];
    }

    public function buildOrCondition($operator, $operands): array
    {
        $operator = $this->normalizeConditionKeyword($operator);
        $parts = [];
        foreach ($operands as $operand) {
            $parts[] = $this->buildCondition($operand);
        }
        return [$operator => $parts];
    }

    public function buildBetweenCondition($operator, $operands): array
    {
        if (!isset($operands[0], $operands[1], $operands[2])) {
            throw new \InvalidArgumentException("Operator '$operator' requires three operands.");
        }
        list($column, $value1, $value2) = $operands;
        if (\strncmp('NOT', $operator, 3) === 0) {
            return [
                $column => [
                    '$lt' => $value1,
                    '$gt' => $value2,
                ]
            ];
        }
        return [
            $column => [
                '$gte' => $value1,
                '$lte' => $value2,
            ]
        ];
    }

    public function buildInCondition($operator, $operands): array
    {
        if (!isset($operands[0], $operands[1])) {
            throw new \InvalidArgumentException("Operator '$operator' requires two operands.");
        }
        list($column, $values) = $operands;
        $values = (array) $values;
        $operator = $this->normalizeConditionKeyword($operator);
        if (!\is_array($column)) {
            $columns = [$column];
            $values = [$column => $values];
        } elseif (\count($column) > 1) {
            return $this->buildCompositeInCondition($operator, $column, $values);
        } else {
            $columns = $column;
            $values = [$column[0] => $values];
        }
        $result = [];
        foreach ($columns as $column) {
            if ($column == '_id') {
                $inValues = $this->ensureMongoId($values[$column]);
            } else {
                $inValues = $values[$column];
            }
            $inValues = \array_values($inValues);
            if (\count($inValues) === 1 && $operator === '$in') {
                $result[$column] = $inValues[0];
            } else {
                $result[$column][$operator] = $inValues;
            }
        }
        return $result;
    }

    protected function buildCompositeInCondition($operator, $columns, $values): array
    {
        $result = [];
        $inValues = [];
        foreach ($values as $columnValues) {
            foreach ($columnValues as $column => $value) {
                if ($column == '_id') {
                    $value = $this->ensureMongoId($value);
                }
                $inValues[$column][] = $value;
            }
        }
        foreach ($columns as $column) {
            $columnInValues = \array_values($inValues[$column]);
            if (\count($columnInValues) === 1 && $operator === '$in') {
                $result[$column] = $columnInValues[0];
            } else {
                $result[$column][$operator] = $columnInValues;
            }
        }
        return $result;
    }

    public function buildRegexCondition($operator, $operands): array
    {
        if (!isset($operands[0], $operands[1])) {
            throw new \InvalidArgumentException("Operator '$operator' requires two operands.");
        }
        list($column, $value) = $operands;
        if (!($value instanceof Regex)) {
            if (\preg_match('~\/(.+)\/(.*)~', $value, $matches)) {
                $value = new Regex($matches[1], $matches[2]);
            } else {
                $value = new Regex($value, '');
            }
        }
        return [$column => $value];
    }

    public function buildLikeCondition($operator, $operands): array
    {
        if (!isset($operands[0], $operands[1])) {
            throw new \InvalidArgumentException("Operator '$operator' requires two operands.");
        }
        list($column, $value) = $operands;
        if (!($value instanceof Regex)) {
            $value = new Regex(preg_quote($value), 'i');
        }
        return [$column => $value];
    }

    public function buildSimpleCondition($operator, $operands): array
    {
        if (\count($operands) !== 2) {
            throw new \InvalidArgumentException("Operator '$operator' requires two operands.");
        }
        list($column, $value) = $operands;
        if (\strncmp('$', $operator, 1) !== 0) {
            static $operatorMap = [
                '>' => '$gt',
                '<' => '$lt',
                '>=' => '$gte',
                '<=' => '$lte',
                '!=' => '$ne',
                '<>' => '$ne',
                '=' => '$eq',
                '==' => '$eq',
            ];
            if (isset($operatorMap[$operator])) {
                $operator = $operatorMap[$operator];
            } else {
                throw new \InvalidArgumentException("Unsupported operator '{$operator}'");
            }
        }
        return [$column => [$operator => $value]];
    }
}
