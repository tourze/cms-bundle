<?php

namespace CmsBundle\Twig;

use CmsBundle\Entity\Entity;
use CmsBundle\Enum\EntityState;
use CmsBundle\Exception\ModelNotFoundException;
use CmsBundle\Repository\EntityRepository;
use CmsBundle\Repository\ModelRepository;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

#[Autoconfigure(lazy: true)]
class CmsExtension extends AbstractExtension
{
    public function __construct(
        private readonly ModelRepository $modelRepository,
        private readonly EntityRepository $entityRepository,
    ) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_cms_entity_detail', $this->getCmsEntityDetail(...)),
            new TwigFunction('get_cms_entity_list', $this->getCmsEntityList(...)),
        ];
    }

    /**
     * 获取单个文章的内容
     */
    public function getCmsEntityDetail(string $id): ?Entity
    {
        return $this->entityRepository->findOneBy([
            'id' => $id,
            'state' => EntityState::PUBLISHED,
        ]);
    }

    /**
     * 拉取指定模型的实体列表
     */
    /**
     * @return array<Entity>
     */
    public function getCmsEntityList(string $modelCode, int $limit = 20, int $offset = 0): array
    {
        $model = $this->modelRepository->findOneBy(['code' => $modelCode]);
        if ($model === null) {
            throw new ModelNotFoundException($modelCode);
        }

        $qb = $this->entityRepository->createQueryBuilder('a')
            ->where('a.model = :model AND a.state = :state')
            ->setParameter('model', $model)
            ->setParameter('state', EntityState::PUBLISHED)
            ->addOrderBy('a.sortNumber', Criteria::DESC)
            ->addOrderBy('a.id', Criteria::DESC)
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }
}
