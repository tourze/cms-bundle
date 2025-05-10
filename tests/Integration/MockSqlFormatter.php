<?php

namespace CmsBundle\Tests\Integration;

use Doctrine\Persistence\ObjectManager;
use Tourze\DoctrineEntityCheckerBundle\Service\SqlFormatter;

/**
 * 模拟SQL格式化器
 */
class MockSqlFormatter extends SqlFormatter
{
    /**
     * 获取对象的插入SQL
     */
    public function getObjectInsertSql(ObjectManager $objectManager, object $object): array
    {
        // 简单地返回一个假的表名和参数
        $className = get_class($object);
        $tableName = strtolower(substr($className, strrpos($className, '\\') + 1));
        
        $params = [
            'id' => method_exists($object, 'getId') ? $object->getId() : null,
        ];
        
        return [$tableName, $params];
    }
}
 