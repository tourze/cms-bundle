<?php

declare(strict_types=1);

namespace CmsBundle\Tests\Service;

use CmsBundle\Entity\Entity;
use CmsBundle\Entity\VisitStat;
use CmsBundle\Enum\EntityState;
use CmsBundle\Repository\VisitStatRepository;
use CmsBundle\Service\StatService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(StatService::class)]
#[RunTestsInSeparateProcesses]
final class StatServiceTest extends AbstractIntegrationTestCase
{
    private StatService $statService;
    private VisitStatRepository $visitStatRepository;

    /**
     * 测试更新已有统计记录.
     */
    public function testUpdateStatWithExistingStatIncrementsValue(): void
    {
        $this->setUpMocks();

        // 创建真实的Entity对象
        $entity = new Entity();
        $entity->setTitle('Test Entity '.uniqid());
        $entity->setState(EntityState::PUBLISHED);

        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = self::getEntityManager();
        $entityManager->persist($entity);
        $entityManager->flush();

        // 获取当前日期的开始时间
        $today = new \DateTimeImmutable();
        $today = $today->setTime(0, 0, 0, 0);

        // 创建已有的统计记录
        $existingStat = new VisitStat();
        $existingStat->setEntityId((string) $entity->getId());
        $existingStat->setDate($today);
        $existingStat->setValue(10);

        $entityManager->persist($existingStat);
        $entityManager->flush();

        // 调用被测方法
        $this->statService->updateStat($entity);

        // 刷新实体以获取更新后的值
        $entityManager->refresh($existingStat);

        // 验证统计值被正确增加
        $this->assertSame(11, $existingStat->getValue());
    }

    /**
     * 测试创建新统计记录.
     */
    public function testUpdateStatWithNoExistingStatCreatesNewStat(): void
    {
        $this->setUpMocks();

        // 创建真实的Entity对象
        $entity = new Entity();
        $entity->setTitle('Test Entity '.uniqid());
        $entity->setState(EntityState::PUBLISHED);

        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = self::getEntityManager();
        $entityManager->persist($entity);
        $entityManager->flush();

        // 获取当前日期的开始时间
        $today = new \DateTimeImmutable();
        $today = $today->setTime(0, 0, 0, 0);

        // 调用被测方法
        $this->statService->updateStat($entity);

        // 验证新的统计记录被创建
        $stat = $this->visitStatRepository->findOneBy([
            'entityId' => (string) $entity->getId(),
            'date' => $today,
        ]);

        $this->assertNotNull($stat, '应该创建新的统计记录');
        $this->assertSame(1, $stat->getValue(), '新统计记录的值应该是1');
        $this->assertSame($today->format('Y-m-d'), $stat->getDate()?->format('Y-m-d'), '统计记录的日期应该正确');
    }

    /**
     * 测试服务基本功能正常运行.
     */
    public function testUpdateStatBasicFunctionality(): void
    {
        $this->setUpMocks();

        // 创建真实的Entity对象
        $entity = new Entity();
        $entity->setTitle('Test Entity '.uniqid());
        $entity->setState(EntityState::PUBLISHED);

        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = self::getEntityManager();
        $entityManager->persist($entity);
        $entityManager->flush();

        // 第一次调用应该创建新记录
        $this->statService->updateStat($entity);

        $today = new \DateTimeImmutable();
        $today = $today->setTime(0, 0, 0, 0);
        $stat = $this->visitStatRepository->findOneBy([
            'entityId' => (string) $entity->getId(),
            'date' => $today,
        ]);

        $this->assertNotNull($stat, '第一次调用应该创建新的统计记录');
        $this->assertSame(1, $stat->getValue(), '第一次统计值应该是1');

        // 第二次调用应该增加现有记录
        $this->statService->updateStat($entity);

        $entityManager->refresh($stat);
        $this->assertSame(2, $stat->getValue(), '第二次调用应该将统计值增加到2');
    }

    protected function onSetUp(): void
    {
        // 空实现，保持兼容性
    }

    private function setUpMocks(): void
    {
        // 从容器获取服务实例，符合集成测试的要求
        $this->statService = self::getService(StatService::class);
        $this->visitStatRepository = self::getService(VisitStatRepository::class);
    }
}
