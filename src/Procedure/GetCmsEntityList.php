<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\Procedure;

use Symfony\Bundle\SecurityBundle\Security;
use Tourze\CatalogBundle\Entity\Catalog;
use Tourze\CatalogBundle\Service\CatalogService;
use Tourze\CmsBundle\Entity\Attribute;
use Tourze\CmsBundle\Entity\Entity;
use Tourze\CmsBundle\Entity\Model;
use Tourze\CmsBundle\Entity\Value;
use Tourze\CmsBundle\Param\GetCmsEntityListParam;
use Tourze\CmsBundle\Service\CollectServiceInterface;
use Tourze\CmsBundle\Service\ContentService;
use Tourze\CmsBundle\Service\EntityService;
use Tourze\CmsBundle\Service\LikeServiceInterface;
use Tourze\CmsBundle\Service\ModelService;
use Tourze\CmsBundle\Service\StatService;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Tourze\JsonRPC\Core\Result\ArrayResult;
use Tourze\JsonRPCPaginatorBundle\Procedure\PaginatorTrait;
use Tourze\TagManageBundle\Entity\Tag;
use Yiisoft\Arrays\ArrayHelper;

#[MethodTag(name: '内容管理')]
#[MethodDoc(summary: '拉取CMS内容列表')]
#[MethodExpose(method: 'GetCmsEntityList')]
final class GetCmsEntityList extends BaseProcedure
{
    use PaginatorTrait;

    public function __construct(
        private readonly CatalogService $catalogService,
        private readonly ModelService $modelService,
        private readonly EntityService $entityService,
        private readonly ContentService $contentService,
        private readonly StatService $statService,
        private readonly Security $security,
        private readonly LikeServiceInterface $likeService,
        private readonly CollectServiceInterface $collectService,
    ) {
    }

    /**
     * @phpstan-param GetCmsEntityListParam $param
     */
    public function execute(GetCmsEntityListParam|RpcParamInterface $param): ArrayResult
    {
        $user = $this->security->getUser();
        $qb = $this->entityService->createPublishedEntitiesQueryBuilder();

        // 查找指定目录
        if (null !== $param->catalogId) {
            $catalogs = $this->catalogService->findBy(['id' => $param->catalogId]);
            if ([] === $catalogs) {
                throw new ApiException('目录不存在');
            }
            $qb->innerJoin('a.catalogs', 'c');
            $qb->andWhere('c.id IN (:catalogs)');
            $qb->setParameter('catalogs', ArrayHelper::getColumn($catalogs, fn (Catalog $catalog) => $catalog->getId()));
        }

        if ('' !== $param->keyword) {
            $qb->andWhere('a.title LIKE :title')->setParameter('title', "%{$param->keyword}%");
        }

        // 查找指定模型
        if (null !== $param->modelCode) {
            $models = $this->modelService->findBy(['code' => $param->modelCode]);
            $qb->innerJoin('a.model', 'm');
            $qb->andWhere('m.id IN (:models)');
            $qb->setParameter('models', ArrayHelper::getColumn($models, fn (Model $category) => $category->getId()));

            if ('' !== $param->keyword) {
                foreach ($models as $model) {
                    $this->contentService->searchByKeyword($qb, $param->keyword, $model);
                }
            }
        }

        return new ArrayResult($this->fetchList($qb, fn (Entity $item) => $this->format($item, $user), null, $param));
    }

    /**
     * @return array<string, mixed>
     */
    private function format(Entity $item, mixed $user): array
    {
        $visitTotal = $this->statService->getVisitTotal($item);
        $isLike = null !== $user && $this->likeService->isLikedByUser($item, $user);
        $isCollected = null !== $user && $this->collectService->isCollectedByUser($item, $user);

        return [
            'id' => $item->getId(),
            'title' => $item->getTitle(),
            'model' => $this->formatModel($item->getModel()),
            'catalogs' => $this->formatCatalogs($item->getCatalogs()->toArray()),
            'tags' => $this->formatTags($item->getTags()->toArray()),
            'valueList' => $this->formatValueList($item->getValueList()->toArray()),
            'publishTime' => $item->getPublishTime()?->format('c'),
            'endTime' => $item->getEndTime()?->format('c'),
            'state' => $item->getState()->value,
            'values' => $item->getValues(),
            'visitTotal' => $visitTotal,
            'isLike' => $isLike,
            'isCollected' => $isCollected,
        ];
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
