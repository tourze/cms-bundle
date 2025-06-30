<?php

namespace CmsBundle\Tests\Procedure\Category;

use CmsBundle\Procedure\Category\GetCmsCategoryDetail;
use PHPUnit\Framework\TestCase;

/**
 * @covers \CmsBundle\Procedure\Category\GetCmsCategoryDetail
 */
class GetCmsCategoryDetailTest extends TestCase
{
    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(GetCmsCategoryDetail::class));
    }
} 