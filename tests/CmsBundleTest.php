<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\CmsBundle\CmsBundle;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;

/**
 * @internal
 */
#[CoversClass(CmsBundle::class)]
#[RunTestsInSeparateProcesses]
final class CmsBundleTest extends AbstractBundleTestCase
{
}
