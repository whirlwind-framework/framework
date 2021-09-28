<?php declare(strict_types=1);

namespace Whirlwind\Infrastructure\Persistence\Mongo\Structure;

use Whirlwind\Infrastructure\Persistence\Mongo\Structure\MongoCollection;
use Whirlwind\Infrastructure\Persistence\Mongo\Structure\MongoDatabase;

class MongoCollectionFactory
{
    public function create(MongoDatabase $database, string $name): MongoCollection
    {
        return new MongoCollection($database, $name);
    }
}
