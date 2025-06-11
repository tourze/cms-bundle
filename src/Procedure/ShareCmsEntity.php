<?php

namespace CmsBundle\Procedure;

use CmsBundle\Entity\ShareLog;
use CmsBundle\Enum\EntityState;
use CmsBundle\Repository\EntityRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Tourze\DoctrineAsyncInsertBundle\Service\AsyncInsertService as DoctrineService;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;

#[MethodExpose('ShareCmsEntity')]
#[MethodTag('内容管理')]
#[MethodDoc('分享指定文章')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[Log]
class ShareCmsEntity extends LockableProcedure
{
    #[MethodParam('文章ID')]
    public int $entityId;

    public function __construct(
        private readonly EntityRepository $entityRepository,
        private readonly DoctrineService $doctrineService,
        private readonly Security $security,
    ) {
    }

    public static function getMockResult(): ?array
    {
        return [
            'message' => '分享成功',
        ];
    }

    public function execute(): array
    {
        $entity = $this->entityRepository->findOneBy([
            'id' => $this->entityId,
            'state' => EntityState::PUBLISHED,
        ]);
        if (!$entity) {
            throw new ApiException('找不到文章');
        }

        $log = new ShareLog();
        $log->setEntity($entity);
        $log->setUser($this->security->getUser());
        $this->doctrineService->asyncInsert($log);

        return [
            'message' => '分享成功',
        ];
    }
}
