<?php

namespace CmsBundle\Tests\Service;

use Carbon\CarbonImmutable;
use CmsBundle\Entity\Entity;
use CmsBundle\Entity\VisitStat;
use CmsBundle\Repository\VisitStatRepository;
use CmsBundle\Service\StatService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Tourze\DoctrineEntityLockBundle\Service\EntityLockService;

class StatServiceTest extends TestCase
{
    private StatService $statService;
    private VisitStatRepository|MockObject $visitStatRepository;
    private LoggerInterface|MockObject $logger;
    private EntityLockService|MockObject $entityLockService;
    private EntityManagerInterface|MockObject $entityManager;

    protected function setUp(): void
    {
        $this->visitStatRepository = $this->createMock(VisitStatRepository::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->entityLockService = $this->createMock(EntityLockService::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->statService = new StatService(
            $this->visitStatRepository,
            $this->logger,
            $this->entityLockService,
            $this->entityManager
        );
    }

    /**
     * 测试更新已有统计记录
     */
    public function testUpdateStat_withExistingStat_incrementsValue(): void
    {
        // 创建模拟Entity对象
        $entity = $this->createMock(Entity::class);
        $entity->method('getId')->willReturn(123);

        // 获取当前日期的开始时间
        $today = CarbonImmutable::now()->startOfDay();

        // 创建模拟的现有统计对象
        $existingStat = $this->createMock(VisitStat::class);
        $existingStat->expects($this->once())
            ->method('getValue')
            ->willReturn(10);
        $existingStat->expects($this->once())
            ->method('setValue')
            ->with(11);

        // 配置visitStatRepository返回现有统计
        $this->visitStatRepository->expects($this->once())
            ->method('findOneBy')
            ->with([
                'entityId' => 123,
                'date' => $today,
            ])
            ->willReturn($existingStat);

        // 配置entityManager
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($existingStat);
        $this->entityManager->expects($this->once())
            ->method('flush');

        // 配置entityLockService执行锁定回调函数
        $this->entityLockService->expects($this->once())
            ->method('lockEntity')
            ->with($entity)
            ->willReturnCallback(function ($entity, $callback) {
                $callback();
            });

        // 调用被测方法
        $this->statService->updateStat($entity);
    }

    /**
     * 测试创建新统计记录
     */
    public function testUpdateStat_withNoExistingStat_createsNewStat(): void
    {
        // 创建模拟Entity对象
        $entity = $this->createMock(Entity::class);
        $entity->method('getId')->willReturn(123);

        // 获取当前日期的开始时间
        $today = CarbonImmutable::now()->startOfDay();

        // 配置visitStatRepository返回null（无现有统计）
        $this->visitStatRepository->expects($this->once())
            ->method('findOneBy')
            ->with([
                'entityId' => 123,
                'date' => $today,
            ])
            ->willReturn(null);

        // 配置entityManager，检查是否存储了新的VisitStat对象
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->callback(function ($stat) use ($entity) {
                $this->assertInstanceOf(VisitStat::class, $stat);
                $this->assertEquals($entity->getId(), $stat->getEntityId());
                $this->assertEquals(1, $stat->getValue());
                return true;
            }));
        $this->entityManager->expects($this->once())
            ->method('flush');

        // 配置entityLockService执行锁定回调函数
        $this->entityLockService->expects($this->once())
            ->method('lockEntity')
            ->with($entity)
            ->willReturnCallback(function ($entity, $callback) {
                $callback();
            });

        // 调用被测方法
        $this->statService->updateStat($entity);
    }

    /**
     * 测试异常处理
     */
    public function testUpdateStat_whenExceptionOccurs_logsError(): void
    {
        // 创建模拟Entity对象
        $entity = $this->createMock(Entity::class);
        $entity->method('getId')->willReturn(123);

        // 获取当前日期的开始时间
        $today = CarbonImmutable::now()->startOfDay();

        // 创建模拟的现有统计对象
        $existingStat = $this->createMock(VisitStat::class);
        $existingStat->method('getValue')->willReturn(10);
        $existingStat->method('setValue')->with(11);

        // 配置visitStatRepository返回现有统计
        $this->visitStatRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($existingStat);

        // 配置entityManager抛出异常
        $exception = new \Exception('测试异常');
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($existingStat);
        $this->entityManager->expects($this->once())
            ->method('flush')
            ->willThrowException($exception);

        // 预期logger记录错误
        $this->logger->expects($this->once())
            ->method('error')
            ->with(
                '更新CMS内容统计发生异常',
                $this->callback(function ($context) use ($exception) {
                    return isset($context['exception']) && $context['exception'] === $exception;
                })
            );

        // 配置entityLockService执行锁定回调函数
        $this->entityLockService->expects($this->once())
            ->method('lockEntity')
            ->with($entity)
            ->willReturnCallback(function ($entity, $callback) {
                $callback();
            });

        // 调用被测方法
        $this->statService->updateStat($entity);
    }
}
