<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Persistence\Mongo\Structure;

use MongoDB\BSON\ObjectId;
use Whirlwind\Infrastructure\Persistence\Mongo\Structure\MongoDatabase;

class MongoCollection
{
    protected $database;

    protected $name;

    public function __construct(MongoDatabase $database, string $name)
    {
        $this->database = $database;
        $this->name = $name;
    }

    public function find($condition = [], $fields = [], $options = [])
    {
        if (!empty($fields)) {
            $options['projection'] = $fields;
        }
        return $this->database->createCommand()->find($this->name, $condition, $options);
    }

    public function findOne($condition = [], $fields = [], $options = [])
    {
        $options['limit'] = 1;
        $cursor = $this->find($condition, $fields, $options);
        $rows = $cursor->toArray();
        return empty($rows) ? null : \current($rows);
    }

    public function findAndModify($condition, $update, $options = [])
    {
        return $this->database->createCommand()->findAndModify($this->name, $condition, $update, $options);
    }

    public function insert($data, $options = [])
    {
        return $this->database->createCommand()->insert($this->name, $data, $options);
    }

    public function batchInsert($rows, $options = [])
    {
        $insertedIds = $this->database->createCommand()->batchInsert($this->name, $rows, $options);
        foreach ($rows as $key => $row) {
            $rows[$key]['_id'] = $insertedIds[$key];
        }
        return $rows;
    }

    public function update($condition, $newData, $options = [])
    {
        $writeResult = $this->database->createCommand()->update($this->name, $condition, $newData, $options);
        return $writeResult->getModifiedCount() + $writeResult->getUpsertedCount();
    }

    public function save($data, $options = [])
    {
        if (empty($data['_id'])) {
            return $this->insert($data, $options);
        }
        $id = $data['_id'];
        unset($data['_id']);
        $this->update(['_id' => $id], ['$set' => $data], ['upsert' => true]);
        return is_object($id) ? $id : new ObjectID($id);
    }

    public function remove($condition = [], $options = [])
    {
        $options = array_merge(['limit' => 0], $options);
        $writeResult = $this->database->createCommand()->delete($this->name, $condition, $options);
        return $writeResult->getDeletedCount();
    }

    public function count($condition = [], $options = [])
    {
        return $this->database->createCommand()->count($this->name, $condition, $options);
    }

    public function distinct($column, $condition = [], $options = [])
    {
        return $this->database->createCommand()->distinct($this->name, $column, $condition, $options);
    }

    public function aggregate($pipelines, $options = [])
    {
        return $this->database->createCommand()->aggregate($this->name, $pipelines, $options);
    }

    public function group($keys, $initial, $reduce, $options = [])
    {
        return $this->database->createCommand()->group($this->name, $keys, $initial, $reduce, $options);
    }

    public function mapReduce($map, $reduce, $out, $condition = [], $options = [])
    {
        return $this->database->createCommand()->mapReduce($this->name, $map, $reduce, $out, $condition, $options);
    }
}
