<?php declare(strict_types=1);

namespace Whirlwind\App\Router\Adapter;

use League\Route\Route;
use League\Route\Router;
use Whirlwind\App\Router\RouterInterface;

class LeagueRouterAdapter extends Router implements RouterInterface
{
    public function map(string $method, string $path, $handler): Route
    {
        return parent::map($method, $path, $handler);
    }
}
