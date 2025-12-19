<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\CmsBundle\Service\AttributeControllerLoader;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(AttributeControllerLoader::class)]
#[RunTestsInSeparateProcesses]
final class AttributeControllerLoaderTest extends AbstractIntegrationTestCase
{
    private AttributeControllerLoader $loader;

    public function testSupportsReturnsFalse(): void
    {
        $this->assertFalse($this->loader->supports('test', 'test'));
        $this->assertFalse($this->loader->supports(null, null));
    }

    public function testLoadReturnsRouteCollection(): void
    {
        $collection = $this->loader->load('test');
        $this->assertNotNull($collection);
        $this->assertGreaterThanOrEqual(0, $collection->count());
    }

    public function testAutoloadReturnsRouteCollection(): void
    {
        $collection = $this->loader->autoload();
        $this->assertNotNull($collection);
        $this->assertGreaterThanOrEqual(0, $collection->count());
    }

    protected function onSetUp(): void
    {
        $this->loader = self::getService(AttributeControllerLoader::class);
    }
}
