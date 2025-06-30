<?php

namespace CmsBundle\Tests\Procedure\Category;

use CmsBundle\Procedure\Category\AdminGetCmsCategoryList;
use PHPUnit\Framework\TestCase;

/**
 * @covers \CmsBundle\Procedure\Category\AdminGetCmsCategoryList
 */
class AdminGetCmsCategoryListTest extends TestCase
{
    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(AdminGetCmsCategoryList::class));
    }
} 