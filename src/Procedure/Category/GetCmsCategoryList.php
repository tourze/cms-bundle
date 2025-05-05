<?php

namespace CmsBundle\Procedure\Category;

use CmsBundle\Entity\Category;
use CmsBundle\Repository\CategoryRepository;
use CmsBundle\Repository\ModelRepository;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Tourze\JsonRPCPaginatorBundle\Procedure\PaginatorTrait;

#[MethodTag('内容管理')]
#[MethodDoc('获取目录')]
#[MethodExpose('GetCmsCategoryList')]
class GetCmsCategoryList extends BaseProcedure
{
    use PaginatorTrait;

    #[MethodParam('模型code')]
    public ?string $modelCode = null;

    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly ModelRepository $modelRepository,
        private readonly NormalizerInterface $normalizer,
    ) {
    }

    public function execute(): array
    {
        $qb = $this->categoryRepository->createQueryBuilder('a')
            ->where('a.valid = true')
            ->orderBy('a.id', 'DESC');

        if ($this->modelCode) {
            $model = $this->modelRepository->findOneBy([
                'code' => $this->modelCode,
            ]);
            if (!$model) {
                throw new ApiException('找不到指定模型');
            }
            $qb->andWhere('a.model = :model');
            $qb->setParameter('model', $model);
        }

        return $this->fetchList($qb, $this->formatItem(...));
    }

    private function formatItem(Category $item): array
    {
        return $item->retrieveApiArray();
    }
}
