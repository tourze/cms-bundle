<?php

declare(strict_types=1);

namespace CmsBundle\Tests;

use CmsBundle\CmsBundle;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;

/**
 * @internal
 */
#[CoversClass(CmsBundle::class)]
#[RunTestsInSeparateProcesses]
final class CmsBundleTest extends AbstractBundleTestCase
{
}
