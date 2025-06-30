<?php

namespace CmsBundle\Tests\Integration\Repository;

use CmsBundle\Entity\Attribute;
use CmsBundle\Repository\AttributeRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

class AttributeRepositoryTest extends TestCase
{
    public function testRepositoryConstruction(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new AttributeRepository($registry);
        
        $this->assertInstanceOf(ServiceEntityRepository::class, $repository);
        $this->assertInstanceOf(AttributeRepository::class, $repository);
    }
}