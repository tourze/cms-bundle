<?php

declare(strict_types=1);

namespace CmsBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * CMS Bundle 控制器测试基类.
 */
#[CoversClass(AbstractEasyAdminControllerTestCase::class)]
#[RunTestsInSeparateProcesses]
abstract class AbstractCmsControllerTestCase extends AbstractEasyAdminControllerTestCase
{
    /**
     * 明确使用测试框架内置的 Dashboard 控制器
     *
     * 说明：业务 Bundle 不应包含任何 Dashboard 控制器，但测试需要至少一个
     * 可用的 Dashboard 以生成 EasyAdmin 路由。这里返回 Testing Framework
     * 自带的 Dashboard（非业务 Bundle 代码），避免对业务 Bundle 产生依赖。
     */
    protected function getPreferredDashboardControllerFqcn(): ?string
    {
        return 'SymfonyTestingFramework\\Controller\\Admin\\DashboardController';
    }
}
