<?php declare(strict_types=1);

namespace Whirlwind\App\Console;

class Request
{
    protected $route;

    protected $params;

    public function __construct()
    {
        $rawParams = $this->getServerParams();
        $endOfOptionsFound = false;
        if (isset($rawParams[0])) {
            $route = \array_shift($rawParams);
            if ($route === '--') {
                $endOfOptionsFound = true;
                $route = \array_shift($rawParams);
            }
        } else {
            $route = '';
        }
        $params = [];
        $prevOption = null;
        foreach ($rawParams as $param) {
            if ($endOfOptionsFound) {
                $params[] = $param;
            } elseif ($param === '--') {
                $endOfOptionsFound = true;
            } elseif (\preg_match('/^--([\w-]+)(?:=(.*))?$/', $param, $matches)) {
                $name = $matches[1];
                if (\is_numeric(\substr($name, 0, 1))) {
                    throw new \Exception('Parameter "' . $name . '" is not valid');
                }
            } elseif (\preg_match('/^-([\w-]+)(?:=(.*))?$/', $param, $matches)) {
                $name = $matches[1];
                if (\is_numeric($name)) {
                    $params[] = $param;
                } else {
                    $params['_aliases'][$name] = isset($matches[2]) ? $matches[2] : true;
                    $prevOption = &$params['_aliases'][$name];
                }
            } elseif ($prevOption === true) {
                $prevOption = $param;
            } else {
                $params[] = $param;
            }
        }
        $this->route = $route;
        $this->params = $params;
    }

    public function getServerParams()
    {
        $params = [];
        if (isset($_SERVER['argv'])) {
            $this->_params = $_SERVER['argv'];
            \array_shift($this->_params);
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
