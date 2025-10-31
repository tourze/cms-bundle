<?php

declare(strict_types=1);

namespace CmsBundle\Service;

use CmsBundle\Repository\ValueRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

/**
 * Value 服务类.
 *
 * 为其他模块提供 Value 查询功能，避免直接调用 Repository
 */
#[Autoconfigure(public: true)]
readonly class ValueService
{
    public function __construct(
        private ValueRepository $valueRepository,
    ) {
    }

    /**
     * 构建搜索子查询.
     *
     * @param array<int> $searchableAttributes
     */
    public function buildSearchSubQuery(array $searchableAttributes, string $keyword): QueryBuilder
    {
        $subQuery = $this->valueRepository->createQueryBuilder('v');
        $subQuery->select('IDENTITY(v.entity)');
        $subQuery->where($subQuery->expr()->in('v.attribute', $searchableAttributes));
        $subQuery->andWhere(
            $subQuery->expr()->like(
                'v.data',
                $subQuery->expr()->literal("%{$keyword}%")
            )
        );

        return $subQuery;
    }

    /**
     * 创建查询构建器 - 内部使用.
     *
     * @internal 仅供内部使用，不建议在Service层之外调用
     */
    public function createQueryBuilder(string $alias): QueryBuilder
    {
        return $this->valueRepository->createQueryBuilder($alias);
    }
}
