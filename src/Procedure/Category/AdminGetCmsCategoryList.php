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

#[MethodTag(name: '内容分类管理')]
#[MethodDoc(summary: '获取所有内容分类')]
#[MethodExpose(method: 'AdminGetCmsCategoryList')]
#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
class AdminGetCmsCategoryList extends CacheableProcedure
{
    use PaginatorTrait;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(): array
    {
        $qb = $this->entityManager
            ->createQueryBuilder()
            ->from(Category::class, 'a')
            ->select('a');

        return $this->fetchList($qb, $this->formatItem(...));
    }

    public function getCacheKey(JsonRpcRequest $request): string
    {
        $key = static::buildParamCacheKey($request->getParams());
        if ($this->security->getUser() !== null) {
            $key .= '-' . $this->security->getUser()->getUserIdentifier();
        }

        return $key;
    }

    public function getCacheDuration(JsonRpcRequest $request): int
    {
        return 60;
    }

    /**
     * @return iterable<string>
     */
    public function getCacheTags(JsonRpcRequest $request): iterable
    {
        yield CacheHelper::getClassTags(Category::class);
    }

    /**
     * @return array<string, mixed>
     */
    private function formatItem(Category $category): array
    {
        return $category->retrieveAdminArray();
    }
}
