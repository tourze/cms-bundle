<?php

namespace CmsBundle\Procedure;

use CmsBundle\Enum\EntityState;
use CmsBundle\Event\VisitEntityEvent;
use CmsBundle\Repository\EntityRepository;
use CmsBundle\Repository\LikeLogRepository;
use CmsBundle\Repository\VisitStatRepository;
use CmsBundle\Service\StatService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Tourze\UserIDBundle\Model\SystemUser;

#[MethodTag(name: '内容管理')]
#[MethodDoc(summary: '获取CMS文章详情')]
#[MethodExpose(method: 'GetCmsEntityDetail')]
class GetCmsEntityDetail extends BaseProcedure
{
    #[MethodParam(description: '文章ID')]
    public int $entityId;

    public function __construct(
        private readonly EntityRepository $entityRepository,
        private readonly NormalizerInterface $normalizer,
        private readonly StatService $statService,
        private readonly Security $security,
        private readonly VisitStatRepository $visitStatRepository,
        private readonly LikeLogRepository $likeLogRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(): array
    {
        $entity = $this->entityRepository->findOneBy([
            'id' => $this->entityId,
            'state' => EntityState::PUBLISHED,
        ]);
        if ($entity === null) {
            throw new ApiException('找不到文章');
        }

        $normalized = $this->normalizer->normalize($entity, 'array', ['groups' => 'restful_read']);
        if (!is_array($normalized)) {
            throw new ApiException('序列化失败');
        }
        $result = $normalized;

        $visitTotal = $this->visitStatRepository->createQueryBuilder('v')
            ->select('SUM(v.value) as visitTotal')
            ->where('v.entityId = :entityId')
            ->setParameter('entityId', $entity->getId())
            ->getQuery()
            ->getSingleScalarResult();

        $result['visitTotal'] = intval($visitTotal ?? 0);
        $result['isLike'] = false;
        $user = $this->security->getUser();
        if ($user !== null) {
            $log = $this->likeLogRepository->findOneBy([
                'entity' => $entity,
                'valid' => true,
                'user' => $this->security->getUser(),
            ]);
            $result['isLike'] = (bool) $log;
        }

        $this->statService->updateStat($entity);

        // 分发事件处理
        if ($this->security->getUser() !== null) {
            $event = new VisitEntityEvent();
            $event->setSender($this->security->getUser());
            $event->setReceiver(SystemUser::instance());
            $event->setEntity($entity);
            $event->setMessage("正在访问内容：{$entity->getTitle()}");
            $this->eventDispatcher->dispatch($event);
        }

        return $result;
    }
}
