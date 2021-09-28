<?php declare(strict_types=1);

namespace Whirlwind\Infrastructure\Persistence\Mongo\Structure;

use Whirlwind\Infrastructure\Persistence\Mongo\MongoConnection;
use Whirlwind\Infrastructure\Persistence\Mongo\Structure\MongoCollectionFactory;
use Whirlwind\Infrastructure\Persistence\Mongo\Structure\MongoDatabase;

class MongoDatabaseFactory
{
    protected $collectionFactory;

    public function __construct(MongoCollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    public function create(\Whirlwind\Infrastructure\Persistence\Mongo\MongoConnection $connection, string $name): MongoDatabase
    {
        return new MongoDatabase($this->collectionFactory, $connection, $name);
    }
}
