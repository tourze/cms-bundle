<?php

namespace CmsBundle\Tests\Procedure;

use CmsBundle\Procedure\GetCmsListFormat;
use PHPUnit\Framework\TestCase;

/**
 * @covers \CmsBundle\Procedure\GetCmsListFormat
 */
class GetCmsListFormatTest extends TestCase
{
    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(GetCmsListFormat::class));
    }
} 