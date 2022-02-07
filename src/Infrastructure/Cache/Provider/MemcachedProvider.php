<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Cache\Provider;

class MemcachedProvider extends AbstractCacheProvider
{
    private const DEFAULT_SERVER_CONFIG = [
        'host' => '127.0.0.1',
        'port' => 11211,
        'weight' => 0
    ];

    public function __construct(array $config = [])
    {
        if (empty($config['servers'])) {
            $config['servers'] = [static::DEFAULT_SERVER_CONFIG];
        }

        $this->cache = new \Memcached(($server['persistentId'] ?? '1'));
        foreach ($config['servers'] as $server) {
            $this->cache->addServer(
                $server['host'] ?? static::DEFAULT_SERVER_CONFIG['host'],
                $server['port'] ?? static::DEFAULT_SERVER_CONFIG['port'],
                $server['weight'] ?? static::DEFAULT_SERVER_CONFIG['weight']
            );
        }
    }

    public function has($key)
    {
        $this->cache->get($key);

        return \Memcached::RES_NOTFOUND !== $this->cache->getResultCode();
    }

    public function set($key, $value, $ttl = null)
    {
        return $this->cache->set($key, $value, $ttl);
    }
}
