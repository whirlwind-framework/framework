<?php declare(strict_types=1);

namespace Whirlwind\Infrastructure\Persistence\Mongo;

use MongoDB\Driver\Manager;
use Whirlwind\Infrastructure\Persistence\ConnectionInterface;
use Whirlwind\Infrastructure\Persistence\Mongo\Command\MongoCommand;
use Whirlwind\Infrastructure\Persistence\Mongo\Command\MongoCommandFactory;
use Whirlwind\Infrastructure\Persistence\Mongo\Structure\MongoCollection;
use Whirlwind\Infrastructure\Persistence\Mongo\Structure\MongoDatabase;
use Whirlwind\Infrastructure\Persistence\Mongo\Structure\MongoDatabaseFactory;
use Whirlwind\Infrastructure\Persistence\Mongo\Query\MongoQueryBuilderFactory;

class MongoConnection implements ConnectionInterface
{
    protected MongoCommandFactory $commandFactory;

    protected MongoQueryBuilderFactory $queryBuilderFactory;

    protected MongoDatabaseFactory $databaseFactory;

    protected string $dsn;

    protected array $options = [];

    protected array $driverOptions = [];

    protected array $typeMap = [];

    protected $manager;

    protected $defaultDatabaseName;

    protected $databases = [];

    public function __construct(
        MongoCommandFactory $commandFactory,
        MongoQueryBuilderFactory $queryBuilderFactory,
        MongoDatabaseFactory $databaseFactory,
        string $dsn,
        array $options = [],
        array $driverOptions = []
    ) {
        $this->commandFactory = $commandFactory;
        $this->queryBuilderFactory = $queryBuilderFactory;
        $this->databaseFactory = $databaseFactory;
        $this->dsn = $dsn;
        $this->options = $options;
        $this->driverOptions = $driverOptions;
    }

    public function open()
    {
        if (!($this->manager instanceof Manager)) {
            $this->manager = new Manager($this->dsn, $this->options, $this->driverOptions);
            $this->manager->selectServer($this->manager->getReadPreference());
            $this->typeMap = \array_merge(
                $this->typeMap,
                [
                    'root' => 'array',
                    'document' => 'array'
                ]
            );
        }
    }

    public function close(): void
    {
        if ($this->manager !== null) {
            $this->manager = null;
        }
    }

    public function createCommand($document = [], string $databaseName = null): MongoCommand
    {
        return $this->commandFactory->create($this, $databaseName, $document);
    }

    public function getManager(): Manager
    {
        $this->open();
        return $this->manager;
    }

    public function getTypeMap(): array
    {
        return $this->typeMap;
    }

    public function getQueryBuilder()
    {
        return $this->queryBuilderFactory->create($this);
    }

    public function getDefaultDatabaseName()
    {
        if ($this->defaultDatabaseName === null) {
            if (\preg_match('/^mongodb:\\/\\/.+\\/([^?&]+)/s', $this->dsn, $matches)) {
                $this->defaultDatabaseName = $matches[1];
            } else {
                throw new \InvalidArgumentException("Unable to determine default database name from dsn.");
            }
        }
        return $this->defaultDatabaseName;
    }

    public function getCollection($name, $refresh = false): MongoCollection
    {
        if (\is_array($name)) {
            list ($dbName, $collectionName) = $name;
            return $this->getDatabase($dbName)->getCollection($collectionName, $refresh);
        }
        return $this->getDatabase()->getCollection($name, $refresh);
    }

    public function getDatabase($name = null, $refresh = false): MongoDatabase
    {
        if ($name === null) {
            $name = $this->getDefaultDatabaseName();
        }
        if ($refresh || !\array_key_exists($name, $this->databases)) {
            $this->databases[$name] = $this->selectDatabase($name);
        }
        return $this->databases[$name];
    }

    protected function selectDatabase(string $name): MongoDatabase
    {
        return $this->databaseFactory->create($this, $name);
    }
}
