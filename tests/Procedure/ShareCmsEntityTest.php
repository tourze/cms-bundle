<?php

namespace CmsBundle\Tests\Procedure;

use CmsBundle\Procedure\ShareCmsEntity;
use PHPUnit\Framework\TestCase;

/**
 * @covers \CmsBundle\Procedure\ShareCmsEntity
 */
class ShareCmsEntityTest extends TestCase
{
    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(ShareCmsEntity::class));
    }
} 