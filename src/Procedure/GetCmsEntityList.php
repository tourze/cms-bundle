<?php

declare(strict_types=1);

namespace CmsBundle\Procedure;

use CmsBundle\Entity\Entity;
use CmsBundle\Entity\Model;
use CmsBundle\Service\ContentService;
use CmsBundle\Service\EntityService;
use CmsBundle\Service\ModelService;
use CmsBundle\Service\StatService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Tourze\CatalogBundle\Entity\Catalog;
use Tourze\CatalogBundle\Service\CatalogService;
use Tourze\CmsLikeBundle\Service\LikeService;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Tourze\JsonRPCPaginatorBundle\Procedure\PaginatorTrait;
use Yiisoft\Arrays\ArrayHelper;

#[MethodTag(name: '内容管理')]
#[MethodDoc(summary: '拉取CMS内容列表')]
#[MethodExpose(method: 'GetCmsEntityList')]
class GetCmsEntityList extends BaseProcedure
{
    use PaginatorTrait;

    #[MethodParam(description: '文章目录')]
    public string|int|null $catalogId = null;

    #[MethodParam(description: '模型代号')]
    public ?string $modelCode = null;

    #[MethodParam(description: '搜索关键词')]
    public string $keyword = '';

    public function __construct(
        private readonly CatalogService $catalogService,
        private readonly ModelService $modelService,
        private readonly EntityService $entityService,
        private readonly NormalizerInterface $normalizer,
        private readonly ContentService $contentService,
        private readonly StatService $statService,
        private readonly Security $security,
        private readonly LikeService $likeService,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(): array
    {
        $user = $this->security->getUser();
        $qb = $this->entityService->createPublishedEntitiesQueryBuilder();

        // 查找指定目录
        if (null !== $this->catalogId) {
            $catalogs = $this->catalogService->findBy(['id' => $this->catalogId]);
            if ([] === $catalogs) {
                throw new ApiException('目录不存在');
            }
            $qb->innerJoin('a.catalogs', 'c');
            $qb->andWhere('c.id IN (:catalogs)');
            $qb->setParameter('catalogs', ArrayHelper::getColumn($catalogs, fn (Catalog $catalog) => $catalog->getId()));
        }

        if ('' !== $this->keyword) {
            $qb->andWhere('a.title LIKE :title')->setParameter('title', "%{$this->keyword}%");
        }

        // 查找指定模型
        if (null !== $this->modelCode) {
            $models = $this->modelService->findBy(['code' => $this->modelCode]);
            $qb->innerJoin('a.model', 'm');
            $qb->andWhere('m.id IN (:models)');
            $qb->setParameter('models', ArrayHelper::getColumn($models, fn (Model $category) => $category->getId()));

            if ('' !== $this->keyword) {
                foreach ($models as $model) {
                    $this->contentService->searchByKeyword($qb, $this->keyword, $model);
                }
            }
        }

        return $this->fetchList($qb, fn (Entity $item) => $this->format($item, $user));
    }

    /**
     * @return array<string, mixed>
     */
    private function format(Entity $item, mixed $user): array
    {
        $visitTotal = $this->statService->getVisitTotal($item);
        $isLike = false;
        if (null !== $user) {
            $log = $this->likeService->findLikeLogBy([
                'entity' => $item,
                'valid' => true,
                'user' => $this->security->getUser(),
            ]);
            $isLike = (bool) $log;
        }

        $normalized = $this->normalizer->normalize($item, 'array', ['groups' => 'restful_read']);
        if (!\is_array($normalized)) {
            $normalized = [];
        }
        /** @var array<string, mixed> $result */
        $result = $normalized;
        $result['visitTotal'] = $visitTotal;
        $result['isLike'] = $isLike;

        return $result;
    }
}
