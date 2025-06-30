<?php

namespace CmsBundle\Repository;

use CmsBundle\Entity\ShareLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ShareLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShareLog|null findOneBy(array<string, mixed> $criteria, array<string, mixed>|null $orderBy = null)
 * @method ShareLog[]    findAll()
 * @method ShareLog[]    findBy(array<string, mixed> $criteria, array<string, mixed>|null $orderBy = null, $limit = null, $offset = null)
 *
 * @extends ServiceEntityRepository<ShareLog>
 */
class ShareLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShareLog::class);
    }
}
