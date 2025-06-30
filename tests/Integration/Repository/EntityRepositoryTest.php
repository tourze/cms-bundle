<?php

namespace CmsBundle\Tests\Integration\Repository;

use CmsBundle\Entity\Entity;
use CmsBundle\Repository\EntityRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

class EntityRepositoryTest extends TestCase
{
    public function testRepositoryConstruction(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new EntityRepository($registry);
        
        $this->assertInstanceOf(ServiceEntityRepository::class, $repository);
        $this->assertInstanceOf(EntityRepository::class, $repository);
    }
}