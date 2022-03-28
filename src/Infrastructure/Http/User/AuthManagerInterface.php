<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Http\User;

interface AuthManagerInterface
{
    /**
     * @return bool
     */
    public function isAuthenticated(): bool;

    /**
     * @return bool
     */
    public function isGuest(): bool;

    /**
     * @return UserInterface|null
     */
    public function getUser(): ?UserInterface;

    /**
     * @return mixed
     */
    public function getId();
    /**
     * @return bool
     */
    public function hasUser(): bool;

    /**
     * @param UserInterface|null $user
     * @return void
     */
    public function setUser(?UserInterface $user): void;
}
