<?php

namespace CmsBundle\Tests\Integration;

use Psr\SimpleCache\CacheInterface;

/**
 * 模拟SimpleCache实现
 */
class MockSimpleCache implements CacheInterface
{
    /**
     * 内存中缓存的数据
     */
    private array $cache = [];
    
    /**
     * 缓存过期时间
     */
    private array $expires = [];

    /**
     * 获取缓存项
     */
    public function get(string $key, mixed $default = null): mixed
    {
        if (!$this->has($key)) {
            return $default;
        }
        
        return $this->cache[$key];
    }

    /**
     * 设置缓存项
     */
    public function set(string $key, mixed $value, null|int|\DateInterval $ttl = null): bool
    {
        $this->cache[$key] = $value;
        
        if ($ttl !== null) {
            $this->expires[$key] = time() + (is_int($ttl) ? $ttl : $ttl->s);
        } else {
            $this->expires[$key] = null;
        }
        
        return true;
    }

    /**
     * 删除缓存项
     */
    public function delete(string $key): bool
    {
        if (isset($this->cache[$key])) {
            unset($this->cache[$key]);
            unset($this->expires[$key]);
            return true;
        }
        
        return false;
    }

    /**
     * 清除所有缓存
     */
    public function clear(): bool
    {
        $this->cache = [];
        $this->expires = [];
        return true;
    }

    /**
     * 获取多个缓存项
     */
    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->get($key, $default);
        }
        
        return $result;
    }

    /**
     * 设置多个缓存项
     */
    public function setMultiple(iterable $values, null|int|\DateInterval $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value, $ttl);
        }
        
        return true;
    }

    /**
     * 删除多个缓存项
     */
    public function deleteMultiple(iterable $keys): bool
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }
        
        return true;
    }

    /**
     * 检查缓存项是否存在
     */
    public function has(string $key): bool
    {
        if (!isset($this->cache[$key])) {
            return false;
        }
        
        // 检查是否过期
        if (isset($this->expires[$key]) && $this->expires[$key] !== null && time() > $this->expires[$key]) {
            $this->delete($key);
            return false;
        }
        
        return true;
    }
} 