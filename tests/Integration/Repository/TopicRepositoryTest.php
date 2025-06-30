<?php

namespace CmsBundle\Tests\Integration\Repository;

use CmsBundle\Entity\Topic;
use CmsBundle\Repository\TopicRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

class TopicRepositoryTest extends TestCase
{
    public function testRepositoryConstruction(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new TopicRepository($registry);
        
        $this->assertInstanceOf(ServiceEntityRepository::class, $repository);
        $this->assertInstanceOf(TopicRepository::class, $repository);
    }
}