<?php declare(strict_types=1);

namespace Whirlwind\Infrastructure\Persistence\Mongo\ConditionBuilder;

class ConditionBuilder
{
    public function build(array $params): array
    {
        $conditions = [];
        foreach ($params as $name => $value) {
            $method = $this->resolveConditionMethodName($name);
            if (\method_exists($this, $method)) {
                $newCondition = $this->$method($value);
            } else {
                $newCondition = $this->buildHashCondition($name, $value);
            }

            if (empty($newCondition)) {
                continue;
            }

            if (empty($conditions)) {
                $conditions = $newCondition;
            } else {
                $conditions = ['and', $conditions, $newCondition];
            }
        }

        return $conditions;
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

    protected function resolveConditionMethodName($fieldName): string
    {
        return 'build' . $this->camelize($fieldName) . 'Condition';
    }

    protected function buildHashCondition(string $name, $value): array
    {
        return [$name => $value];
    }
}
