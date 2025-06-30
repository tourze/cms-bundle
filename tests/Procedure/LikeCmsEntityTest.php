<?php

namespace CmsBundle\Tests\Procedure;

use CmsBundle\Procedure\LikeCmsEntity;
use PHPUnit\Framework\TestCase;

/**
 * @covers \CmsBundle\Procedure\LikeCmsEntity
 */
class LikeCmsEntityTest extends TestCase
{
    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(LikeCmsEntity::class));
    }
} 