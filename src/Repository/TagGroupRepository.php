<?php

namespace CmsBundle\Repository;

use CmsBundle\Entity\TagGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TagGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method TagGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method TagGroup[]    findAll()
 * @method TagGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TagGroup::class);
    }
}
