<?php

namespace CmsBundle\Repository;

use CmsBundle\Entity\SearchLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SearchLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method SearchLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method SearchLog[]    findAll()
 * @method SearchLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SearchLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SearchLog::class);
    }
}
