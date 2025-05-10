<?php

namespace CmsBundle\Tests\Integration;

use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;

/**
 * 模拟LinkGenerator
 */
class MockLinkGenerator implements LinkGeneratorInterface
{
    /**
     * 生成链接
     */
    public function generatePath(string $routeName, array $parameters = []): string
    {
        return '/' . $routeName . '?' . http_build_query($parameters);
    }
    
    /**
     * 获取CRUD列表页
     */
    public function getCurdListPage(string $entityFqcn): string
    {
        $parts = explode('\\', $entityFqcn);
        $className = end($parts);
        return strtolower($className) . '_list';
    }
    
    /**
     * 从URL提取实体类名
     */
    public function extractEntityFqcn(string $url): ?string
    {
        // 模拟从URL提取实体类名
        return null;
    }
} 