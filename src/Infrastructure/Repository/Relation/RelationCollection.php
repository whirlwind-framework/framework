<?php declare(strict_types=1);

namespace Whirlwind\Infrastructure\Repository\Relation;

class RelationCollection
{
    protected $relations = [];

    public function __construct(Relation ...$relations)
    {
        $this->relations = $relations;
    }

    public function addRelation(Relation $relation): void
    {
        $this->relations[] = $relation;
    }

    public function getRelationByProperty(string $property): ?Relation
    {
        foreach ($this->relations as $relation) {
            if ($relation->getProperty() == $property) {
                return $relation;
            }
        }
    }
}
