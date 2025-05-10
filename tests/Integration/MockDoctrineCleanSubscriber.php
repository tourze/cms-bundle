<?php

namespace CmsBundle\Tests\Integration;

use Tourze\DoctrineAsyncBundle\EventSubscriber\DoctrineCleanSubscriber;

/**
 * 模拟DoctrineCleanSubscriber
 */
class MockDoctrineCleanSubscriber extends DoctrineCleanSubscriber
{
    /**
     * 记录表记录
     */
    private array $tableRecords = [];
    
    /**
     * 构造函数
     */
    public function __construct()
    {
        // 空的构造函数，不调用父类
    }
    
    /**
     * 添加表记录
     */
    public function addTableRecord(string $tableName, array $params): void
    {
        $this->tableRecords[] = [
            'table' => $tableName,
            'params' => $params,
        ];
    }
    
    /**
     * 获取所有表记录
     */
    public function getTableRecords(): array
    {
        return $this->tableRecords;
    }
    
    /**
     * 清空表记录
     */
    public function reset(): void
    {
        $this->tableRecords = [];
    }
} 