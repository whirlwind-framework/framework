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

        $this->cache = new \Memcached(($config['persistentId'] ?? '1'));
        foreach ($config['servers'] as $server) {
            if (isset($server['host'], $server['port'], $server['weight'])) {
                $this->cache->addServer(
                    $server['host'],
                    $server['port'],
                    $server['weight']
                );
            }
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
