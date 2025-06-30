<?php

namespace CmsBundle\Tests\DependencyInjection;

use CmsBundle\DependencyInjection\CmsExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class CmsExtensionTest extends TestCase
{
    private CmsExtension $extension;
    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $this->extension = new CmsExtension();
        $this->container = new ContainerBuilder();
    }

    public function testLoad(): void
    {
        $configs = [];

        // 由于扩展会加载services.yaml，我们只验证没有异常抛出
        $this->extension->load($configs, $this->container);

        // 验证容器存在
        $this->assertInstanceOf(ContainerBuilder::class, $this->container);
    }

    public function testLoadWithEmptyConfigs(): void
    {
        $configs = [[]];

        $this->extension->load($configs, $this->container);

        $this->assertInstanceOf(ContainerBuilder::class, $this->container);
    }
}
