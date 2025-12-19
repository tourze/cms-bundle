<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\Tests;

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
}
