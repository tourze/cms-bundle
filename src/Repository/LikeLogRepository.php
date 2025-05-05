<?php

namespace CmsBundle\Repository;

use CmsBundle\Entity\LikeLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LikeLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method LikeLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method LikeLog[]    findAll()
 * @method LikeLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LikeLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LikeLog::class);
    }
}
