<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Http\User;

use Whirlwind\Infrastructure\Http\User\Exception\UserNotFoundException;

interface UserRepositoryInterface
{
    /**
     * @param $id
     * @return UserInterface
     * @throws UserNotFoundException
     */
    public function findByAuthId($id): UserInterface;

    /**
     * @param string $login
     * @return UserInterface
     * @throws UserNotFoundException
     */
    public function findByUsername(string $login): UserInterface;

    /**
     * @param string $token
     * @return UserInterface
     * @throws UserNotFoundException
     */
    public function findByAccessToken(string $token): UserInterface;
}
