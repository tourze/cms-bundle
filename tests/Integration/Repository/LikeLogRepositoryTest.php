<?php

namespace CmsBundle\Tests\Integration\Repository;

use CmsBundle\Entity\LikeLog;
use CmsBundle\Repository\LikeLogRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

class LikeLogRepositoryTest extends TestCase
{
    public function testRepositoryConstruction(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new LikeLogRepository($registry);
        
        $this->assertInstanceOf(ServiceEntityRepository::class, $repository);
        $this->assertInstanceOf(LikeLogRepository::class, $repository);
    }
}