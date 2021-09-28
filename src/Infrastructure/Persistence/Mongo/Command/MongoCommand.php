<?php declare(strict_types=1);

namespace Whirlwind\Infrastructure\Persistence\Mongo\Command;

use MongoDB\BSON\ObjectID;
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Command;
use MongoDB\Driver\Cursor;
use MongoDB\Driver\Exception\RuntimeException;
use MongoDB\Driver\Query;
use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\WriteConcern;
use MongoDB\Driver\WriteResult;
use Whirlwind\Infrastructure\Persistence\Mongo\MongoConnection;

class MongoCommand
{
    protected MongoConnection $connection;

    protected ?string $databaseName;

    protected array $document = [];

    private $readPreference;

    private $writeConcern;

    private $readConcern;

    public function __construct(MongoConnection $connection, string $databaseName = null, array $document = [])
    {
        $this->connection = $connection;
        $this->databaseName = $databaseName;
        $this->document = $document;
    }

    public function getReadPreference(): ReadPreference
    {
        if (!\is_object($this->readPreference)) {
            if ($this->readPreference === null) {
                $this->readPreference = $this->connection->getManager()->getReadPreference();
            } elseif (\is_scalar($this->readPreference)) {
                $this->readPreference = new ReadPreference($this->readPreference);
            }
        }
        return $this->readPreference;
    }

    public function getWriteConcern(): ?WriteConcern
    {
        if ($this->writeConcern !== null) {
            if (\is_scalar($this->writeConcern)) {
                $this->writeConcern = new WriteConcern($this->writeConcern);
            }
        }
        return $this->writeConcern;
    }

    public function getReadConcern(): ?ReadConcern
    {
        if ($this->readConcern !== null) {
            if (\is_scalar($this->readConcern)) {
                $this->readConcern = new ReadConcern($this->readConcern);
            }
        }
        return $this->readConcern;
    }

    public function execute(): Cursor
    {
        $databaseName = $this->databaseName === null ? $this->connection->getDefaultDatabaseName() : $this->databaseName;
        $this->connection->open();
        $mongoCommand = new Command($this->document);
        $cursor = $this->connection->getManager()->executeCommand($databaseName, $mongoCommand, $this->getReadPreference());
        $cursor->setTypeMap($this->connection->getTypeMap());
        return $cursor;
    }

    public function executeBatch($collectionName, $options = []): array
    {
        $databaseName = $this->databaseName === null ? $this->connection->getDefaultDatabaseName() : $this->databaseName;
        $batch = new BulkWrite($options);
        $insertedIds = [];
        foreach ($this->document as $key => $operation) {
            switch ($operation['type']) {
                case 'insert':
                    $insertedIds[$key] = $batch->insert($operation['document']);
                    break;
                case 'update':
                    $batch->update($operation['condition'], $operation['document'], $operation['options']);
                    break;
                case 'delete':
                    $batch->delete($operation['condition'], isset($operation['options']) ? $operation['options'] : []);
                    break;
                default:
                    throw new \InvalidArgumentException("Unsupported batch operation type '{$operation['type']}'");
            }
        }

        $this->connection->open();
        $writeResult = $this->connection->getManager()->executeBulkWrite($databaseName . '.' . $collectionName, $batch, $this->getWriteConcern());

        return [
            'insertedIds' => $insertedIds,
            'result' => $writeResult,
        ];
    }

    public function query($collectionName, $options = []): Cursor
    {
        $databaseName = $this->databaseName === null ? $this->connection->getDefaultDatabaseName() : $this->databaseName;

        $readConcern = $this->getReadConcern();
        if ($readConcern !== null) {
            $options['readConcern'] = $readConcern;
        }
        $query = new Query($this->document, $options);
        $this->connection->open();
        $cursor = $this->connection->getManager()->executeQuery($databaseName . '.' . $collectionName, $query, $this->getReadPreference());
        $cursor->setTypeMap($this->connection->getTypeMap());
        return $cursor;
    }

    public function count($collectionName, $condition = [], $options = [])
    {
        $this->document = $this->connection->getQueryBuilder()->count($collectionName, $condition, $options);
        $result = \current($this->execute()->toArray());
        return $result['n'];
    }

    public function addInsert($document): self
    {
        $this->document[] = [
            'type' => 'insert',
            'document' => $document,
        ];
        return $this;
    }

    public function addUpdate($condition, $document, $options = []): self
    {
        $options = \array_merge(
            [
                'multi' => true,
                'upsert' => false,
            ],
            $options
        );
        if ($options['multi']) {
            $keys = \array_keys($document);
            if (!empty($keys) && \strncmp('$', $keys[0], 1) !== 0) {
                $document = ['$set' => $document];
            }
        }
        $this->document[] = [
            'type' => 'update',
            'condition' => $this->connection->getQueryBuilder()->buildCondition($condition),
            'document' => $document,
            'options' => $options,
        ];
        return $this;
    }

    public function addDelete($condition, $options = []): self
    {
        $this->document[] = [
            'type' => 'delete',
            'condition' => $this->connection->getQueryBuilder()->buildCondition($condition),
            'options' => $options,
        ];
        return $this;
    }

    public function insert($collectionName, $document, $options = [])
    {
        $this->document = [];
        $this->addInsert($document);
        $result = $this->executeBatch($collectionName, $options);
        if ($result['result']->getInsertedCount() < 1) {
            return false;
        }
        return \reset($result['insertedIds']);
    }

    public function batchInsert($collectionName, $documents, $options = [])
    {
        $this->document = [];
        foreach ($documents as $key => $document) {
            $this->document[$key] = [
                'type' => 'insert',
                'document' => $document
            ];
        }
        $result = $this->executeBatch($collectionName, $options);
        if ($result['result']->getInsertedCount() < 1) {
            return false;
        }
        return $result['insertedIds'];
    }

    public function update($collectionName, $condition, $document, $options = []): WriteResult
    {
        $batchOptions = [];
        foreach (['bypassDocumentValidation'] as $name) {
            if (isset($options[$name])) {
                $batchOptions[$name] = $options[$name];
                unset($options[$name]);
            }
        }
        $this->document = [];
        $this->addUpdate($condition, $document, $options);
        $result = $this->executeBatch($collectionName, $batchOptions);
        return $result['result'];
    }

    public function delete($collectionName, $condition, $options = []): WriteResult
    {
        $batchOptions = [];
        foreach (['bypassDocumentValidation'] as $name) {
            if (isset($options[$name])) {
                $batchOptions[$name] = $options[$name];
                unset($options[$name]);
            }
        }
        $this->document = [];
        $this->addDelete($condition, $options);
        $result = $this->executeBatch($collectionName, $batchOptions);
        return $result['result'];
    }

    public function find($collectionName, $condition, $options = [])
    {
        $queryBuilder = $this->connection->getQueryBuilder();
        $this->document = $queryBuilder->buildCondition($condition);
        if (isset($options['projection'])) {
            $options['projection'] = $queryBuilder->buildSelectFields($options['projection']);
        }
        if (isset($options['sort'])) {
            $options['sort'] = $queryBuilder->buildSortFields($options['sort']);
        }
        if (\array_key_exists('limit', $options)) {
            if ($options['limit'] === null || !\ctype_digit((string) $options['limit'])) {
                unset($options['limit']);
            } else {
                $options['limit'] = (int)$options['limit'];
            }
        }
        if (\array_key_exists('skip', $options)) {
            if ($options['skip'] === null || !\ctype_digit((string) $options['skip'])) {
                unset($options['skip']);
            } else {
                $options['skip'] = (int)$options['skip'];
            }
        }
        return $this->query($collectionName, $options);
    }

    public function findAndModify($collectionName, $condition = [], $update = [], $options = [])
    {
        $this->document = $this->connection->getQueryBuilder()->findAndModify($collectionName, $condition, $update, $options);
        $cursor = $this->execute();
        $result = current($cursor->toArray());
        if (!isset($result['value'])) {
            return null;
        }
        return $result['value'];
    }

    public function distinct($collectionName, $fieldName, $condition = [], $options = [])
    {
        $this->document = $this->connection->getQueryBuilder()->distinct($collectionName, $fieldName, $condition, $options);
        $cursor = $this->execute();
        $result = \current($cursor->toArray());
        if (!isset($result['values']) || !\is_array($result['values'])) {
            return false;
        }
        return $result['values'];
    }

    public function group($collectionName, $keys, $initial, $reduce, $options = [])
    {
        $this->document = $this->connection->getQueryBuilder()->group($collectionName, $keys, $initial, $reduce, $options);
        $cursor = $this->execute();
        $result = \current($cursor->toArray());
        return $result['retval'];
    }

    public function mapReduce($collectionName, $map, $reduce, $out, $condition = [], $options = [])
    {
        $this->document = $this->connection->getQueryBuilder()->mapReduce($collectionName, $map, $reduce, $out, $condition, $options);
        $cursor = $this->execute();
        $result = \current($cursor->toArray());
        return \array_key_exists('results', $result) ? $result['results'] : $result['result'];
    }

    public function aggregate($collectionName, $pipelines, $options = [])
    {
        if (empty($options['cursor'])) {
            $returnCursor = false;
            $options['cursor'] = new \stdClass();
        } else {
            $returnCursor = true;
        }
        $this->document = $this->connection->getQueryBuilder()->aggregate($collectionName, $pipelines, $options);
        $cursor = $this->execute();
        if ($returnCursor) {
            return $cursor;
        }
        return $cursor->toArray();
    }

    public function explain($collectionName, $query)
    {
        $this->document = $this->connection->getQueryBuilder()->explain($collectionName, $query);
        $cursor = $this->execute();
        return \current($cursor->toArray());
    }
}
