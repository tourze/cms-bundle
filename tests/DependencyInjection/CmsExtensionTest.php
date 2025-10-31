<?php

declare(strict_types=1);

namespace CmsBundle\Tests\DependencyInjection;

use CmsBundle\DependencyInjection\CmsExtension;
use PHPUnit\Framework\Attributes\CoversClass;
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
