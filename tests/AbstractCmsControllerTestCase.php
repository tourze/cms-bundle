<?php

declare(strict_types=1);

namespace CmsBundle\Tests;

use CmsBundle\Tests\Fixtures\Controller\Admin\TestDashboardController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * CMS Bundle 控制器测试基类
 * 提供测试专用的 Dashboard 配置.
 */
#[CoversClass(AbstractEasyAdminControllerTestCase::class)]
#[RunTestsInSeparateProcesses]
abstract class AbstractCmsControllerTestCase extends AbstractEasyAdminControllerTestCase
{
    /**
     * 为 CMS Bundle 测试使用专用的 Dashboard 控制器.
     */
    protected function getPreferredDashboardControllerFqcn(): ?string
    {
        return TestDashboardController::class;
    }
}
