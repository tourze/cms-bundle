<?php

namespace CmsBundle\Procedure;

use CmsBundle\Entity\CollectLog;
use CmsBundle\Enum\EntityState;
use CmsBundle\Event\CollectEntityEvent;
use CmsBundle\Repository\CollectLogRepository;
use CmsBundle\Repository\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;
use Tourze\UserIDBundle\Model\SystemUser;

#[MethodExpose('CollectCmsEntity')]
#[MethodTag('内容管理')]
#[MethodDoc('收藏/反收藏指定文章')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[Log]
class CollectCmsEntity extends LockableProcedure
{
    #[MethodParam('文章ID')]
    public int $entityId;

    public function __construct(
        private readonly EntityRepository $entityRepository,
        private readonly CollectLogRepository $collectLogRepository,
        private readonly Security $security,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function execute(): array
    {
        $entity = $this->entityRepository->findOneBy([
            'id' => $this->entityId,
            'state' => EntityState::PUBLISHED,
        ]);
        if ($entity === null) {
            throw new ApiException('找不到文章');
        }

        $log = $this->collectLogRepository->findOneBy([
            'entity' => $entity,
            'user' => $this->security->getUser(),
        ]);
        if ($log === null) {
            $log = new CollectLog();
            $log->setEntity($entity);
            $log->setUser($this->security->getUser());
        }

        $log->setValid(!$log->isValid());
        $this->entityManager->persist($log);
        $this->entityManager->flush();

        $event = new CollectEntityEvent();
        $event->setSender($this->security->getUser());
        $event->setReceiver(SystemUser::instance());
        $event->setEntity($entity);

        if ($log->isValid()) {
            $event->setMessage("收藏内容：{$entity->getTitle()}");
        } else {
            $event->setMessage("取消收藏内容：{$entity->getTitle()}");
        }

        $this->eventDispatcher->dispatch($event);

        return [
            '__message' => $log->isValid() ? '收藏成功' : '已取消收藏',
        ];
    }

    public static function getMockResult(): ?array
    {
        return [
            '__message' => 0 === rand(0, 1) ? '收藏成功' : '已取消收藏',
        ];
    }
}
