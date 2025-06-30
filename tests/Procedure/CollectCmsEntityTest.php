<?php

namespace CmsBundle\Tests\Procedure;

use CmsBundle\Procedure\CollectCmsEntity;
use PHPUnit\Framework\TestCase;

/**
 * @covers \CmsBundle\Procedure\CollectCmsEntity
 */
class CollectCmsEntityTest extends TestCase
{
    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(CollectCmsEntity::class));
    }
} 