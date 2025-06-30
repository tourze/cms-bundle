<?php

namespace CmsBundle\Tests\Integration\Repository;

use CmsBundle\Entity\CollectLog;
use CmsBundle\Repository\CollectLogRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

class CollectLogRepositoryTest extends TestCase
{
    public function testRepositoryConstruction(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new CollectLogRepository($registry);
        
        $this->assertInstanceOf(ServiceEntityRepository::class, $repository);
        $this->assertInstanceOf(CollectLogRepository::class, $repository);
    }
}