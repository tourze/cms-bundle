<?php

namespace CmsBundle\Tests\Procedure;

use CmsBundle\Entity\Entity;
use CmsBundle\Entity\LikeLog;
use CmsBundle\Entity\Model;
use CmsBundle\Enum\EntityState;
use CmsBundle\Procedure\GetCmsEntityDetail;
use CmsBundle\Repository\EntityRepository;
use CmsBundle\Repository\LikeLogRepository;
use CmsBundle\Repository\VisitStatRepository;
use CmsBundle\Service\StatService;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class GetCmsEntityDetailTest extends TestCase
{
    private GetCmsEntityDetail $procedure;
    private EntityRepository|MockObject $entityRepository;
    private NormalizerInterface|MockObject $normalizer;
    private StatService|MockObject $statService;
    private Security|MockObject $security;
    private VisitStatRepository|MockObject $visitStatRepository;
    private LikeLogRepository|MockObject $likeLogRepository;
    private EventDispatcherInterface|MockObject $eventDispatcher;

    protected function setUp(): void
    {
        $this->entityRepository = $this->createMock(EntityRepository::class);
        $this->normalizer = $this->createMock(NormalizerInterface::class);
        $this->statService = $this->createMock(StatService::class);
        $this->security = $this->createMock(Security::class);
        $this->visitStatRepository = $this->createMock(VisitStatRepository::class);
        $this->likeLogRepository = $this->createMock(LikeLogRepository::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $this->procedure = new GetCmsEntityDetail(
            $this->entityRepository,
            $this->normalizer,
            $this->statService,
            $this->security,
            $this->visitStatRepository,
            $this->likeLogRepository,
            $this->eventDispatcher
        );

        // 设置entityId参数
        $reflection = new \ReflectionProperty(GetCmsEntityDetail::class, 'entityId');
        $reflection->setAccessible(true);
        $reflection->setValue($this->procedure, 123);
    }

    /**
     * 测试获取已发布的内容详情
     */
    public function testExecute_withPublishedEntity_returnsFormattedEntityDetail(): void
    {
        // 创建模拟的Entity和Model对象
        $model = $this->createMock(Model::class);
        $model->method('getCode')->willReturn('article');

        $entity = $this->createMock(Entity::class);
        $entity->method('getId')->willReturn(123);
        $entity->method('getState')->willReturn(EntityState::PUBLISHED);
        $entity->method('getModel')->willReturn($model);
        $entity->method('getTitle')->willReturn('测试文章');

        // 配置entityRepository返回实体
        $this->entityRepository->method('findOneBy')
            ->with([
                'id' => 123,
                'state' => EntityState::PUBLISHED,
            ])
            ->willReturn($entity);

        // 配置安全上下文返回null用户（未登录）
        $this->security->method('getUser')->willReturn(null);

        // 配置visitStatRepository
        $visitStatQueryBuilder = $this->createMock(QueryBuilder::class);
        $visitStatQuery = $this->getMockBuilder(Query::class)
            ->disableOriginalConstructor()
            ->getMock();

        $visitStatQueryBuilder->method('select')->willReturnSelf();
        $visitStatQueryBuilder->method('where')->willReturnSelf();
        $visitStatQueryBuilder->method('setParameter')->willReturnSelf();
        $visitStatQueryBuilder->method('getQuery')->willReturn($visitStatQuery);

        $this->visitStatRepository->method('createQueryBuilder')
            ->willReturn($visitStatQueryBuilder);

        $visitStatQuery->method('getSingleScalarResult')->willReturn(100); // 访问量

        // 配置StatService不执行任何操作（已由expects($this->once())验证调用）
        $this->statService->expects($this->once())->method('updateStat');

        // 配置normalizer返回格式化的实体数据
        $this->normalizer->method('normalize')
            ->with($entity, 'array', ['groups' => 'restful_read'])
            ->willReturn([
                'id' => 123,
                'title' => '测试文章',
                'modelCode' => 'article'
            ]);

        // 调用被测方法
        $result = $this->procedure->execute();

        // 验证结果
        $this->assertArrayHasKey('id', $result);
        $this->assertEquals(123, $result['id']);
        $this->assertArrayHasKey('title', $result);
        $this->assertEquals('测试文章', $result['title']);
        $this->assertArrayHasKey('modelCode', $result);
        $this->assertEquals('article', $result['modelCode']);
        $this->assertArrayHasKey('visitTotal', $result);
        $this->assertEquals(100, $result['visitTotal']);
        $this->assertArrayHasKey('isLike', $result);
        $this->assertFalse($result['isLike']); // 未登录用户默认为false
    }

    /**
     * 测试获取不存在的内容详情
     */
    public function testExecute_withNonExistentEntity_returnsNull(): void
    {
        // 配置entityRepository返回null
        $this->entityRepository->method('findOneBy')
            ->willReturn(null);

        // 确保StatService不会被调用
        $this->statService->expects($this->never())->method('updateStat');

        // 预期执行会抛出异常
        $this->expectException(\Tourze\JsonRPC\Core\Exception\ApiException::class);
        $this->expectExceptionMessage('找不到文章');

        // 调用被测方法
        $this->procedure->execute();
    }

    /**
     * 测试已登录用户访问内容详情
     */
    public function testExecute_withLoggedInUser_determinesLikeStatus(): void
    {
        // 创建模拟的Entity和Model对象
        $model = $this->createMock(Model::class);
        $model->method('getCode')->willReturn('article');

        $entity = $this->createMock(Entity::class);
        $entity->method('getId')->willReturn(123);
        $entity->method('getState')->willReturn(EntityState::PUBLISHED);
        $entity->method('getModel')->willReturn($model);
        $entity->method('getTitle')->willReturn('测试文章');

        // 创建模拟用户
        $user = $this->createMock(UserInterface::class);

        // 配置安全上下文返回用户
        $this->security->method('getUser')->willReturn($user);

        // 配置entityRepository返回实体
        $this->entityRepository->method('findOneBy')
            ->willReturn($entity);

        // 配置likeLogRepository返回点赞记录
        $likeLog = $this->createMock(LikeLog::class);
        $this->likeLogRepository->method('findOneBy')
            ->with([
                'entity' => $entity,
                'valid' => true,
                'user' => $user,
            ])
            ->willReturn($likeLog); // 用户已点赞

        // 配置visitStatRepository
        $visitStatQueryBuilder = $this->createMock(QueryBuilder::class);
        $visitStatQuery = $this->getMockBuilder(Query::class)
            ->disableOriginalConstructor()
            ->getMock();

        $visitStatQueryBuilder->method('select')->willReturnSelf();
        $visitStatQueryBuilder->method('where')->willReturnSelf();
        $visitStatQueryBuilder->method('setParameter')->willReturnSelf();
        $visitStatQueryBuilder->method('getQuery')->willReturn($visitStatQuery);

        $this->visitStatRepository->method('createQueryBuilder')
            ->willReturn($visitStatQueryBuilder);

        $visitStatQuery->method('getSingleScalarResult')->willReturn(100); // 访问量

        // 配置StatService不执行任何操作
        $this->statService->expects($this->once())->method('updateStat');

        // 配置normalizer返回格式化的实体数据
        $this->normalizer->method('normalize')
            ->willReturn([
                'id' => 123,
                'title' => '测试文章',
                'modelCode' => 'article'
            ]);

        // 配置eventDispatcher
        $this->eventDispatcher->expects($this->once())->method('dispatch');

        // 调用被测方法
        $result = $this->procedure->execute();

        // 验证结果
        $this->assertArrayHasKey('isLike', $result);
        $this->assertTrue($result['isLike']); // 用户已点赞
    }
}
