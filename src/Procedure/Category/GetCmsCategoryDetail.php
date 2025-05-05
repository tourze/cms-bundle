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

#[MethodTag('内容管理')]
#[MethodDoc('获取目录详情')]
#[MethodExpose('GetCmsCategoryDetail')]
class GetCmsCategoryDetail extends BaseProcedure
{
    #[MethodParam('分类ID')]
    public ?string $categoryId = null;

    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly NormalizerInterface $normalizer,
    ) {
    }

    public function execute(): array
    {
        $category = $this->categoryRepository->findOneBy([
            'id' => $this->categoryId,
            'valid' => true,
        ]);
        if (!$category) {
            throw new ApiException('找不到指定目录');
        }

        return $this->normalizer->normalize($category, 'array', ['group' => 'restful_read']);
    }
}
