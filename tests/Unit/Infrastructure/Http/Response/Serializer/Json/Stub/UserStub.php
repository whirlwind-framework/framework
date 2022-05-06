<?php

declare(strict_types=1);

namespace Test\Unit\Infrastructure\Http\Response\Serializer\Json\Stub;

class UserStub
{
    private $id;

    private $userName;

    private $password;

    public function __construct(string $id, string $userName, string $password)
    {
        $this->id = $id;
        $this->userName = $userName;
        $this->password = $password;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
