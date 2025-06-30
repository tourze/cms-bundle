<?php

declare(strict_types=1);

namespace CmsBundle\Tests\Unit;

use CmsBundle\CmsBundle;
use PHPUnit\Framework\TestCase;

class CmsBundleTest extends TestCase
{
    public function testBundleInstantiation(): void
    {
        $bundle = new CmsBundle();
        
        $this->assertInstanceOf(CmsBundle::class, $bundle);
    }
}