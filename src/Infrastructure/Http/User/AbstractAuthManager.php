<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Http\User;

abstract class AbstractAuthManager implements AuthManagerInterface
{
    protected ?UserInterface $user = null;
    protected UserRepositoryInterface $repository;

    /**
     * @param UserRepositoryInterface $repository
     */
    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return bool
     */
    public function isAuthenticated(): bool
    {
        return null !== $this->getUser();
    }

    /**
     * @return bool
     */
    public function isGuest(): bool
    {
        return !$this->isAuthenticated();
    }

    /**
     * @return mixed|null
     */
    public function getId()
    {
        $user = $this->getUser();

        return $user ? $user->getAuthId() : null;
    }

    /**
     * @return bool
     */
    public function hasUser(): bool
    {
        return null !== $this->user;
    }

    /**
     * @param UserInterface|null $user
     * @return void
     */
    public function setUser(?UserInterface $user): void
    {
        $this->user = $user;
    }
}
