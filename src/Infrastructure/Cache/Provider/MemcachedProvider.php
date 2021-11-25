<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Cache\Provider;

class MemcachedProvider extends AbstractCacheProvider
{
    public function __construct(array $config = [])
    {
        if (empty($config['servers'])) {
            $config['servers'] = [[
                'host' => '127.0.0.1',
                'port' => 11211,
                'weight' => 0
            ]];
        }

        $this->cache = new \Memcached(($server['persistentId'] ?? 1));
        foreach ($config['servers'] as $server) {
            $this->cache->addServer(
                $server['host'],
                $server['port'],
                $server['weight']
            );
        }
    }
}
