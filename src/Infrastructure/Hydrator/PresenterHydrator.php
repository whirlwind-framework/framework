<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Hydrator;

use Whirlwind\Infrastructure\Hydrator\Extractor\ExtractorInterface;

class PresenterHydrator extends Hydrator
{
    /**
     * @var ExtractorInterface[]
     */
    protected array $extractors = [];

    public function addExtractor(ExtractorInterface $extractor): void
    {
        $this->extractors[] = $extractor;
    }

    public function extract(object $object, array $fields = []): array
    {
        $reflection = $this->getReflectionClass(\get_class($object));
        $result = [];
        if ($fields === []) {
            $fields = $this->accessor->getPropertyNames($object, $reflection);
        }
        foreach ($fields as $name) {
            $result[$name] = $this->accessor->get($object, $reflection, $name);
            if (isset($this->strategies[$name])) {
                $result[$name] = $this->strategies[$name]->extract($result[$name], $object);
            } elseif (\is_iterable($result[$name])) {
                $result[$name] = $this->extractIterable($result[$name]);
            } elseif (\is_object($result[$name])) {
                $result[$name] = $this->extractObject($result[$name]);
            }
        }
        return $result;
    }

    public function getReflectionClass($target)
    {
        $className = \is_object($target) ? \get_class($target) : $target;
        if (!isset($this->reflectionClassMap[$className])) {
            $this->reflectionClassMap[$className] = new \ReflectionClass($className);
        }
        return $this->reflectionClassMap[$className];
    }

    private function extractIterable(iterable $items): array
    {
        $result = [];
        foreach ($items as $key => $value) {
            if (\is_iterable($value)) {
                $result[$key] = $this->extractIterable($value);
            } elseif (\is_object($value)) {
                $result[$key] = $this->extractObject($value);
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    protected function extractObject(object $object)
    {
        foreach ($this->extractors as $extractor) {
            if ($extractor->isExtractable($object)) {
                return $extractor->extract($object);
            }
        }

        return $this->extract($object);
    }
}
