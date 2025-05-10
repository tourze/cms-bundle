<?php

namespace CmsBundle\Tests\Integration;

use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ContextualValidatorInterface;

/**
 * 模拟上下文验证器
 */
class MockContextualValidator implements ContextualValidatorInterface
{
    /**
     * 设置验证路径
     */
    public function atPath(string $path): static
    {
        return $this;
    }
    
    /**
     * 进行验证
     */
    public function validate($value, $constraints = null, $groups = null): static
    {
        return $this;
    }
    
    /**
     * 验证属性
     */
    public function validateProperty(object $object, string $propertyName, $groups = null): static
    {
        return $this;
    }
    
    /**
     * 验证属性值
     */
    public function validatePropertyValue(object|string $objectOrClass, string $propertyName, $value, $groups = null): static
    {
        return $this;
    }
    
    /**
     * 获取违规列表
     */
    public function getViolations(): ConstraintViolationListInterface
    {
        return new ConstraintViolationList();
    }
} 