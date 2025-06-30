<?php

namespace CmsBundle\Tests\Procedure;

use CmsBundle\Procedure\GetCmsEntityList;
use PHPUnit\Framework\TestCase;

/**
 * @covers \CmsBundle\Procedure\GetCmsEntityList
 */
class GetCmsEntityListTest extends TestCase
{
    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(GetCmsEntityList::class));
    }
} 