<?php

namespace CmsBundle\Tests\Integration\Repository;

use CmsBundle\Entity\ShareLog;
use CmsBundle\Repository\ShareLogRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

class ShareLogRepositoryTest extends TestCase
{
    public function testRepositoryConstruction(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new ShareLogRepository($registry);
        
        $this->assertInstanceOf(ServiceEntityRepository::class, $repository);
        $this->assertInstanceOf(ShareLogRepository::class, $repository);
    }
}