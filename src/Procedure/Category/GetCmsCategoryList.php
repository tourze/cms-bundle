<?php

namespace CmsBundle\Procedure\Category;

use CmsBundle\Entity\Category;
use CmsBundle\Repository\CategoryRepository;
use CmsBundle\Repository\ModelRepository;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Tourze\JsonRPCPaginatorBundle\Procedure\PaginatorTrait;

#[MethodTag(name: '内容管理')]
#[MethodDoc(summary: '获取目录')]
#[MethodExpose(method: 'GetCmsCategoryList')]
class GetCmsCategoryList extends BaseProcedure
{
    use PaginatorTrait;

    #[MethodParam(description: '模型code')]
    public ?string $modelCode = null;

    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly ModelRepository $modelRepository,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(): array
    {
        $qb = $this->categoryRepository->createQueryBuilder('a')
            ->where('a.valid = true')
            ->orderBy('a.id', 'DESC');

        if ($this->modelCode !== null) {
            $model = $this->modelRepository->findOneBy([
                'code' => $this->modelCode,
            ]);
            if ($model === null) {
                throw new ApiException('找不到指定模型');
            }
            $qb->andWhere('a.model = :model');
            $qb->setParameter('model', $model);
        }

        return $this->fetchList($qb, $this->formatItem(...));
    }

    /**
     * @return array<string, mixed>
     */
    private function formatItem(Category $item): array
    {
        return $item->retrieveApiArray();
    }
}
