<?php declare(strict_types=1);

namespace Whirlwind\Infrastructure\Cache\Provider;

use Whirlwind\Infrastructure\Cache\ProviderInterface;

class MemcacheProvider implements ProviderInterface
{
    protected $memcache;

    public function __construct(array $config = [])
    {
        if (empty($config['servers'])) {
            $config['servers'] = [[
                'host' => '127.0.0.1',
                'port' => 11211,
            ]];
        }
        $this->memcache = new \Memcache();
        foreach ($config['servers'] as $server) {
            $this->memcache->addServer(
                $server['host'],
                $server['port'],
                true,
                (isset($server['weight']) ? $server['weight'] : 1)
            );
        }
    }

    public function get($key, $default = null)
    {
        $data = $this->memcache->get($key);
        if (false === $data) {
            $data = $default;
        }
        return $data;
    }

    public function set($key, $value, $ttl = null)
    {
        return $this->memcache->set($key, $value, 0, $ttl);
    }

    public function delete($key)
    {
        return $this->memcache->delete($key);
    }

    public function clear()
    {
        return $this->memcache->flush();
    }

    public function getMultiple($keys, $default = null)
    {
        return $this->memcache->get($keys);
    }

    public function setMultiple($values, $ttl = null)
    {
        $result = true;
        foreach ($values as $key => $value) {
            $set = $this->set($key, $value, $ttl);
            if (!$set) {
                $result = false;
            }
        }
        return $result;
    }

    public function deleteMultiple($keys)
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }
        return true;
    }

    public function has($key)
    {
        $value = $this->get($key);
        return $value !== false;
    }

    public function add($key, $value, $ttl = null)
    {
        $this->memcache->add($key, $value, 0, $ttl);
    }

    public function increment($key)
    {
        return $this->memcache->increment($key);
    }
}
