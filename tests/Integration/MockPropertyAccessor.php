<?php

namespace CmsBundle\Tests\Integration;

use Symfony\Component\PropertyAccess\Exception\AccessException;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

/**
 * 模拟属性访问器
 */
class MockPropertyAccessor implements PropertyAccessorInterface
{
    /**
     * 获取属性值
     */
    public function getValue(object|array $objectOrArray, string|PropertyPathInterface $propertyPath): mixed
    {
        try {
            // 转换属性路径为字符串
            $path = $propertyPath instanceof PropertyPathInterface ? $propertyPath->__toString() : $propertyPath;
            
            if (is_array($objectOrArray)) {
                if (isset($objectOrArray[$path])) {
                    return $objectOrArray[$path];
                }
                
                throw new NoSuchPropertyException(sprintf('数组中不存在键 "%s"', $path));
            }
            
            $getter = 'get' . ucfirst($path);
            if (method_exists($objectOrArray, $getter)) {
                return $objectOrArray->$getter();
            }
            
            $isser = 'is' . ucfirst($path);
            if (method_exists($objectOrArray, $isser)) {
                return $objectOrArray->$isser();
            }
            
            $hasser = 'has' . ucfirst($path);
            if (method_exists($objectOrArray, $hasser)) {
                return $objectOrArray->$hasser();
            }
            
            if (property_exists($objectOrArray, $path)) {
                return $objectOrArray->$path;
            }
            
            throw new NoSuchPropertyException(sprintf('对象不存在属性 "%s"', $path));
        } catch (\Throwable $e) {
            throw new AccessException(sprintf('无法读取属性 "%s"', $propertyPath), 0, $e);
        }
    }
    
    /**
     * 设置属性值
     */
    public function setValue(object|array &$objectOrArray, string|PropertyPathInterface $propertyPath, mixed $value): void
    {
        try {
            // 转换属性路径为字符串
            $path = $propertyPath instanceof PropertyPathInterface ? $propertyPath->__toString() : $propertyPath;
            
            if (is_array($objectOrArray)) {
                $objectOrArray[$path] = $value;
                return;
            }
            
            $setter = 'set' . ucfirst($path);
            if (method_exists($objectOrArray, $setter)) {
                $objectOrArray->$setter($value);
                return;
            }
            
            if (property_exists($objectOrArray, $path)) {
                $objectOrArray->$path = $value;
                return;
            }
            
            throw new NoSuchPropertyException(sprintf('对象不存在属性 "%s"', $path));
        } catch (\Throwable $e) {
            throw new AccessException(sprintf('无法写入属性 "%s"', $propertyPath), 0, $e);
        }
    }
    
    /**
     * 检查属性是否可读
     */
    public function isReadable(object|array $objectOrArray, string|PropertyPathInterface $propertyPath): bool
    {
        // 转换属性路径为字符串
        $path = $propertyPath instanceof PropertyPathInterface ? $propertyPath->__toString() : $propertyPath;
        
        if (is_array($objectOrArray)) {
            return isset($objectOrArray[$path]);
        }
        
        $getter = 'get' . ucfirst($path);
        if (method_exists($objectOrArray, $getter)) {
            return true;
        }
        
        $isser = 'is' . ucfirst($path);
        if (method_exists($objectOrArray, $isser)) {
            return true;
        }
        
        $hasser = 'has' . ucfirst($path);
        if (method_exists($objectOrArray, $hasser)) {
            return true;
        }
        
        return property_exists($objectOrArray, $path);
    }
    
    /**
     * 检查属性是否可写
     */
    public function isWritable(object|array $objectOrArray, string|PropertyPathInterface $propertyPath): bool
    {
        // 转换属性路径为字符串
        $path = $propertyPath instanceof PropertyPathInterface ? $propertyPath->__toString() : $propertyPath;
        
        if (is_array($objectOrArray)) {
            return true;
        }
        
        $setter = 'set' . ucfirst($path);
        if (method_exists($objectOrArray, $setter)) {
            return true;
        }
        
        return property_exists($objectOrArray, $path);
    }
} 