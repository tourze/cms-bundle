<?php

namespace CmsBundle\Service;

use CmsBundle\Entity\Model;
use CmsBundle\Repository\ModelRepository;
use CmsBundle\Repository\ValueRepository;
use Doctrine\ORM\QueryBuilder;

class ContentService
{
    public function __construct(
        private readonly ValueRepository $valueRepository,
        private readonly ModelRepository $modelRepository,
    ) {
    }

    /**
     * 搜索指定模型的关键词数据列表
     */
    public function searchByKeyword(QueryBuilder $queryBuilder, string $keyword, ?Model $model = null): void
    {
        if (!$model) {
            $models = $this->modelRepository->findBy(['valid' => true]);
        } else {
            $models = [$model];
        }

        $searchableAttributes = [];
        foreach ($models as $model) {
            foreach ($model->getAttributes() as $attribute) {
                if (!$attribute->getSearchable()) {
                    continue;
                }

                $searchableAttributes[] = $attribute->getId();
            }
        }

        if (empty($searchableAttributes)) {
            return;
        }

        // SELECT IDENTITY(v.entity) FROM Value AS v WHERE v.attribute in [:attribute] AND v.data LIKE '%TEST%'
        $subQuery = $this->valueRepository->createQueryBuilder('v');
        $subQuery->select('IDENTITY(v.entity)');
        $subQuery->Where(
            $subQuery->expr()->in(
                'v.attribute',
                $searchableAttributes
            )
        );
        $subQuery->andWhere(
            $subQuery->expr()->like(
                'v.data',
                $subQuery->expr()->literal("%{$keyword}%")
            )
        );

        // 补充查询
        $queryBuilder->andWhere($queryBuilder->expr()->in('a.id', $subQuery->getDQL()));
    }
}
