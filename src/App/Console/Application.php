<?php

declare(strict_types=1);

namespace Whirlwind\App\Console;

use Psr\Container\ContainerInterface;

class Application
{
    protected ContainerInterface $container;

    protected array $commands = [];

    public function __construct(
        ContainerInterface $container
    ) {
        $this->container = $container;
    }

    public function addCommand(string $route, string $command): void
    {
        if (!\is_subclass_of($command, CommandInterface::class)) {
            throw new \InvalidArgumentException('Command must implement CommandInterface');
        }
        $this->commands[$route] = $command;
    }

    protected function getHandler(string $route): CommandInterface
    {
        if (!isset($this->commands[$route])) {
            throw new \RuntimeException('Invalid route: ' . $route);
        }
        return $this->container->get($this->commands[$route]);
    }

    public function run(Request $request)
    {
        $command = $this->getHandler($request->getRoute());

        if ($command instanceof CommandOptionInterface) {
            $command->setOptions($request->getParams());
        }

        return $command->run($request->getParams());
    }
}
