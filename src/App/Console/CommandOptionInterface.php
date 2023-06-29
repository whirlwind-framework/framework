<?php

declare(strict_types=1);

namespace Whirlwind\App\Console;

interface CommandOptionInterface
{
    /**
     * @param array $options
     * @return void
     */
    public function setOptions(array $options = []): void;

    /**
     * @param string $name
     * @param $default
     * @return mixed
     */
    public function getOption(string $name, $default = null);
}
