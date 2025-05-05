<?php

namespace CmsBundle\Procedure;

use CmsBundle\Entity\LikeLog;
use CmsBundle\Enum\EntityState;
use CmsBundle\Event\LikeEntityEvent;
use CmsBundle\Repository\EntityRepository;
use CmsBundle\Repository\LikeLogRepository;
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

#[MethodExpose('LikeCmsEntity')]
#[MethodTag('内容管理')]
#[MethodDoc('点赞/取消点赞指定文章')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[Log]
class LikeCmsEntity extends LockableProcedure
{
    #[MethodParam('文章ID')]
    public int $entityId;

    public function __construct(
        private readonly EntityRepository $entityRepository,
        private readonly LikeLogRepository $likeLogRepository,
        private readonly Security $security,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public static function getMockResult(): ?array
    {
        return [
            '__message' => 0 === rand(0, 1) ? '已点赞' : '已取消点赞',
        ];
    }

    public function execute(): array
    {
        $entity = $this->entityRepository->findOneBy([
            'id' => $this->entityId,
            'state' => EntityState::PUBLISHED,
        ]);
        if (!$entity) {
            throw new ApiException('找不到文章');
        }

        $log = $this->likeLogRepository->findOneBy([
            'entity' => $entity,
            'user' => $this->security->getUser(),
        ]);
        if (!$log) {
            $log = new LikeLog();
            $log->setEntity($entity);
            $log->setUser($this->security->getUser());
        }

        $log->setValid(!$log->isValid());
        $this->entityManager->persist($log);
        $this->entityManager->flush();

        $event = new LikeEntityEvent();
        $event->setSender($this->security->getUser());
        $event->setReceiver(SystemUser::instance());
        $event->setEntity($entity);

        if ($log->isValid()) {
            $event->setMessage("点赞内容：{$entity->getTitle()}");
        } else {
            $event->setMessage("取消点赞内容：{$entity->getTitle()}");
        }

        $this->eventDispatcher->dispatch($event);

        return [
            '__message' => $log->isValid() ? '已点赞' : '已取消点赞',
        ];
    }
}
