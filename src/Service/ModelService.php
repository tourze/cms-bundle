<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\Service;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\CmsBundle\Entity\Model;
use Tourze\CmsBundle\Repository\ModelRepository;

/**
 * 模型服务类.
 *
 * 为其他模块提供模型查询功能，避免直接调用 Repository
 */
#[Autoconfigure(public: true)]
readonly class ModelService
{
    public function __construct(
        private ModelRepository $modelRepository,
    ) {
    }

    /**
     * 通过代号查找有效模型.
     */
    public function findValidModelByCode(string $code): ?Model
    {
        return $this->modelRepository->findOneBy([
            'code' => $code,
            'valid' => true,
        ]);
    }

    /**
     * 通过ID查找有效模型.
     */
    public function findValidModelById(int $id): ?Model
    {
        return $this->modelRepository->findOneBy([
            'id' => $id,
            'valid' => true,
        ]);
    }

    /**
     * 通过条件查找模型.
     *
     * @param array<string, mixed> $criteria
     */
    public function findModelBy(array $criteria): ?Model
    {
        return $this->modelRepository->findOneBy($criteria);
    }

    /**
     * 查找所有有效模型.
     *
     * @return Model[]
     */
    public function findAllValidModels(): array
    {
        return $this->modelRepository->findBy(['valid' => true]);
    }

    /**
     * 通过条件查找多个模型.
     *
     * @param array<string, mixed>                          $criteria
     * @param array<string, 'ASC'|'asc'|'DESC'|'desc'>|null $orderBy
     *
     * @return Model[]
     */
    public function findBy(array $criteria, ?array $orderBy = null): array
    {
        return $this->modelRepository->findBy($criteria, $orderBy);
    }
}
