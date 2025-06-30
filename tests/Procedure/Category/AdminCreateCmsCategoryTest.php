<?php

namespace CmsBundle\Tests\Procedure\Category;

use CmsBundle\Procedure\Category\AdminCreateCmsCategory;
use PHPUnit\Framework\TestCase;

/**
 * @covers \CmsBundle\Procedure\Category\AdminCreateCmsCategory
 */
class AdminCreateCmsCategoryTest extends TestCase
{
    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(AdminCreateCmsCategory::class));
    }
} 