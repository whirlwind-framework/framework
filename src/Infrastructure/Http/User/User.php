<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Http\User;

final class User implements UserInterface
{
    private string $id;
    private string $password;

    /**
     * @param string $id
     * @param string $password
     */
    public function __construct(string $id, string $password)
    {
        $this->id = $id;
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getAuthId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }
}
