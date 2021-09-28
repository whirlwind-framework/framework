<?php declare(strict_types=1);

namespace Whirlwind\App\Console;

interface CommandInterface
{
    public function run(array $params = []);
}
