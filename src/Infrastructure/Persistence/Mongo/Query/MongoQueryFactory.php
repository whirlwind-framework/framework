<?php declare(strict_types=1);

namespace Whirlwind\Infrastructure\Persistence\Mongo\Query;

use Whirlwind\Infrastructure\Persistence\Mongo\MongoConnection;
use Whirlwind\Infrastructure\Persistence\Mongo\Query\MongoQuery;

class MongoQueryFactory
{
    public function create(MongoConnection $connection): MongoQuery
    {
        return new MongoQuery($connection);
    }
}
