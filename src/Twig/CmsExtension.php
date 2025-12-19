<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\Twig;

use Tourze\CmsBundle\Entity\Entity;
use Tourze\CmsBundle\Enum\EntityState;
use Tourze\CmsBundle\Exception\ModelNotFoundException;
use Tourze\CmsBundle\Service\EntityService;
use Tourze\CmsBundle\Service\ModelService;
use Twig\Attribute\AsTwigFunction;

readonly class CmsExtension
{
    public function __construct(
        private ModelService $modelService,
        private EntityService $entityService,
    ) {
    }

    /**
     * 获取单个文章的内容.
     */
    #[AsTwigFunction(name: 'get_cms_entity_detail')]
    public function getCmsEntityDetail(string $id): ?Entity
    {
        return $this->entityService->findEntityBy([
            'id' => $id,
            'state' => EntityState::PUBLISHED,
        ]);
    }

    /**
     * 拉取指定模型的实体列表.
     */
    /**
     * @return array<Entity>
     */
    #[AsTwigFunction(name: 'get_cms_entity_list')]
    public function getCmsEntityList(string $modelCode, int $limit = 20, int $offset = 0): array
    {
        $model = $this->modelService->findModelBy(['code' => $modelCode]);
        if (null === $model) {
            throw new ModelNotFoundException($modelCode);
        }

        return $this->entityService->findPublishedEntitiesByModel($model, $limit, $offset);
    }
}
