<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\Procedure;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tourze\CatalogBundle\Entity\Catalog;
use Tourze\CmsBundle\Entity\Attribute;
use Tourze\CmsBundle\Entity\Model;
use Tourze\CmsBundle\Entity\Value;
use Tourze\CmsBundle\Enum\EntityState;
use Tourze\CmsBundle\Event\VisitEntityEvent;
use Tourze\CmsBundle\Param\GetCmsEntityDetailParam;
use Tourze\CmsBundle\Repository\VisitStatRepository;
use Tourze\CmsBundle\Service\CollectServiceInterface;
use Tourze\CmsBundle\Service\EntityService;
use Tourze\CmsBundle\Service\LikeServiceInterface;
use Tourze\CmsBundle\Service\StatService;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Tourze\JsonRPC\Core\Result\ArrayResult;
use Tourze\TagManageBundle\Entity\Tag;
use Tourze\UserIDBundle\Model\SystemUser;

#[MethodTag(name: '内容管理')]
#[MethodDoc(summary: '获取CMS文章详情')]
#[MethodExpose(method: 'GetCmsEntityDetail')]
final class GetCmsEntityDetail extends BaseProcedure
{
    public function __construct(
        private readonly EntityService $entityService,
        private readonly StatService $statService,
        private readonly Security $security,
        private readonly VisitStatRepository $visitStatRepository,
        private readonly LikeServiceInterface $likeService,
        private readonly CollectServiceInterface $collectService,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    /**
     * @phpstan-param GetCmsEntityDetailParam $param
     */
    public function execute(GetCmsEntityDetailParam|RpcParamInterface $param): ArrayResult
    {
        $entity = $this->entityService->findEntityBy([
            'id' => $param->entityId,
            'state' => EntityState::PUBLISHED,
        ]);
        if (null === $entity) {
            throw new ApiException('记录不存在');
        }

        $visitTotal = $this->visitStatRepository->createQueryBuilder('v')
            ->select('SUM(v.value) as visitTotal')
            ->where('v.entityId = :entityId')
            ->setParameter('entityId', $entity->getId())
            ->getQuery()
            ->getSingleScalarResult()
        ;

        $user = $this->security->getUser();

        $result = [
            'id' => $entity->getId(),
            'title' => $entity->getTitle(),
            'model' => $this->formatModel($entity->getModel()),
            'catalogs' => $this->formatCatalogs($entity->getCatalogs()->toArray()),
            'tags' => $this->formatTags($entity->getTags()->toArray()),
            'valueList' => $this->formatValueList($entity->getValueList()->toArray()),
            'publishTime' => $entity->getPublishTime()?->format('c'),
            'endTime' => $entity->getEndTime()?->format('c'),
            'state' => $entity->getState()->value,
            'values' => $entity->getValues(),
            'visitTotal' => (int) ($visitTotal ?? 0),
            'isLike' => null !== $user && $this->likeService->isLikedByUser($entity, $user),
            'isCollected' => null !== $user && $this->collectService->isCollectedByUser($entity, $user),
        ];

        $this->statService->updateStat($entity);

        // 分发事件处理
        if (null !== $this->security->getUser()) {
            $event = new VisitEntityEvent();
            $event->setSender($this->security->getUser());
            $event->setReceiver(SystemUser::instance());
            $event->setEntity($entity);
            $event->setMessage("正在访问内容：{$entity->getTitle()}");
            $this->eventDispatcher->dispatch($event);
        }

        return new ArrayResult($result);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function formatModel(?Model $model): ?array
    {
        if (null === $model) {
            return null;
        }

        return [
            'code' => $model->getCode(),
            'title' => $model->getTitle(),
            'allowLike' => $model->getAllowLike(),
            'allowCollect' => $model->getAllowCollect(),
            'allowShare' => $model->getAllowShare(),
            'sortNumber' => $model->getSortNumber(),
        ];
    }

    /**
     * @param array<int, Catalog> $catalogs
     *
     * @return array<int, array<string, mixed>>
     */
    private function formatCatalogs(array $catalogs): array
    {
        $result = [];
        foreach ($catalogs as $catalog) {
            $result[] = [
                'id' => $catalog->getId(),
                'name' => $catalog->getName(),
            ];
        }

        return $result;
    }

    /**
     * @param array<int, Tag> $tags
     *
     * @return array<int, array<string, mixed>>
     */
    private function formatTags(array $tags): array
    {
        $result = [];
        foreach ($tags as $tag) {
            $result[] = [
                'id' => $tag->getId(),
                'name' => $tag->getName(),
            ];
        }

        return $result;
    }

    /**
     * @param array<int, Value> $valueList
     *
     * @return array<int, array<string, mixed>>
     */
    private function formatValueList(array $valueList): array
    {
        $result = [];
        foreach ($valueList as $value) {
            $result[] = [
                'attribute' => $this->formatAttribute($value->getAttribute()),
                'data' => $value->getData(),
            ];
        }

        return $result;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function formatAttribute(?Attribute $attribute): ?array
    {
        if (null === $attribute) {
            return null;
        }

        return [
            'name' => $attribute->getName(),
            'title' => $attribute->getTitle(),
            'type' => $attribute->getType()?->value,
        ];
    }
}
