<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Http\User\Basic;

use Psr\Http\Message\ServerRequestInterface;
use Whirlwind\Domain\Util\EncoderInterface;
use Whirlwind\Infrastructure\Http\User\AbstractAuthManager;
use Whirlwind\Infrastructure\Http\User\Exception\UserNotFoundException;
use Whirlwind\Infrastructure\Http\User\UserInterface;
use Whirlwind\Infrastructure\Http\User\UserRepositoryInterface;

class BasicAuthManager extends AbstractAuthManager
{
    protected ServerRequestInterface $request;
    protected EncoderInterface $encoder;

    /**
     * @param UserRepositoryInterface $repository
     * @param ServerRequestInterface $request
     * @param EncoderInterface $encoder
     */
    public function __construct(
        UserRepositoryInterface $repository,
        ServerRequestInterface $request,
        EncoderInterface $encoder
    ) {
        parent::__construct($repository);

        $this->request = $request;
        $this->encoder = $encoder;
    }

    /**
     * @return UserInterface|null
     */
    public function getUser(): ?UserInterface
    {
        if (null !== $this->user) {
            return $this->user;
        }
        if (empty($this->request->getHeaderLine('PHP_AUTH_USER'))) {
            return $this->user = null;
        }

        try {
            $user = $this->repository->findByUsername($this->request->getHeaderLine('PHP_AUTH_USER'));
            if ($this->encoder->validateHash($this->request->getHeaderLine('PHP_AUTH_PW'), $user->getPassword())) {
                $this->user = $user;
            }
        } catch (UserNotFoundException $e) {
            $this->user = null;
        }

        return $this->user;
    }
}
