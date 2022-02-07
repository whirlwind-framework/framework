<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Cache\Provider;

use Memcache;

class MemcacheProvider extends AbstractCacheProvider
{
    public function __construct(array $config = [])
    {
        if (empty($config['servers'])) {
            $config['servers'] = [[
                'host' => '127.0.0.1',
                'port' => 11211,
                'weight' => 1,
            ]];
        }

        $this->cache = new Memcache();

        foreach ($config['servers'] as $server) {
            if (isset($server['host'], $server['port'])) {
                $this->cache->addServer(
                    $server['host'],
                    $server['port'],
                    true,
                    $server['weight'] ?? 1
                );
            }
        }
    }
}
