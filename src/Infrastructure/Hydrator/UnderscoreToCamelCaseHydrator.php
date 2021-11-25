<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Hydrator;

class UnderscoreToCamelCaseHydrator extends Hydrator
{
    /**
     * @param $target
     * @param array $data
     * @return object
     */
    public function hydrate($target, array $data): object
    {
        $result = [];
        foreach ($data as $name => $value) {
            $result[$this->variablize($name)] = $value;
        }
        return parent::hydrate($target, $result);
    }

    /**
     * @param object $object
     * @param array $fields
     * @return array
     */
    public function extract(object $object, array $fields = []): array
    {
        $result = parent::extract($object, $fields);
        $data = [];
        foreach ($result as $name => $value) {
            $data[$this->underscore($name)] = $value;
        }
        return $data;
    }

    protected function variablize($word, $encoding = 'UTF-8')
    {
        $word = $this->camelize($word, $encoding);

        return \mb_strtolower(\mb_substr($word, 0, 1, $encoding)) . \mb_substr($word, 1, null, $encoding);
    }

    protected function underscore($words, $encoding = 'UTF-8')
    {
        return \mb_strtolower(\preg_replace('/(?<=\\pL)(\\p{Lu})/u', '_\\1', $words), $encoding);
    }

    protected function camelize($word, $encoding = 'UTF-8')
    {
        return \str_replace(' ', '', $this->mbUcwords(\preg_replace('/[^\pL\pN]+/u', ' ', $word), $encoding));
    }

    protected function mbUcfirst($string, $encoding = 'UTF-8')
    {
        $firstChar = \mb_substr($string, 0, 1, $encoding);
        $rest = \mb_substr($string, 1, null, $encoding);

        return \mb_strtoupper($firstChar, $encoding) . $rest;
    }

    protected function mbUcwords($string, $encoding = 'UTF-8')
    {
        $words = \preg_split("/\s/u", $string, -1, PREG_SPLIT_NO_EMPTY);

        $titelized = \array_map(function ($word) use ($encoding) {
            return $this->mbUcfirst($word, $encoding);
        }, $words);

        return \implode(' ', $titelized);
    }
}
