<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\CmsBundle\Entity\Entity;
use Tourze\CmsBundle\Enum\EntityState;
use Tourze\CmsBundle\Service\LikeServiceInterface;
use Tourze\CmsBundle\Service\NullLikeService;

/**
 * @internal
 */
#[CoversClass(NullLikeService::class)]
final class NullLikeServiceTest extends TestCase
{
    private NullLikeService $service;

    protected function setUp(): void
    {
        $this->service = new NullLikeService();
    }

    public function testImplementsLikeServiceInterface(): void
    {
        $this->assertInstanceOf(LikeServiceInterface::class, $this->service);
    }

    public function testIsLikedByUserReturnsFalse(): void
    {
        // 使用真实的Entity实例而不是Mock
        $entity = new Entity();
        $entity->setTitle('Test Entity');
        $entity->setState(EntityState::DRAFT);

        // UserInterface必须Mock，因为它是接口且没有具体实现
        $user = $this->createMock(UserInterface::class);

        $result = $this->service->isLikedByUser($entity, $user);

        $this->assertFalse($result);
    }
}
