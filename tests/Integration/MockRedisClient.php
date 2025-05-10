<?php

namespace CmsBundle\Tests\Integration;

/**
 * 模拟Redis客户端
 */
class MockRedisClient
{
    /**
     * 模拟Redis客户端实例
     */
    private static $instance;
    
    /**
     * 内存中存储的数据
     */
    private array $data = [];
    
    /**
     * 获取客户端实例（单例模式）
     */
    public static function getInstance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    /**
     * 设置键值
     */
    public function set($key, $value, $expiration = null): bool
    {
        $this->data[$key] = [
            'value' => $value,
            'expiration' => $expiration ? time() + $expiration : null,
        ];
        
        return true;
    }
    
    /**
     * 获取键值
     */
    public function get($key)
    {
        if (!isset($this->data[$key])) {
            return null;
        }
        
        $item = $this->data[$key];
        
        // 检查是否过期
        if ($item['expiration'] !== null && time() > $item['expiration']) {
            unset($this->data[$key]);
            return null;
        }
        
        return $item['value'];
    }
    
    /**
     * 删除键
     */
    public function del($key): bool
    {
        if (isset($this->data[$key])) {
            unset($this->data[$key]);
            return true;
        }
        
        return false;
    }
    
    /**
     * 存在性检查
     */
    public function exists($key): bool
    {
        return isset($this->data[$key]);
    }
    
    /**
     * 模拟eval调用
     */
    public function eval($script, $numkeys, ...$arguments)
    {
        // 简单模拟，返回成功
        return true;
    }
    
    /**
     * 清空存储的数据
     */
    public function flushAll(): bool
    {
        $this->data = [];
        return true;
    }
} 