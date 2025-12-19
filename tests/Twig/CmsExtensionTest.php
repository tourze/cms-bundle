<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\Tests\Twig;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\CmsBundle\Entity\Entity;
use Tourze\CmsBundle\Entity\Model;
use Tourze\CmsBundle\Enum\EntityState;
use Tourze\CmsBundle\Exception\ModelNotFoundException;
use Tourze\CmsBundle\Twig\CmsExtension;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use Twig\Attribute\AsTwigFunction;

/**
 * @internal
 */
#[CoversClass(CmsExtension::class)]
#[RunTestsInSeparateProcesses]
final class CmsExtensionTest extends AbstractIntegrationTestCase
{
    private CmsExtension $extension;

    public function testExtensionHasTwigFunctions(): void
    {
        $reflection = new \ReflectionClass($this->extension);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        $twigFunctionMethods = [];
        foreach ($methods as $method) {
            $attributes = $method->getAttributes(AsTwigFunction::class);
            if ([] !== $attributes) {
                $twigFunctionMethods[] = $method->getName();
            }
        }

        $this->assertCount(2, $twigFunctionMethods);
        $this->assertContains('getCmsEntityDetail', $twigFunctionMethods);
        $this->assertContains('getCmsEntityList', $twigFunctionMethods);
    }

    public function testGetCmsEntityDetail(): void
    {
        $model = new Model();
        $model->setTitle('Test Model');
        $model->setCode('test_model');
        $this->persistAndFlush($model);

        $entity = new Entity();
        $entity->setTitle('Test Article');
        $entity->setState(EntityState::PUBLISHED);
        $entity->setModel($model);
        $this->persistAndFlush($entity);

        $result = $this->extension->getCmsEntityDetail((string) $entity->getId());

        $this->assertSame($entity, $result);
    }

    public function testGetCmsEntityDetailReturnsNull(): void
    {
        $result = $this->extension->getCmsEntityDetail('non-existent-id');

        $this->assertNull($result);
    }

    public function testGetCmsEntityList(): void
    {
        $model = new Model();
        $model->setTitle('Test Model');
        $model->setCode('test-model');
        $this->persistAndFlush($model);

        $entity1 = new Entity();
        $entity1->setTitle('Test Article 1');
        $entity1->setState(EntityState::PUBLISHED);
        $entity1->setModel($model);
        $this->persistAndFlush($entity1);

        $entity2 = new Entity();
        $entity2->setTitle('Test Article 2');
        $entity2->setState(EntityState::PUBLISHED);
        $entity2->setModel($model);
        $this->persistAndFlush($entity2);

        $result = $this->extension->getCmsEntityList('test-model', 10, 0);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        // 确保返回的是Entity对象数组
        foreach ($result as $item) {
            $this->assertInstanceOf(Entity::class, $item);
        }
    }

    public function testGetCmsEntityListWithDefaults(): void
    {
        $model = new Model();
        $model->setTitle('Test Model');
        $model->setCode('test-model');
        $this->persistAndFlush($model);

        $entity = new Entity();
        $entity->setTitle('Default Test Article');
        $entity->setState(EntityState::PUBLISHED);
        $entity->setModel($model);
        $this->persistAndFlush($entity);

        $result = $this->extension->getCmsEntityList('test-model');

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        // 确保返回的是Entity对象数组
        foreach ($result as $item) {
            $this->assertInstanceOf(Entity::class, $item);
        }
    }

    public function testGetCmsEntityListThrowsExceptionWhenModelNotFound(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage('找不到指定CMS模型: non-existent-model');

        $this->extension->getCmsEntityList('non-existent-model');
    }

    protected function onSetUp(): void
    {
        $this->extension = self::getService(CmsExtension::class);
    }
}
