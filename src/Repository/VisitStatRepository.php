<?php

namespace CmsBundle\Repository;

use CmsBundle\Entity\VisitStat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method VisitStat|null find($id, $lockMode = null, $lockVersion = null)
 * @method VisitStat|null findOneBy(array<string, mixed> $criteria, array<string, mixed>|null $orderBy = null)
 * @method VisitStat[]    findAll()
 * @method VisitStat[]    findBy(array<string, mixed> $criteria, array<string, mixed>|null $orderBy = null, $limit = null, $offset = null)
 *
 * @extends ServiceEntityRepository<VisitStat>
 */
class VisitStatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VisitStat::class);
    }
}
