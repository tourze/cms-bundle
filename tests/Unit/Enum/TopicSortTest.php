<?php

declare(strict_types=1);

namespace CmsBundle\Tests\Unit\Enum;

use CmsBundle\Enum\TopicSort;
use PHPUnit\Framework\TestCase;

class TopicSortTest extends TestCase
{
    public function testEnumValues(): void
    {
        $this->assertSame('id-desc', TopicSort::ID_DESC->value);
        $this->assertSame('id-asc', TopicSort::ID_ASC->value);
        $this->assertSame('random', TopicSort::RANDOM->value);
    }

    public function testGetLabel(): void
    {
        $this->assertSame('ID倒序', TopicSort::ID_DESC->getLabel());
        $this->assertSame('ID顺序', TopicSort::ID_ASC->getLabel());
        $this->assertSame('随机返回', TopicSort::RANDOM->getLabel());
    }
}