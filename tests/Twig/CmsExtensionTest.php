<?php

namespace CmsBundle\Tests\Twig;

use CmsBundle\Entity\Entity;
use CmsBundle\Entity\Model;
use CmsBundle\Enum\EntityState;
use CmsBundle\Exception\ModelNotFoundException;
use CmsBundle\Repository\EntityRepository;
use CmsBundle\Repository\ModelRepository;
use CmsBundle\Twig\CmsExtension;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Twig\TwigFunction;

class CmsExtensionTest extends TestCase
{
    private ModelRepository&MockObject $modelRepository;
    private EntityRepository&MockObject $entityRepository;
    private CmsExtension $extension;

    protected function setUp(): void
    {
        $this->modelRepository = $this->createMock(ModelRepository::class);
        $this->entityRepository = $this->createMock(EntityRepository::class);
        $this->extension = new CmsExtension($this->modelRepository, $this->entityRepository);
    }

    public function testGetFunctions(): void
    {
        $functions = $this->extension->getFunctions();

        $this->assertCount(2, $functions);
        $this->assertContainsOnlyInstancesOf(TwigFunction::class, $functions);

        $functionNames = array_map(fn(TwigFunction $func) => $func->getName(), $functions);
        $this->assertContains('get_cms_entity_detail', $functionNames);
        $this->assertContains('get_cms_entity_list', $functionNames);
    }

    public function testGetCmsEntityDetail(): void
    {
        $entityId = 'test-id';
        $entity = $this->createMock(Entity::class);

        $this->entityRepository->expects($this->once())
            ->method('findOneBy')
            ->with([
                'id' => $entityId,
                'state' => EntityState::PUBLISHED,
            ])
            ->willReturn($entity);

        $result = $this->extension->getCmsEntityDetail($entityId);

        $this->assertSame($entity, $result);
    }

    public function testGetCmsEntityDetailReturnsNull(): void
    {
        $entityId = 'non-existent-id';

        $this->entityRepository->expects($this->once())
            ->method('findOneBy')
            ->with([
                'id' => $entityId,
                'state' => EntityState::PUBLISHED,
            ])
            ->willReturn(null);

        $result = $this->extension->getCmsEntityDetail($entityId);

        $this->assertNull($result);
    }

    public function testGetCmsEntityList(): void
    {
        $modelCode = 'test-model';
        $limit = 10;
        $offset = 5;

        $model = $this->createMock(Model::class);
        $entities = [$this->createMock(Entity::class)];

        $query = $this->createMock(Query::class);
        $queryBuilder = $this->createMock(QueryBuilder::class);

        $this->modelRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['code' => $modelCode])
            ->willReturn($model);

        $this->entityRepository->expects($this->once())
            ->method('createQueryBuilder')
            ->with('a')
            ->willReturn($queryBuilder);

        $queryBuilder->expects($this->once())
            ->method('where')
            ->with('a.model = :model AND a.state = :state')
            ->willReturnSelf();

        $queryBuilder->expects($this->exactly(2))
            ->method('setParameter')
            ->willReturnCallback(function ($key, $value) use ($model, $queryBuilder) {
                if ($key === 'model') {
                    $this->assertSame($model, $value);
                } elseif ($key === 'state') {
                    $this->assertSame(EntityState::PUBLISHED, $value);
                }
                return $queryBuilder;
            });

        $queryBuilder->expects($this->exactly(2))
            ->method('addOrderBy')
            ->willReturnSelf();

        $queryBuilder->expects($this->once())
            ->method('setFirstResult')
            ->with($offset)
            ->willReturnSelf();

        $queryBuilder->expects($this->once())
            ->method('setMaxResults')
            ->with($limit)
            ->willReturnSelf();

        $queryBuilder->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);

        $query->expects($this->once())
            ->method('getResult')
            ->willReturn($entities);

        $result = $this->extension->getCmsEntityList($modelCode, $limit, $offset);

        $this->assertSame($entities, $result);
    }

    public function testGetCmsEntityListWithDefaults(): void
    {
        $modelCode = 'test-model';

        $model = $this->createMock(Model::class);
        $entities = [$this->createMock(Entity::class)];

        $query = $this->createMock(Query::class);
        $queryBuilder = $this->createMock(QueryBuilder::class);

        $this->modelRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['code' => $modelCode])
            ->willReturn($model);

        $this->entityRepository->expects($this->once())
            ->method('createQueryBuilder')
            ->with('a')
            ->willReturn($queryBuilder);

        $queryBuilder->method('where')->willReturnSelf();
        $queryBuilder->method('setParameter')->willReturnSelf();
        $queryBuilder->method('addOrderBy')->willReturnSelf();
        $queryBuilder->method('setFirstResult')->with(0)->willReturnSelf();
        $queryBuilder->method('setMaxResults')->with(20)->willReturnSelf();
        $queryBuilder->method('getQuery')->willReturn($query);

        $query->expects($this->once())
            ->method('getResult')
            ->willReturn($entities);

        $result = $this->extension->getCmsEntityList($modelCode);

        $this->assertSame($entities, $result);
    }

    public function testGetCmsEntityListThrowsExceptionWhenModelNotFound(): void
    {
        $modelCode = 'non-existent-model';

        $this->modelRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['code' => $modelCode])
            ->willReturn(null);

        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage("找不到指定CMS模型: {$modelCode}");

        $this->extension->getCmsEntityList($modelCode);
    }
}
