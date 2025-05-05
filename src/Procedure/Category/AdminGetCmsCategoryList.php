<?php

namespace CmsBundle\Procedure\Category;

use CmsBundle\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Tourze\DoctrineHelper\CacheHelper;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Model\JsonRpcRequest;
use Tourze\JsonRPCCacheBundle\Procedure\CacheableProcedure;
use Tourze\JsonRPCPaginatorBundle\Procedure\PaginatorTrait;

#[MethodTag('内容分类管理')]
#[MethodDoc('获取所有内容分类')]
#[MethodExpose('AdminGetCmsCategoryList')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class AdminGetCmsCategoryList extends CacheableProcedure
{
    use PaginatorTrait;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security,
    ) {
    }

    public function execute(): array
    {
        $qb = $this->entityManager
            ->createQueryBuilder()
            ->from(Category::class, 'a')
            ->select('a');

        return $this->fetchList($qb, $this->formatItem(...));
    }

    protected function getCacheKey(JsonRpcRequest $request): string
    {
        $key = static::buildParamCacheKey($request->getParams());
        if ($this->security->getUser()) {
            $key .= '-' . $this->security->getUser()->getUserIdentifier();
        }

        return $key;
    }

    protected function getCacheDuration(JsonRpcRequest $request): int
    {
        return 60;
    }

    protected function getCacheTags(JsonRpcRequest $request): iterable
    {
        yield CacheHelper::getClassTags(Category::class);
    }

    private function formatItem(Category $category): array
    {
        return $category->retrieveAdminArray();
    }
}
