<?php

namespace CmsBundle\Tests\Procedure\Category;

use CmsBundle\Procedure\Category\AdminGetCmsCategoryTree;
use PHPUnit\Framework\TestCase;

/**
 * @covers \CmsBundle\Procedure\Category\AdminGetCmsCategoryTree
 */
class AdminGetCmsCategoryTreeTest extends TestCase
{
    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(AdminGetCmsCategoryTree::class));
    }
} 