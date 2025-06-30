<?php

namespace CmsBundle\Repository;

use CmsBundle\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array<string, mixed> $criteria, array<string, mixed>|null $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array<string, mixed> $criteria, array<string, mixed>|null $orderBy = null, $limit = null, $offset = null)
 *
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }
}
