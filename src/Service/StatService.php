<?php

namespace CmsBundle\Service;

use Carbon\CarbonImmutable;
use CmsBundle\Entity\Entity;
use CmsBundle\Entity\VisitStat;
use CmsBundle\Repository\VisitStatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Tourze\DoctrineEntityLockBundle\Service\EntityLockService;
use Tourze\Symfony\AopAsyncBundle\Attribute\Async;

class StatService
{
    public function __construct(
        private readonly VisitStatRepository $visitStatRepository,
        private readonly LoggerInterface $logger,
        private readonly EntityLockService $entityLockService,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * 更新统计信息
     */
    #[Async]
    public function updateStat(Entity $entity): void
    {
        // 加个锁，减少重复可能
        $this->entityLockService->lockEntity($entity, function () use ($entity) {
            $date = CarbonImmutable::now()->startOfDay();

            // 更新统计
            $stat = $this->visitStatRepository->findOneBy([
                'entityId' => $entity->getId(),
                'date' => $date,
            ]);
            if ($stat === null) {
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
        });
    }
}
