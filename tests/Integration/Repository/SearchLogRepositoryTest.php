<?php

namespace CmsBundle\Tests\Integration\Repository;

use CmsBundle\Entity\SearchLog;
use CmsBundle\Repository\SearchLogRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

class SearchLogRepositoryTest extends TestCase
{
    public function testRepositoryConstruction(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new SearchLogRepository($registry);
        
        $this->assertInstanceOf(ServiceEntityRepository::class, $repository);
        $this->assertInstanceOf(SearchLogRepository::class, $repository);
    }
}