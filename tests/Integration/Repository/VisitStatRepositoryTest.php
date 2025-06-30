<?php

namespace CmsBundle\Tests\Integration\Repository;

use CmsBundle\Entity\VisitStat;
use CmsBundle\Repository\VisitStatRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

class VisitStatRepositoryTest extends TestCase
{
    public function testRepositoryConstruction(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new VisitStatRepository($registry);
        
        $this->assertInstanceOf(ServiceEntityRepository::class, $repository);
        $this->assertInstanceOf(VisitStatRepository::class, $repository);
    }
}