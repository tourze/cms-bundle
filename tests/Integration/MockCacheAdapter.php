<?php

namespace CmsBundle\Tests\Integration;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * 简化的模拟缓存适配器
 */
class MockCacheAdapter implements CacheItemPoolInterface
{
    private array $cache = [];
    private array $deferred = [];

    public function getItem(string $key): CacheItemInterface
    {
        return new MockCacheItem($key, $this->cache[$key] ?? null, isset($this->cache[$key]));
    }

    public function getItems(array $keys = []): iterable
    {
        $items = [];
        foreach ($keys as $key) {
            $items[$key] = $this->getItem($key);
        }
        return $items;
    }

    public function hasItem(string $key): bool
    {
        return isset($this->cache[$key]);
    }

    public function clear(): bool
    {
        $this->cache = [];
        $this->deferred = [];
        return true;
    }

    public function deleteItem(string $key): bool
    {
        unset($this->cache[$key]);
        unset($this->deferred[$key]);
        return true;
    }

    public function deleteItems(array $keys): bool
    {
        foreach ($keys as $key) {
            $this->deleteItem($key);
        }
        return true;
    }

    public function save(CacheItemInterface $item): bool
    {
        $this->cache[$item->getKey()] = $item->get();
        return true;
    }

    public function saveDeferred(CacheItemInterface $item): bool
    {
        $this->deferred[$item->getKey()] = $item;
        return true;
    }

    public function commit(): bool
    {
        foreach ($this->deferred as $item) {
            $this->save($item);
        }
        $this->deferred = [];
        return true;
    }
}

/**
 * 模拟缓存项
 */
class MockCacheItem implements CacheItemInterface
{
    private mixed $value;
    private bool $hit;
    private ?\DateTimeInterface $expiry = null;

    public function __construct(
        private readonly string $key,
        mixed $value = null,
        bool $hit = false
    ) {
        $this->value = $value;
        $this->hit = $hit;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function get(): mixed
    {
        return $this->hit ? $this->value : null;
    }

    public function isHit(): bool
    {
        return $this->hit;
    }

    public function set(mixed $value): static
    {
        $this->value = $value;
        $this->hit = true;
        return $this;
    }

    public function expiresAt(?\DateTimeInterface $expiration): static
    {
        $this->expiry = $expiration;
        return $this;
    }

    public function expiresAfter(\DateInterval|int|null $time): static
    {
        if ($time === null) {
            $this->expiry = null;
        } elseif ($time instanceof \DateInterval) {
            $this->expiry = (new \DateTime())->add($time);
        } else {
            $this->expiry = (new \DateTime())->modify("+{$time} seconds");
        }
        return $this;
    }
} 