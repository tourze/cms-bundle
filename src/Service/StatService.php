<?php

declare(strict_types=1);

namespace CmsBundle\Service;

use Carbon\CarbonImmutable;
use CmsBundle\Entity\Entity;
use CmsBundle\Entity\VisitStat;
use CmsBundle\Repository\VisitStatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Tourze\DoctrineEntityLockBundle\Service\EntityLockService;

#[WithMonologChannel(channel: 'cms')]
readonly class StatService
{
    public function __construct(
        private VisitStatRepository $visitStatRepository,
        private LoggerInterface $logger,
        private EntityManagerInterface $entityManager,
        private ?EntityLockService $entityLockService = null,
    ) {
    }

    /**
     * 获取实体的访问总量.
     */
    public function getVisitTotal(Entity $entity): int
    {
        $visitTotal = $this->visitStatRepository->createQueryBuilder('v')
            ->select('SUM(v.value) as visitTotal')
            ->where('v.entityId = :entityId')
            ->setParameter('entityId', $entity->getId())
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return (int) ($visitTotal ?? 0);
    }

    /**
     * 更新统计信息.
     */
    public function updateStat(Entity $entity): void
    {
        $updateLogic = function () use ($entity): void {
            $date = CarbonImmutable::now()->startOfDay();

            // 更新统计
            $stat = $this->visitStatRepository->findOneBy([
                'entityId' => $entity->getId(),
                'date' => $date,
            ]);
            if (null === $stat) {
                $stat = new VisitStat();
                $stat->setEntityId((string) $entity->getId());
                $stat->setDate($date);
                $stat->setValue(0);
            }
            $stat->setValue(($stat->getValue() ?? 0) + 1);
            try {
                $this->entityManager->persist($stat);
                $this->entityManager->flush();
            } catch (\Throwable $exception) {
                $this->logger->error('更新CMS内容统计发生异常', [
                    'exception' => $exception,
                ]);
            }
        };

        // 如果有锁服务，使用锁，否则直接执行
        if (null !== $this->entityLockService) {
            $this->entityLockService->lockEntity($entity, $updateLogic);
        } else {
            $updateLogic();
        }
    }
}
