<?php

namespace CmsBundle\Repository;

use CmsBundle\Entity\CollectLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CollectLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method CollectLog|null findOneBy(array<string, mixed> $criteria, array<string, mixed>|null $orderBy = null)
 * @method CollectLog[]    findAll()
 * @method CollectLog[]    findBy(array<string, mixed> $criteria, array<string, mixed>|null $orderBy = null, $limit = null, $offset = null)
 *
 * @extends ServiceEntityRepository<CollectLog>
 */
class CollectLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CollectLog::class);
    }
}
