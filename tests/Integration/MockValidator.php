<?php

namespace CmsBundle\Tests\Integration;

use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Mapping\MetadataInterface;
use Symfony\Component\Validator\Validator\ContextualValidatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * 模拟验证器
 */
class MockValidator implements ValidatorInterface
{
    /**
     * 验证对象
     */
    public function validate($value, $constraints = null, $groups = null): ConstraintViolationListInterface
    {
        // 测试环境下，总是返回空的验证错误列表
        return new ConstraintViolationList();
    }
    
    /**
     * 验证对象的属性
     */
    public function validateProperty(object $object, string $propertyName, $groups = null): ConstraintViolationListInterface
    {
        // 测试环境下，总是返回空的验证错误列表
        return new ConstraintViolationList();
    }
    
    /**
     * 验证属性值
     */
    public function validatePropertyValue(object|string $objectOrClass, string $propertyName, $value, $groups = null): ConstraintViolationListInterface
    {
        // 测试环境下，总是返回空的验证错误列表
        return new ConstraintViolationList();
    }
    
    /**
     * 启动验证
     */
    public function startContext(): ContextualValidatorInterface
    {
        // 返回一个简单的ContextualValidatorInterface实现
        return new MockContextualValidator();
    }
    
    /**
     * 在上下文中验证
     */
    public function inContext(ExecutionContextInterface $context): ContextualValidatorInterface
    {
        // 返回一个简单的ContextualValidatorInterface实现
        return new MockContextualValidator();
    }
    
    /**
     * 获取元数据工厂
     */
    public function getMetadataFor($value): MetadataInterface
    {
        throw new \RuntimeException('未在测试中实现的方法: ' . __METHOD__);
    }
    
    /**
     * 是否有元数据
     */
    public function hasMetadataFor($value): bool
    {
        return false;
    }
} 