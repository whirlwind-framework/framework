<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Persistence\Mongo\Command;

use Whirlwind\Infrastructure\Persistence\Mongo\Command\MongoCommand;
use Whirlwind\Infrastructure\Persistence\Mongo\MongoConnection;

class MongoCommandFactory
{
    public function create(MongoConnection $connection, string $databaseName = null, array $document = []): MongoCommand
    {
        return new MongoCommand($connection, $databaseName, $document);
    }
}
