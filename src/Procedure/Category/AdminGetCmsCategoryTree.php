<?php

namespace CmsBundle\Procedure\Category;

use CmsBundle\Entity\Category;
use CmsBundle\Repository\CategoryRepository;
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
#[MethodDoc('获取树形结构的内容分类')]
#[MethodExpose('AdminGetCmsCategoryTree')]
#[IsGranted('ROLE_OPERATOR')]
class AdminGetCmsCategoryTree extends CacheableProcedure
{
    use PaginatorTrait;

    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly Security $security,
    ) {
    }

    public function execute(): array
    {
        $parents = $this->categoryRepository->findBy(['parent' => null]);
        $result = [];
        foreach ($parents as $parent) {
            $result[] = $parent->retrieveAdminTreeArray();
        }

        return $result;
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
        return MINUTE_IN_SECONDS;
    }

    protected function getCacheTags(JsonRpcRequest $request): iterable
    {
        yield CacheHelper::getClassTags(Category::class);
    }
}
