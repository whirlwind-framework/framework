<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Persistence\Mongo\Query;

use Whirlwind\Infrastructure\Persistence\Mongo\MongoConnection;
use Whirlwind\Infrastructure\Persistence\Mongo\Query\MongoQueryBuilder;

class MongoQueryBuilderFactory
{
    public function create(MongoConnection $connection): MongoQueryBuilder
    {
        return new MongoQueryBuilder($connection);
    }
}
