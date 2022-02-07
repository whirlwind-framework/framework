<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Cache\Provider;

use Memcache;
use Whirlwind\Infrastructure\Cache\ProviderInterface;

class MemcacheProvider extends AbstractCacheProvider
{
    private const DEFAULT_SERVER_CONFIG = [
        'host' => '127.0.0.1',
        'port' => 11211,
        'persistent' => true,
        'weight' => 0,
    ];

    public function __construct(array $config = [])
    {
        if (empty($config['servers'])) {
            $config['servers'] = [static::DEFAULT_SERVER_CONFIG];
        }

        $this->cache = new Memcache();

        foreach ($config['servers'] as $server) {
            $this->cache->addServer(
                $server['host'] ?? static::DEFAULT_SERVER_CONFIG['host'],
                $server['port'] ?? static::DEFAULT_SERVER_CONFIG['port'],
                $server['persistent'] ?? static::DEFAULT_SERVER_CONFIG['persistent'],
                $server['weight'] ?? static::DEFAULT_SERVER_CONFIG['weight']
            );
        }
    }
}
