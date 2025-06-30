<?php

namespace CmsBundle\Tests\Integration\Repository;

use CmsBundle\Entity\TagGroup;
use CmsBundle\Repository\TagGroupRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

class TagGroupRepositoryTest extends TestCase
{
    public function testRepositoryConstruction(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new TagGroupRepository($registry);
        
        $this->assertInstanceOf(ServiceEntityRepository::class, $repository);
        $this->assertInstanceOf(TagGroupRepository::class, $repository);
    }
}