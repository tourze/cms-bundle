<?php

namespace CmsBundle\Tests\Procedure\Category;

use CmsBundle\Procedure\Category\GetCmsCategoryList;
use PHPUnit\Framework\TestCase;

/**
 * @covers \CmsBundle\Procedure\Category\GetCmsCategoryList
 */
class GetCmsCategoryListTest extends TestCase
{
    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(GetCmsCategoryList::class));
    }
} 