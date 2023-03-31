<?php

declare(strict_types=1);

namespace Whirlwind\App\Console;

class Request
{
    protected $route;

    protected $params;

    public function __construct()
    {
        $rawParams = $this->getServerParams();
        $endOfOptionsFound = false;
        $route = '';
        if (isset($rawParams[0])) {
            $route = \array_shift($rawParams);
            if ($route === '--') {
                $endOfOptionsFound = true;
                $route = \array_shift($rawParams);
            }
        }
        $this->route = $route;
        $this->params = $this->extractParams($rawParams, $endOfOptionsFound);
    }

    protected function extractParams(array $rawParams, bool $endOfOptionsFound): array
    {
        $params = [];
        $prevOption = null;
        foreach ($rawParams as $param) {
            if ($endOfOptionsFound) {
                $params[] = $param;
                continue;
            }
            if ($param === '--') {
                $endOfOptionsFound = true;
                continue;
            }
            if (\preg_match('/^--([\w-]+)(?:=(.*))?$/', $param, $matches)) {
                $name = $matches[1];
                if (\is_numeric(\substr($name, 0, 1))) {
                    throw new \Exception('Parameter "' . $name . '" is not valid');
                }
                $params[$name] = $matches[2] ?? true;
                continue;
            }
            if (\preg_match('/^-([\w-]+)(?:=(.*))?$/', $param, $matches)) {
                $name = $matches[1];
                $params[] = $param;
                if (!\is_numeric($name)) {
                    $params['_aliases'][$name] = $matches[2] ?? true;
                    $prevOption = &$params['_aliases'][$name];
                }
                continue;
            }
            if ($prevOption === true) {
                $prevOption = $param;
                continue;
            }
            $params[] = $param;
        }
        return $params;
    }

    public function getServerParams()
    {
        $params = [];
        if (isset($_SERVER['argv'])) {
            $params = $_SERVER['argv'];
            \array_shift($params);
        }
        return $params;
    }

    public function getRoute()
    {
        return $this->route;
    }

    public function getParams(): array
    {
        return $this->params;
    }
}
