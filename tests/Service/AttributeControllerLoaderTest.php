<?php

namespace CmsBundle\Tests\Service;

use CmsBundle\Service\AttributeControllerLoader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\RouteCollection;

class AttributeControllerLoaderTest extends TestCase
{
    private AttributeControllerLoader $loader;

    protected function setUp(): void
    {
        $this->loader = new AttributeControllerLoader();
    }

    public function test_supports_returns_false(): void
    {
        $this->assertFalse($this->loader->supports('test', 'test'));
        $this->assertFalse($this->loader->supports(null, null));
    }

    public function test_load_returns_route_collection(): void
    {
        $collection = $this->loader->load('test');
        $this->assertInstanceOf(RouteCollection::class, $collection);
    }

    public function test_autoload_returns_route_collection(): void
    {
        $collection = $this->loader->autoload();
        $this->assertInstanceOf(RouteCollection::class, $collection);
    }
} 