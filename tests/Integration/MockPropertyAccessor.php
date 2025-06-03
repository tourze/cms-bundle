<?php

namespace CmsBundle\Tests\Integration;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

/**
 * 模拟属性访问器
 */
class MockPropertyAccessor implements PropertyAccessorInterface
{
    public function __construct()
    {
        // 空构造函数，覆盖父类的构造函数
    }

    /**
     * 获取属性值
     */
    public function getValue(object|array $objectOrArray, string|PropertyPathInterface $propertyPath): mixed
    {
        $path = $propertyPath instanceof PropertyPathInterface ? $propertyPath->__toString() : $propertyPath;
        
        if (is_array($objectOrArray)) {
            return $objectOrArray[$path] ?? null;
        }
        
        if (is_object($objectOrArray)) {
            $reflection = new \ReflectionClass($objectOrArray);
            
            // 尝试直接访问属性
            if ($reflection->hasProperty($path)) {
                $property = $reflection->getProperty($path);
                $property->setAccessible(true);
                return $property->getValue($objectOrArray);
            }
            
            // 尝试 getter 方法
            $getter = 'get' . ucfirst($path);
            if ($reflection->hasMethod($getter)) {
                return $objectOrArray->$getter();
            }
            
            // 尝试 is/has 方法
            $isser = 'is' . ucfirst($path);
            if ($reflection->hasMethod($isser)) {
                return $objectOrArray->$isser();
            }
            
            $hasher = 'has' . ucfirst($path);
            if ($reflection->hasMethod($hasher)) {
                return $objectOrArray->$hasher();
            }
        }
        
        return null;
    }

    /**
     * 设置属性值
     */
    public function setValue(object|array &$objectOrArray, string|PropertyPathInterface $propertyPath, mixed $value): void
    {
        $path = $propertyPath instanceof PropertyPathInterface ? $propertyPath->__toString() : $propertyPath;
        
        if (is_array($objectOrArray)) {
            $objectOrArray[$path] = $value;
            return;
        }
        
        if (is_object($objectOrArray)) {
            $reflection = new \ReflectionClass($objectOrArray);
            
            // 尝试直接设置属性
            if ($reflection->hasProperty($path)) {
                $property = $reflection->getProperty($path);
                $property->setAccessible(true);
                $property->setValue($objectOrArray, $value);
                return;
            }
            
            // 尝试 setter 方法
            $setter = 'set' . ucfirst($path);
            if ($reflection->hasMethod($setter)) {
                $objectOrArray->$setter($value);
                return;
            }
        }
    }

    /**
     * 检查属性是否可读
     */
    public function isReadable(object|array $objectOrArray, string|PropertyPathInterface $propertyPath): bool
    {
        $path = $propertyPath instanceof PropertyPathInterface ? $propertyPath->__toString() : $propertyPath;
        
        if (is_array($objectOrArray)) {
            return array_key_exists($path, $objectOrArray);
        }
        
        if (is_object($objectOrArray)) {
            $reflection = new \ReflectionClass($objectOrArray);
            
            if ($reflection->hasProperty($path)) {
                return true;
            }
            
            $getter = 'get' . ucfirst($path);
            if ($reflection->hasMethod($getter)) {
                return true;
            }
            
            $isser = 'is' . ucfirst($path);
            if ($reflection->hasMethod($isser)) {
                return true;
            }
            
            $hasher = 'has' . ucfirst($path);
            if ($reflection->hasMethod($hasher)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * 检查属性是否可写
     */
    public function isWritable(object|array $objectOrArray, string|PropertyPathInterface $propertyPath): bool
    {
        $path = $propertyPath instanceof PropertyPathInterface ? $propertyPath->__toString() : $propertyPath;
        
        if (is_array($objectOrArray)) {
            return true;
        }
        
        if (is_object($objectOrArray)) {
            $reflection = new \ReflectionClass($objectOrArray);
            
            if ($reflection->hasProperty($path)) {
                return true;
            }
            
            $setter = 'set' . ucfirst($path);
            if ($reflection->hasMethod($setter)) {
                return true;
            }
        }
        
        return false;
    }
} 