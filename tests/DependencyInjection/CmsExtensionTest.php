<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\CmsBundle\DependencyInjection\CmsExtension;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;

/**
 * @internal
 */
#[CoversClass(CmsExtension::class)]
final class CmsExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    protected function getExtensionClass(): string
    {
        return CmsExtension::class;
    }
}
