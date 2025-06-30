<?php

namespace CmsBundle\Procedure\Category;

use CmsBundle\Repository\CategoryRepository;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;

#[MethodTag(name: '内容管理')]
#[MethodDoc(summary: '获取目录详情')]
#[MethodExpose(method: 'GetCmsCategoryDetail')]
class GetCmsCategoryDetail extends BaseProcedure
{
    #[MethodParam(description: '分类ID')]
    public ?string $categoryId = null;

    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly NormalizerInterface $normalizer,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(): array
    {
        $category = $this->categoryRepository->findOneBy([
            'id' => $this->categoryId,
            'valid' => true,
        ]);
        if ($category === null) {
            throw new ApiException('找不到指定目录');
        }

        $result = $this->normalizer->normalize($category, 'array', ['group' => 'restful_read']);
        if (!is_array($result)) {
            throw new ApiException('序列化失败');
        }
        return $result;
    }
}
