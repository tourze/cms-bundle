<?php

declare(strict_types=1);

namespace CmsBundle\Service;

use CmsBundle\Entity\Model;
use Doctrine\ORM\QueryBuilder;

readonly class ContentService
{
    public function __construct(
        private ValueService $valueService,
        private ModelService $modelService,
    ) {
    }

    /**
     * 搜索指定模型的关键词数据列表.
     */
    public function searchByKeyword(QueryBuilder $queryBuilder, string $keyword, ?Model $model = null): void
    {
        $models = $this->resolveModels($model);
        $searchableAttributes = $this->extractSearchableAttributes($models);

        if ([] === $searchableAttributes) {
            return;
        }

        $subQuery = $this->buildSearchSubQuery($searchableAttributes, $keyword);
        $queryBuilder->andWhere($queryBuilder->expr()->in('a.id', $subQuery->getDQL()));
    }

    /**
     * @return array<Model>
     */
    private function resolveModels(?Model $model): array
    {
        return null === $model ? $this->modelService->findAllValidModels() : [$model];
    }

    /**
     * @param array<Model> $models
     *
     * @return array<int>
     */
    private function extractSearchableAttributes(array $models): array
    {
        $searchableAttributes = [];
        foreach ($models as $mdl) {
            foreach ($mdl->getAttributes() as $attribute) {
                if ($attribute->getSearchable() ?? false) {
                    $attributeId = $attribute->getId();
                    if (null !== $attributeId) {
                        $searchableAttributes[] = $attributeId;
                    }
                }
            }
        }

        return $searchableAttributes;
    }

    /**
     * @param array<int> $searchableAttributes
     */
    private function buildSearchSubQuery(array $searchableAttributes, string $keyword): QueryBuilder
    {
        return $this->valueService->buildSearchSubQuery($searchableAttributes, $keyword);
    }
}
