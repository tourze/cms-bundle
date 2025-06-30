<?php

namespace CmsBundle\Repository;

use CmsBundle\Entity\Value;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Value|null find($id, $lockMode = null, $lockVersion = null)
 * @method Value|null findOneBy(array<string, mixed> $criteria, array<string, mixed>|null $orderBy = null)
 * @method Value[]    findAll()
 * @method Value[]    findBy(array<string, mixed> $criteria, array<string, mixed>|null $orderBy = null, $limit = null, $offset = null)
 *
 * @extends ServiceEntityRepository<Value>
 */
class ValueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Value::class);
    }
}
