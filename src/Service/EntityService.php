<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\Service;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\CmsBundle\Entity\Entity;
use Tourze\CmsBundle\Entity\Model;
use Tourze\CmsBundle\Enum\EntityState;
use Tourze\CmsBundle\Repository\EntityRepository;

/**
 * 实体服务类.
 *
 * 为其他模块提供实体查询功能，避免直接调用 Repository
 */
#[Autoconfigure(public: true)]
readonly class EntityService
{
    public function __construct(
        private EntityRepository $entityRepository,
    ) {
    }

    /**
     * 通过ID查找实体.
     */
    public function findEntityById(int $id): ?Entity
    {
        return $this->entityRepository->findOneBy(['id' => $id]);
    }

    /**
     * 通过条件查找实体.
     *
     * @param array<string, mixed> $criteria
     */
    public function findEntityBy(array $criteria): ?Entity
    {
        return $this->entityRepository->findOneBy($criteria);
    }

    /**
     * 查找多个实体.
     *
     * @param array<string, mixed>                          $criteria
     * @param array<string, 'ASC'|'asc'|'DESC'|'desc'>|null $orderBy
     *
     * @return Entity[]
     */
    public function findEntitiesBy(array $criteria, ?array $orderBy = null): array
    {
        return $this->entityRepository->findBy($criteria, $orderBy);
    }

    /**
     * 根据模型查找发布的实体列表.
     *
     * @return Entity[]
     */
    public function findPublishedEntitiesByModel(Model $model, int $limit = 20, int $offset = 0): array
    {
        /** @var Entity[] $result */
        $result = $this->entityRepository->createQueryBuilder('a')
            ->where('a.model = :model AND a.state = :state')
            ->setParameter('model', $model)
            ->setParameter('state', EntityState::PUBLISHED)
            ->addOrderBy('a.sortNumber', 'DESC')
            ->addOrderBy('a.id', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return $result;
    }

    /**
     * 创建发布实体的基础查询构建器.
     */
    public function createPublishedEntitiesQueryBuilder(): QueryBuilder
    {
        return $this->entityRepository->createQueryBuilder('a')
            ->where("a.state = 'published'")
            ->addOrderBy('a.sortNumber', 'DESC')
            ->addOrderBy('a.id', 'DESC');
    }

    /**
     * 创建查询构建器.
     *
     * @internal 仅供内部使用，不建议在Service层之外调用
     */
    public function createQueryBuilder(string $alias): QueryBuilder
    {
        return $this->entityRepository->createQueryBuilder($alias);
    }
}
