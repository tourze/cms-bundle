<?php

namespace CmsBundle\Tests\Entity;

use CmsBundle\Entity\Model;
use PHPUnit\Framework\TestCase;

/**
 * @covers \CmsBundle\Entity\Model
 */
class ModelTest extends TestCase
{
    public function test_construct(): void
    {
        $model = new Model();
        $this->assertInstanceOf(Model::class, $model);
    }
} 