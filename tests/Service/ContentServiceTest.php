<?php

declare(strict_types=1);

namespace CmsBundle\Tests\Service;

use CmsBundle\Entity\Attribute;
use CmsBundle\Entity\Model;
use CmsBundle\Service\ContentService;
use CmsBundle\Service\ModelService;
use CmsBundle\Service\ValueService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Func;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(ContentService::class)]
final class ContentServiceTest extends TestCase
{
    private ContentService $contentService;

    private ValueService $valueService;

    private ModelService $modelService;

    /** @phpstan-var QueryBuilder */
    private QueryBuilder $queryBuilder;

    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        // 创建 EntityManager Mock
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        /*
         * 使用 ValueService Mock 的原因：
         * 1. ValueService 是 readonly 类，不能被匿名类继承
         * 2. Service 是业务逻辑层服务类，没有对应的接口
         * 3. 在测试中需要模拟 Service 的具体行为
         * 4. 使用 PHPUnit Mock 符合 PHP 8.4+ readonly 类要求
         */
        $this->valueService = $this->createMock(ValueService::class);
        $this->valueService->method('buildSearchSubQuery')->willReturn(new QueryBuilder($this->entityManager));

        /*
         * 使用 ModelService Mock 的原因：
         * 1. ModelService 是 readonly 类，不能被匿名类继承
         * 2. Service 是业务逻辑层服务类，没有对应的接口
         * 3. 在测试中需要模拟 Service 的具体行为
         * 4. 使用 PHPUnit Mock 符合 PHP 8.4+ readonly 类要求
         */
        $this->modelService = $this->createMock(ModelService::class);
        // 在 setUp 中不设置默认返回值，让每个测试方法自行设置

        $this->contentService = new ContentService(
            $this->valueService,
            $this->modelService
        );

        // 创建QueryBuilder匿名类实例
        /*
         * 使用 QueryBuilder 匿名类实现的原因：
         * 1. QueryBuilder 是 Doctrine ORM 的具体实现类，没有对应的接口
         * 2. 在测试中需要模拟该类的具体行为
         * 3. 匿名类实现更符合静态分析要求，避免 Mock 的类型推断问题
         */
        $this->queryBuilder = new class($this->entityManager) extends QueryBuilder {
            /** @var callable|null */
            private $exprCallback;

            /** @var callable|null */
            private $andWhereCallback;

            private int $andWhereCallCount = 0;

            public function setExprCallback(callable $callback): void
            {
                $this->exprCallback = $callback;
            }

            public function setAndWhereCallback(callable $callback): void
            {
                $this->andWhereCallback = $callback;
            }

            public function expr(): Expr
            {
                if (null !== $this->exprCallback) {
                    $result = ($this->exprCallback)();
                    if ($result instanceof Expr) {
                        return $result;
                    }
                }

                return parent::expr();
            }

            public function andWhere(mixed ...$where): static
            {
                ++$this->andWhereCallCount;
                if (null !== $this->andWhereCallback) {
                    ($this->andWhereCallback)($where, $this->andWhereCallCount);
                }

                return parent::andWhere(...$where);
            }

            public function getAndWhereCallCount(): int
            {
                return $this->andWhereCallCount;
            }
        };
    }

    /**
     * 测试当没有提供模型时，会搜索所有有效模型的可搜索属性.
     */
    public function testSearchByKeywordWithoutModelSearchesAllValidModels(): void
    {
        // 创建属性匿名类实现
        /*
         * 使用 Attribute 匿名类实现的原因：
         * 1. Entity 是 Doctrine 实体类，不适合定义接口
         * 2. 在测试中需要模拟实体对象的属性和方法
         * 3. 匿名类实现更符合静态分析要求，避免 Mock 的类型推断问题
         */
        $searchableAttribute1 = new class extends Attribute {
            public ?int $id = 1;

            private bool $searchable = true;

            public function getSearchable(): bool
            {
                return $this->searchable;
            }
        };

        /*
         * 使用 Attribute 匿名类实现的原因：
         * 1. Entity 是 Doctrine 实体类，不适合定义接口
         * 2. 在测试中需要模拟实体对象的属性和方法
         * 3. 匿名类实现更符合静态分析要求，避免 Mock 的类型推断问题
         */
        $nonSearchableAttribute = new class extends Attribute {
            public ?int $id = 2;

            private bool $searchable = false;

            public function getSearchable(): bool
            {
                return $this->searchable;
            }
        };

        /*
         * 使用 Attribute 匿名类实现的原因：
         * 1. Entity 是 Doctrine 实体类，不适合定义接口
         * 2. 在测试中需要模拟实体对象的属性和方法
         * 3. 匿名类实现更符合静态分析要求，避免 Mock 的类型推断问题
         */
        $searchableAttribute2 = new class extends Attribute {
            public ?int $id = 3;

            private bool $searchable = true;

            public function getSearchable(): bool
            {
                return $this->searchable;
            }
        };

        // 创建模型匿名类实现
        /*
         * 使用 Model 匿名类实现的原因：
         * 1. Entity 是 Doctrine 实体类，不适合定义接口
         * 2. 在测试中需要模拟实体对象的属性和方法
         * 3. 匿名类实现更符合静态分析要求，避免 Mock 的类型推断问题
         */
        $model1 = new class([$searchableAttribute1, $nonSearchableAttribute]) extends Model {
            /** @var array<int, Attribute> */
            private array $attributes;

            /** @param array<int, Attribute> $attributes */
            public function __construct(array $attributes = [])
            {
                parent::__construct();
                $this->attributes = $attributes;
            }

            /** @return ArrayCollection<int, Attribute> */
            public function getAttributes(): ArrayCollection
            {
                return new ArrayCollection($this->attributes);
            }
        };

        /*
         * 使用 Model 匿名类实现的原因：
         * 1. Entity 是 Doctrine 实体类，不适合定义接口
         * 2. 在测试中需要模拟实体对象的属性和方法
         * 3. 匿名类实现更符合静态分析要求，避免 Mock 的类型推断问题
         */
        $model2 = new class([$searchableAttribute2]) extends Model {
            /** @var array<int, Attribute> */
            private array $attributes;

            /** @param array<int, Attribute> $attributes */
            public function __construct(array $attributes = [])
            {
                parent::__construct();
                $this->attributes = $attributes;
            }

            /** @return ArrayCollection<int, Attribute> */
            public function getAttributes(): ArrayCollection
            {
                return new ArrayCollection($this->attributes);
            }
        };

        // 配置ModelService返回所有有效模型
        $this->modelService->method('findAllValidModels')->willReturn([$model1, $model2]);

        // 创建子查询构建器匿名类实现
        /*
         * 使用 QueryBuilder 匿名类实现的原因：
         * 1. QueryBuilder 是 Doctrine ORM 的具体实现类，没有对应的接口
         * 2. 在测试中需要模拟该类的具体行为
         * 3. 匿名类实现更符合静态分析要求，避免 Mock 的类型推断问题
         */
        $subQueryBuilder = new class($this->entityManager) extends QueryBuilder {
            public function getDQL(): string
            {
                return 'SUBQUERY_DQL';
            }
        };

        // 配置ValueService返回子查询构建器
        $this->valueService->method('buildSearchSubQuery')->willReturn($subQueryBuilder);

        // 配置主查询构建器
        /*
         * 使用 Expr 匿名类实现的原因：
         * 1. Expr 是 Doctrine ORM 的具体实现类，没有对应的接口
         * 2. 在测试中需要模拟该类的具体行为
         * 3. 匿名类实现更符合静态分析要求，避免 Mock 的类型推断问题
         */
        $mainExpr = new class extends Expr {
            /** @var callable|null */
            private $inCallback;

            public function setInCallback(callable $callback): void
            {
                $this->inCallback = $callback;
            }

            public function in(string $x, mixed $y): Func
            {
                if (null !== $this->inCallback) {
                    // 创建一个简单的 Func 对象，而不是匿名类
                    return new Func($x, [$y]);
                }

                return parent::in($x, $y);
            }
        };

        $mainExpr->setInCallback(static fn (string $x, mixed $y) => new Func('IN', ['TEST_DQL']));

        // @phpstan-ignore-next-line method.notFound
        $this->queryBuilder->setExprCallback(static fn () => $mainExpr);

        $andWhereCalled = false;
        // @phpstan-ignore-next-line method.notFound
        $this->queryBuilder->setAndWhereCallback(static function ($where, int $callCount) use (&$andWhereCalled) {
            if (1 === $callCount) {
                $andWhereCalled = true;
                // 验证第一个参数是 Func 对象
                if (!$where[0] instanceof Func) {
                    throw new \UnexpectedValueException('Expected andWhere to be called with Func parameter');
                }
            }
        });

        // 调用被测方法
        $this->contentService->searchByKeyword($this->queryBuilder, 'test');

        // 验证方法被正确调用
        $this->assertTrue($andWhereCalled, 'andWhere should have been called once');
    }

    /**
     * 测试当提供特定模型时，只搜索该模型的可搜索属性.
     */
    public function testSearchByKeywordWithSpecificModelSearchesOnlyThatModel(): void
    {
        // 创建属性匿名类实现
        /*
         * 使用 Attribute 匿名类实现的原因：
         * 1. Entity 是 Doctrine 实体类，不适合定义接口
         * 2. 在测试中需要模拟实体对象的属性和方法
         * 3. 匿名类实现更符合静态分析要求，避免 Mock 的类型推断问题
         */
        $searchableAttribute = new class extends Attribute {
            public ?int $id = 1;

            private bool $searchable = true;

            public function getSearchable(): bool
            {
                return $this->searchable;
            }
        };

        /*
         * 使用 Attribute 匿名类实现的原因：
         * 1. Entity 是 Doctrine 实体类，不适合定义接口
         * 2. 在测试中需要模拟实体对象的属性和方法
         * 3. 匿名类实现更符合静态分析要求，避免 Mock 的类型推断问题
         */
        $nonSearchableAttribute = new class extends Attribute {
            public ?int $id = 2;

            private bool $searchable = false;

            public function getSearchable(): bool
            {
                return $this->searchable;
            }
        };

        // 创建模型匿名类实现
        /*
         * 使用 Model 匿名类实现的原因：
         * 1. Entity 是 Doctrine 实体类，不适合定义接口
         * 2. 在测试中需要模拟实体对象的属性和方法
         * 3. 匿名类实现更符合静态分析要求，避免 Mock 的类型推断问题
         */
        $specificModel = new class([$searchableAttribute, $nonSearchableAttribute]) extends Model {
            /** @var array<int, Attribute> */
            private array $attributes;

            /** @param array<int, Attribute> $attributes */
            public function __construct(array $attributes = [])
            {
                parent::__construct();
                $this->attributes = $attributes;
            }

            /** @return ArrayCollection<int, Attribute> */
            public function getAttributes(): ArrayCollection
            {
                return new ArrayCollection($this->attributes);
            }
        };

        // 创建子查询构建器匿名类实现
        /*
         * 使用 QueryBuilder 匿名类实现的原因：
         * 1. QueryBuilder 是 Doctrine ORM 的具体实现类，没有对应的接口
         * 2. 在测试中需要模拟该类的具体行为
         * 3. 匿名类实现更符合静态分析要求，避免 Mock 的类型推断问题
         */
        $subQueryBuilder = new class($this->entityManager) extends QueryBuilder {
            public function getDQL(): string
            {
                return 'SUBQUERY_DQL';
            }
        };

        // 配置ValueService返回子查询构建器
        $this->valueService->method('buildSearchSubQuery')->willReturn($subQueryBuilder);

        // 配置主查询构建器
        /*
         * 使用 Expr 匿名类实现的原因：
         * 1. Expr 是 Doctrine ORM 的具体实现类，没有对应的接口
         * 2. 在测试中需要模拟该类的具体行为
         * 3. 匿名类实现更符合静态分析要求，避免 Mock 的类型推断问题
         */
        $mainExpr = new class extends Expr {
            /** @var callable|null */
            private $inCallback;

            public function setInCallback(callable $callback): void
            {
                $this->inCallback = $callback;
            }

            public function in(string $x, mixed $y): Func
            {
                if (null !== $this->inCallback) {
                    // 创建一个简单的 Func 对象，而不是匿名类
                    return new Func($x, [$y]);
                }

                return parent::in($x, $y);
            }
        };

        $mainExpr->setInCallback(static fn (string $x, mixed $y) => new Func('IN', ['TEST_DQL']));

        // @phpstan-ignore-next-line method.notFound
        $this->queryBuilder->setExprCallback(static fn () => $mainExpr);

        $andWhereCalled = false;
        // @phpstan-ignore-next-line method.notFound
        $this->queryBuilder->setAndWhereCallback(static function ($where, int $callCount) use (&$andWhereCalled) {
            if (1 === $callCount) {
                $andWhereCalled = true;
                // 验证第一个参数是 Func 对象
                if (!$where[0] instanceof Func) {
                    throw new \UnexpectedValueException('Expected andWhere to be called with Func parameter');
                }
            }
        });

        // 调用被测方法（传入特定模型）
        $this->contentService->searchByKeyword($this->queryBuilder, 'test', $specificModel);

        // 验证方法被正确调用
        $this->assertTrue($andWhereCalled, 'andWhere should have been called once');
    }

    /**
     * 测试当没有可搜索属性时，不修改查询构建器.
     */
    public function testSearchByKeywordWithNoSearchableAttributesDoesNotModifyQueryBuilder(): void
    {
        // 创建属性匿名类实现
        /*
         * 使用 Attribute 匿名类实现的原因：
         * 1. Entity 是 Doctrine 实体类，不适合定义接口
         * 2. 在测试中需要模拟实体对象的属性和方法
         * 3. 匿名类实现更符合静态分析要求，避免 Mock 的类型推断问题
         */
        $nonSearchableAttribute = new class extends Attribute {
            public ?int $id = 2;

            private bool $searchable = false;

            public function getSearchable(): bool
            {
                return $this->searchable;
            }
        };

        // 创建模型匿名类实现
        /*
         * 使用 Model 匿名类实现的原因：
         * 1. Entity 是 Doctrine 实体类，不适合定义接口
         * 2. 在测试中需要模拟实体对象的属性和方法
         * 3. 匿名类实现更符合静态分析要求，避免 Mock 的类型推断问题
         */
        $model = new class([$nonSearchableAttribute]) extends Model {
            /** @var array<int, Attribute> */
            private array $attributes;

            /** @param array<int, Attribute> $attributes */
            public function __construct(array $attributes = [])
            {
                parent::__construct();
                $this->attributes = $attributes;
            }

            /** @return ArrayCollection<int, Attribute> */
            public function getAttributes(): ArrayCollection
            {
                return new ArrayCollection($this->attributes);
            }
        };

        // 配置ModelService返回所有有效模型
        $this->modelService->method('findAllValidModels')->willReturn([$model]);

        // 验证QueryBuilder的andWhere方法不会被调用
        $andWhereCalled = false;
        // @phpstan-ignore-next-line method.notFound
        $this->queryBuilder->setAndWhereCallback(static function ($where, int $callCount) use (&$andWhereCalled) {
            $andWhereCalled = true;
            throw new \UnexpectedValueException('andWhere should not have been called');
        });

        // 调用被测方法
        $this->contentService->searchByKeyword($this->queryBuilder, 'test');

        // 验证方法没有被调用
        $this->assertFalse($andWhereCalled, 'andWhere should not have been called');
        // @phpstan-ignore-next-line method.notFound
        $this->assertSame(0, $this->queryBuilder->getAndWhereCallCount(), 'andWhere should not have been called');
    }
}
