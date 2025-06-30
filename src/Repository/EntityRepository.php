<?php

namespace CmsBundle\Repository;

use CmsBundle\Entity\Entity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Entity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Entity|null findOneBy(array<string, mixed> $criteria, array<string, mixed>|null $orderBy = null)
 * @method Entity[]    findAll()
 * @method Entity[]    findBy(array<string, mixed> $criteria, array<string, mixed>|null $orderBy = null, $limit = null, $offset = null)
 *
 * @extends ServiceEntityRepository<Entity>
 */
class EntityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Entity::class);
    }
}
