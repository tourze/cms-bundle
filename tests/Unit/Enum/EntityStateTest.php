<?php

declare(strict_types=1);

namespace CmsBundle\Tests\Unit\Enum;

use CmsBundle\Enum\EntityState;
use PHPUnit\Framework\TestCase;

class EntityStateTest extends TestCase
{
    public function testEnumValues(): void
    {
        $this->assertSame('draft', EntityState::DRAFT->value);
        $this->assertSame('published', EntityState::PUBLISHED->value);
        $this->assertSame('revoked', EntityState::REVOKED->value);
    }

    public function testGetLabel(): void
    {
        $this->assertSame('草稿', EntityState::DRAFT->getLabel());
        $this->assertSame('已发布', EntityState::PUBLISHED->getLabel());
        $this->assertSame('已撤回', EntityState::REVOKED->getLabel());
    }
}