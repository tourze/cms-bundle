<?php

declare(strict_types=1);

namespace CmsBundle\Tests\Integration\Controller;

use CmsBundle\Controller\SqlController;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SqlControllerTest extends TestCase
{
    public function testControllerInstanceOf(): void
    {
        $controller = new SqlController();

        $this->assertInstanceOf(AbstractController::class, $controller);
    }

    public function testControllerRouteAttribute(): void
    {
        $reflection = new \ReflectionClass(SqlController::class);
        $method = $reflection->getMethod('__invoke');
        $attributes = $method->getAttributes();
        
        $this->assertNotEmpty($attributes);
    }
}