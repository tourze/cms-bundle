<?php

namespace CmsBundle\Procedure\Category;

use CmsBundle\Entity\Category;
use CmsBundle\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;

#[MethodTag(name: '内容分类管理')]
#[MethodDoc(summary: '创建内容分类')]
#[MethodExpose(method: 'AdminCreateCmsCategory')]
#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
#[Log]
class AdminCreateCmsCategory extends LockableProcedure
{
    #[MethodParam(description: '标题')]
    public string $title;

    #[MethodParam(description: '上级分类ID')]
    public ?string $parentId = null;

    #[MethodParam(description: '是否有效')]
    public ?bool $valid = null;

    #[MethodParam(description: '排序编号')]
    public ?int $sortNumber = 0;

    public function __construct(private readonly CategoryRepository $categoryRepository, private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(): array
    {
        $parent = null;
        if (null !== $this->parentId) {
            $parent = $this->categoryRepository->find($this->parentId);
            if ($parent === null) {
                throw new ApiException('找不到上级分类');
            }
        }

        $cate = new Category();
        $cate->setParent($parent);
        $cate->setTitle($this->title);
        $cate->setValid($this->valid);
        $cate->setSortNumber($this->sortNumber);
        $this->entityManager->persist($cate);
        $this->entityManager->flush();

        return [
            'id' => $cate->getId(),
            '__message' => '创建成功',
        ];
    }
}
