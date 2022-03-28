<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Http\User;

interface UserInterface
{
    /**
     * @return mixed
     */
    public function getAuthId();

    /**
     * @return string
     */
    public function getPassword(): string;
}
