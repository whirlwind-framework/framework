<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Cache\Provider;

use Whirlwind\Infrastructure\Cache\ProviderInterface;

abstract class AbstractCacheProvider implements ProviderInterface
{
    protected $cache;

    public function get($key, $default = null)
    {
        $data = $this->cache->get($key);
        if (false === $data) {
            $data = $default;
        }
        return $data;
    }

    public function set($key, $value, $ttl = null)
    {
        return $this->cache->set($key, $value, 0, $ttl);
    }

    public function delete($key)
    {
        return $this->cache->delete($key);
    }

    public function clear()
    {
        return $this->cache->flush();
    }

    public function getMultiple($keys, $default = null)
    {
        return $this->cache->get($keys);
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
        return $value !== false; // TODO: null value returns true
    }

    public function add($key, $value, $ttl = null)
    {
        $this->cache->add($key, $value, 0, $ttl);
    }

    public function increment($key)
    {
        return $this->cache->increment($key);
    }
}
