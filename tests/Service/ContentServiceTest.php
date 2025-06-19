<?php

namespace CmsBundle\Tests\Service;

use CmsBundle\Entity\Attribute;
use CmsBundle\Entity\Model;
use CmsBundle\Repository\ModelRepository;
use CmsBundle\Repository\ValueRepository;
use CmsBundle\Service\ContentService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\ORM\Query\Expr\Func;
use Doctrine\ORM\Query\Expr\Literal;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ContentServiceTest extends TestCase
{
    private ContentService $contentService;
    private ValueRepository|MockObject $valueRepository;
    private ModelRepository|MockObject $modelRepository;
    private QueryBuilder|MockObject $queryBuilder;

    protected function setUp(): void
    {
        $this->valueRepository = $this->createMock(ValueRepository::class);
        $this->modelRepository = $this->createMock(ModelRepository::class);

        $this->contentService = new ContentService(
            $this->valueRepository,
            $this->modelRepository
        );

        // 创建QueryBuilder实例
        $this->queryBuilder = $this->createMock(QueryBuilder::class);
    }

    /**
     * 测试当没有提供模型时，会搜索所有有效模型的可搜索属性
     */
    public function testSearchByKeyword_withoutModel_searchesAllValidModels(): void
    {
        // 创建属性模拟对象
        $searchableAttribute1 = $this->createMock(Attribute::class);
        $searchableAttribute1->method('getId')->willReturn(1);
        $searchableAttribute1->method('getSearchable')->willReturn(true);

        $nonSearchableAttribute = $this->createMock(Attribute::class);
        $nonSearchableAttribute->method('getId')->willReturn(2);
        $nonSearchableAttribute->method('getSearchable')->willReturn(false);

        $searchableAttribute2 = $this->createMock(Attribute::class);
        $searchableAttribute2->method('getId')->willReturn(3);
        $searchableAttribute2->method('getSearchable')->willReturn(true);

        // 创建模型模拟对象
        $model1 = $this->createMock(Model::class);
        $model1->method('getAttributes')->willReturn(
            new ArrayCollection([$searchableAttribute1, $nonSearchableAttribute])
        );

        $model2 = $this->createMock(Model::class);
        $model2->method('getAttributes')->willReturn(
            new ArrayCollection([$searchableAttribute2])
        );

        // 配置ModelRepository返回所有有效模型
        $this->modelRepository->method('findBy')
            ->with(['valid' => true])
            ->willReturn([$model1, $model2]);

        // 创建子查询构建器
        $subQueryBuilder = $this->createMock(QueryBuilder::class);

        // 创建表达式对象和Func/Comparison/Literal对象
        $expr = $this->createMock(Expr::class);
        $inFunc = $this->createMock(Func::class);
        $likeComparison = $this->createMock(Comparison::class);
        $literal = $this->createMock(Literal::class);

        // 配置子查询构建器方法链
        $subQueryBuilder->method('expr')->willReturn($expr);
        $subQueryBuilder->method('select')->with('IDENTITY(v.entity)')->willReturnSelf();
        $subQueryBuilder->method('where')->willReturnSelf();
        $subQueryBuilder->method('andWhere')->willReturnSelf();
        $subQueryBuilder->method('getDQL')->willReturn('SUBQUERY_DQL');

        // 配置expr对象返回对应类型对象
        $expr->method('in')
            ->with('v.attribute', [1, 3])
            ->willReturn($inFunc);

        $expr->method('like')
            ->willReturn($likeComparison);

        $expr->method('literal')
            ->with('%test%')
            ->willReturn($literal);

        // 配置ValueRepository返回子查询构建器
        $this->valueRepository->method('createQueryBuilder')
            ->with('v')
            ->willReturn($subQueryBuilder);

        // 配置主查询构建器
        $mainExpr = $this->createMock(Expr::class);
        $mainInFunc = $this->createMock(Func::class);

        $mainExpr->method('in')
            ->with('a.id', 'SUBQUERY_DQL')
            ->willReturn($mainInFunc);

        $this->queryBuilder->method('expr')
            ->willReturn($mainExpr);

        $this->queryBuilder->expects($this->once())
            ->method('andWhere')
            ->with($mainInFunc)
            ->willReturnSelf();

        // 调用被测方法
        $this->contentService->searchByKeyword($this->queryBuilder, 'test');
    }

    /**
     * 测试当提供特定模型时，只搜索该模型的可搜索属性
     */
    public function testSearchByKeyword_withSpecificModel_searchesOnlyThatModel(): void
    {
        // 创建属性模拟对象
        $searchableAttribute = $this->createMock(Attribute::class);
        $searchableAttribute->method('getId')->willReturn(1);
        $searchableAttribute->method('getSearchable')->willReturn(true);

        $nonSearchableAttribute = $this->createMock(Attribute::class);
        $nonSearchableAttribute->method('getId')->willReturn(2);
        $nonSearchableAttribute->method('getSearchable')->willReturn(false);

        // 创建模型模拟对象
        $specificModel = $this->createMock(Model::class);
        $specificModel->method('getAttributes')->willReturn(
            new ArrayCollection([$searchableAttribute, $nonSearchableAttribute])
        );

        // 创建子查询构建器
        $subQueryBuilder = $this->createMock(QueryBuilder::class);

        // 创建表达式对象和Func/Comparison/Literal对象
        $expr = $this->createMock(Expr::class);
        $inFunc = $this->createMock(Func::class);
        $likeComparison = $this->createMock(Comparison::class);
        $literal = $this->createMock(Literal::class);

        // 配置子查询构建器方法链
        $subQueryBuilder->method('expr')->willReturn($expr);
        $subQueryBuilder->method('select')->with('IDENTITY(v.entity)')->willReturnSelf();
        $subQueryBuilder->method('where')->willReturnSelf();
        $subQueryBuilder->method('andWhere')->willReturnSelf();
        $subQueryBuilder->method('getDQL')->willReturn('SUBQUERY_DQL');

        // 配置expr对象返回对应类型对象
        $expr->method('in')
            ->with('v.attribute', [1])
            ->willReturn($inFunc);

        $expr->method('like')
            ->willReturn($likeComparison);

        $expr->method('literal')
            ->with('%test%')
            ->willReturn($literal);

        // 配置ValueRepository返回子查询构建器
        $this->valueRepository->method('createQueryBuilder')
            ->with('v')
            ->willReturn($subQueryBuilder);

        // 配置主查询构建器
        $mainExpr = $this->createMock(Expr::class);
        $mainInFunc = $this->createMock(Func::class);

        $mainExpr->method('in')
            ->with('a.id', 'SUBQUERY_DQL')
            ->willReturn($mainInFunc);

        $this->queryBuilder->method('expr')
            ->willReturn($mainExpr);

        $this->queryBuilder->expects($this->once())
            ->method('andWhere')
            ->with($mainInFunc)
            ->willReturnSelf();

        // 调用被测方法
        $this->contentService->searchByKeyword($this->queryBuilder, 'test', $specificModel);
    }

    /**
     * 测试当没有可搜索属性时，不修改查询构建器
     */
    public function testSearchByKeyword_withNoSearchableAttributes_doesNotModifyQueryBuilder(): void
    {
        // 创建属性模拟对象
        $nonSearchableAttribute = $this->createMock(Attribute::class);
        $nonSearchableAttribute->method('getId')->willReturn(2);
        $nonSearchableAttribute->method('getSearchable')->willReturn(false);

        // 创建模型模拟对象
        $model = $this->createMock(Model::class);
        $model->method('getAttributes')->willReturn(
            new ArrayCollection([$nonSearchableAttribute])
        );

        // 配置ModelRepository返回所有有效模型
        $this->modelRepository->method('findBy')
            ->with(['valid' => true])
            ->willReturn([$model]);

        // 确保QueryBuilder的andWhere方法不会被调用
        $this->queryBuilder->expects($this->never())->method('andWhere');

        // 调用被测方法
        $this->contentService->searchByKeyword($this->queryBuilder, 'test');
    }

    /**
     * 测试当关键词包含特殊字符时，查询构建器正确处理
     */
    public function testSearchByKeyword_withSpecialCharacters_escapesProperlyInQuery(): void
    {
        // 创建属性模拟对象
        $searchableAttribute = $this->createMock(Attribute::class);
        $searchableAttribute->method('getId')->willReturn(1);
        $searchableAttribute->method('getSearchable')->willReturn(true);

        // 创建模型模拟对象
        $model = $this->createMock(Model::class);
        $model->method('getAttributes')->willReturn(
            new ArrayCollection([$searchableAttribute])
        );

        // 配置ModelRepository返回所有有效模型
        $this->modelRepository->method('findBy')
            ->with(['valid' => true])
            ->willReturn([$model]);

        // 创建子查询构建器
        $subQueryBuilder = $this->createMock(QueryBuilder::class);

        // 创建表达式对象和Func/Comparison/Literal对象
        $expr = $this->createMock(Expr::class);
        $inFunc = $this->createMock(Func::class);
        $likeComparison = $this->createMock(Comparison::class);
        $literal = $this->createMock(Literal::class);

        // 配置子查询构建器方法链
        $subQueryBuilder->method('expr')->willReturn($expr);
        $subQueryBuilder->method('select')->willReturnSelf();
        $subQueryBuilder->method('where')->willReturnSelf();
        $subQueryBuilder->method('andWhere')->willReturnSelf();
        $subQueryBuilder->method('getDQL')->willReturn('SUBQUERY_DQL');

        // 配置expr对象返回对应类型对象
        $expr->method('in')
            ->willReturn($inFunc);

        $expr->method('like')
            ->willReturn($likeComparison);

        // 验证特殊字符是否被正确传递
        $expr->expects($this->once())
            ->method('literal')
            ->with('%special\'chars%')
            ->willReturn($literal);

        // 配置ValueRepository返回子查询构建器
        $this->valueRepository->method('createQueryBuilder')
            ->willReturn($subQueryBuilder);

        // 配置主查询构建器
        $mainExpr = $this->createMock(Expr::class);
        $mainInFunc = $this->createMock(Func::class);

        $mainExpr->method('in')
            ->willReturn($mainInFunc);

        $this->queryBuilder->method('expr')
            ->willReturn($mainExpr);

        $this->queryBuilder->method('andWhere')
            ->willReturnSelf();

        // 调用被测方法，使用包含特殊字符的关键词
        $this->contentService->searchByKeyword($this->queryBuilder, 'special\'chars');
    }
}
