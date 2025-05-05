<?php

namespace CmsBundle\Procedure;

use CmsBundle\Entity\Category;
use CmsBundle\Entity\Entity;
use CmsBundle\Entity\Model;
use CmsBundle\Repository\CategoryRepository;
use CmsBundle\Repository\EntityRepository;
use CmsBundle\Repository\LikeLogRepository;
use CmsBundle\Repository\ModelRepository;
use CmsBundle\Repository\VisitStatRepository;
use CmsBundle\Service\ContentService;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Tourze\JsonRPCPaginatorBundle\Procedure\PaginatorTrait;
use Yiisoft\Arrays\ArrayHelper;

#[MethodTag('内容管理')]
#[MethodDoc('拉取CMS内容列表')]
#[MethodExpose('GetCmsEntityList')]
class GetCmsEntityList extends BaseProcedure
{
    use PaginatorTrait;

    #[MethodParam('文章目录')]
    public ?int $categoryId = null;

    #[MethodParam('模型代号')]
    public ?string $modelCode = null;

    #[MethodParam('搜索关键词')]
    public string $keyword = '';

    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly ModelRepository $modelRepository,
        private readonly EntityRepository $entityRepository,
        private readonly NormalizerInterface $normalizer,
        private readonly ContentService $contentService,
        private readonly VisitStatRepository $visitStatRepository,
        private readonly Security $security,
        private readonly LikeLogRepository $likeLogRepository,
    ) {
    }

    public function execute(): array
    {
        $user = $this->security->getUser();
        $qb = $this->entityRepository
            ->createQueryBuilder('a')
            ->where("a.state = 'published'")
            ->addOrderBy('a.sortNumber', Criteria::DESC)
            ->addOrderBy('a.id', Criteria::DESC);

        // 查找指定目录
        if ($this->categoryId) {
            $categories = $this->categoryRepository->findBy(['id' => $this->categoryId]);
            $qb->innerJoin('a.categories', 'c');
            $qb->andWhere('c.id IN (:categories)');
            $qb->setParameter('categories', ArrayHelper::getColumn($categories, fn (Category $category) => $category->getId()));
        }

        if (!empty($this->keyword)) {
            $qb->andWhere('a.title LIKE :title')->setParameter('title', "%{$this->keyword}%");
        }

        // 查找指定模型
        if ($this->modelCode) {
            $models = $this->modelRepository->findBy(['code' => $this->modelCode]);
            $qb->innerJoin('a.model', 'm');
            $qb->andWhere('m.id IN (:models)');
            $qb->setParameter('models', ArrayHelper::getColumn($models, fn (Model $category) => $category->getId()));

            if (!empty($this->keyword)) {
                foreach ($models as $model) {
                    $this->contentService->searchByKeyword($qb, $this->keyword, $model);
                }
            }
        }

        return $this->fetchList($qb, fn (Entity $item) => $this->format($item, $user));
    }

    private function format(Entity $item, $user): array
    {
        $visitTotal = $this->visitStatRepository->createQueryBuilder('v')
            ->select('SUM(v.value) as visitTotal')
            ->where('v.entityId = :entityId')
            ->setParameter('entityId', $item->getId())
            ->getQuery()
            ->getSingleScalarResult();
        $isLike = false;
        if ($user) {
            $log = $this->likeLogRepository->findOneBy([
                'entity' => $item,
                'valid' => true,
                'user' => $this->security->getUser(),
            ]);
            $isLike = (bool) $log;
        }

        $result = $this->normalizer->normalize($item, 'array', ['groups' => 'restful_read']);
        $result['visitTotal'] = intval($visitTotal);
        $result['isLike'] = $isLike;

        return $result;
    }
}
