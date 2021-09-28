<?php declare(strict_types=1);

namespace Whirlwind\App\Application;

interface ApplicationServiceProviderInterface
{
    public function register(): void;
}
