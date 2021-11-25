<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Repository\Relation;

class Relation
{
    protected $property;

    protected $field;

    protected $relatedField;

    protected $relatedCollection;

    protected $relatedModel;

    protected $relationType;

    public function __construct(
        string $property,
        string $field,
        string $relatedField,
        string $relatedCollection,
        string $relatedModel,
        string $relationType
    ) {
        $this->property = $property;
        $this->field = $field;
        $this->relatedField = $relatedField;
        $this->relatedCollection = $relatedCollection;
        $this->relatedModel = $relatedModel;
        $this->relationType = $relationType;
    }

    public function getProperty(): string
    {
        return $this->property;
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getRelatedField(): string
    {
        return $this->relatedField;
    }

    public function getRelatedCollection(): string
    {
        return $this->relatedCollection;
    }

    public function getRelatedModel(): string
    {
        return $this->relatedModel;
    }

    public function getRelationType(): string
    {
        return $this->relationType;
    }
}
