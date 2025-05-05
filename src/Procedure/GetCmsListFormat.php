<?php

namespace CmsBundle\Procedure;

use Symfony\Component\Security\Http\Attribute\IsGranted;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;

#[MethodTag('内容管理')]
#[MethodDoc('格式化内容列表')]
#[MethodExpose('GetCmsListFormat')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class GetCmsListFormat extends BaseProcedure
{
    #[MethodParam('文章目录')]
    public ?int $categoryId = null;

    #[MethodParam('模型代号')]
    public ?string $modelCode = null;

    #[MethodParam('搜索关键词')]
    public string $keyword = '';

    public function __construct(
        private readonly GetCmsEntityList $getCmsEntityList,
    ) {
    }

    public function execute(): array
    {
        $this->getCmsEntityList->categoryId = $this->categoryId;
        $this->getCmsEntityList->modelCode = $this->modelCode;
        $this->getCmsEntityList->keyword = $this->keyword;
        $res = $this->getCmsEntityList->execute();

        $result = [];
        foreach ($res['list'] as $re) {
            $tmp = $re;
            foreach ($re['values'] as $k => $v) {
                $tmp[$k] = $v;
            }

            $result[] = $tmp;
        }

        if (1 === count($result)) {
            $result = $result[0];
        }

        return $result;
    }
}
