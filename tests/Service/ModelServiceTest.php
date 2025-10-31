<?php

declare(strict_types=1);

namespace CmsBundle\Tests\Service;

use CmsBundle\Entity\Model;
use CmsBundle\Service\ModelService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(ModelService::class)]
#[RunTestsInSeparateProcesses]
final class ModelServiceTest extends AbstractIntegrationTestCase
{
    public function testServiceCreation(): void
    {
        $modelService = self::getService(ModelService::class);
        $this->assertInstanceOf(ModelService::class, $modelService);
    }

    public function testFindValidModelByCode(): void
    {
        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = self::getEntityManager();

        // Create test model
        $model = new Model();
        $model->setCode('test-code');
        $model->setTitle('Test Model');
        $model->setValid(true);
        $entityManager->persist($model);
        $entityManager->flush();

        $modelService = self::getService(ModelService::class);
        $result = $modelService->findValidModelByCode('test-code');

        $this->assertSame($model, $result);
    }

    public function testFindValidModelByCodeReturnsNull(): void
    {
        $modelService = self::getService(ModelService::class);
        $result = $modelService->findValidModelByCode('non-existent');

        $this->assertNull($result);
    }

    public function testFindValidModelByCodeWithInvalidModel(): void
    {
        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = self::getEntityManager();

        // Create invalid model
        $model = new Model();
        $model->setCode('invalid-model');
        $model->setTitle('Invalid Model');
        $model->setValid(false);
        $entityManager->persist($model);
        $entityManager->flush();

        $modelService = self::getService(ModelService::class);
        $result = $modelService->findValidModelByCode('invalid-model');

        $this->assertNull($result);
    }

    public function testFindValidModelById(): void
    {
        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = self::getEntityManager();

        // Create test model
        $model = new Model();
        $model->setCode('test-code');
        $model->setTitle('Test Model');
        $model->setValid(true);
        $entityManager->persist($model);
        $entityManager->flush();

        $modelService = self::getService(ModelService::class);
        $result = $modelService->findValidModelById((int) $model->getId());

        $this->assertSame($model, $result);
    }

    public function testFindModelBy(): void
    {
        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = self::getEntityManager();

        // Create test model
        $model = new Model();
        $model->setCode('test-code');
        $model->setTitle('Test Model');
        $model->setValid(true);
        $entityManager->persist($model);
        $entityManager->flush();

        $modelService = self::getService(ModelService::class);
        $result = $modelService->findModelBy(['code' => 'test-code']);

        $this->assertSame($model, $result);
    }

    public function testFindAllValidModels(): void
    {
        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = self::getEntityManager();
        $modelService = self::getService(ModelService::class);

        // 记录添加测试数据前的有效模型数量
        $initialValidCount = \count($modelService->findAllValidModels());

        // Create test models
        $model1 = new Model();
        $model1->setCode('test-code-1');
        $model1->setTitle('Test Model 1');
        $model1->setValid(true);
        $entityManager->persist($model1);

        $model2 = new Model();
        $model2->setCode('test-code-2');
        $model2->setTitle('Test Model 2');
        $model2->setValid(true);
        $entityManager->persist($model2);

        $invalidModel = new Model();
        $invalidModel->setCode('invalid-model');
        $invalidModel->setTitle('Invalid Model');
        $invalidModel->setValid(false);
        $entityManager->persist($invalidModel);

        $entityManager->flush();

        $results = $modelService->findAllValidModels();

        // 验证有效模型数量增加了2个（我们添加的两个有效模型）
        $this->assertCount($initialValidCount + 2, $results);

        // 验证我们添加的模型在结果中
        $resultCodes = array_map(fn (Model $model) => $model->getCode(), $results);
        $this->assertContains('test-code-1', $resultCodes);
        $this->assertContains('test-code-2', $resultCodes);
        $this->assertNotContains('invalid-model', $resultCodes);
    }

    public function testFindBy(): void
    {
        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = self::getEntityManager();

        // Create test model
        $model = new Model();
        $model->setCode('test-code');
        $model->setTitle('Test Model');
        $model->setValid(true);
        $entityManager->persist($model);
        $entityManager->flush();

        $modelService = self::getService(ModelService::class);
        $results = $modelService->findBy(['valid' => true]);

        $this->assertGreaterThanOrEqual(1, \count($results));
    }

    protected function onSetUp(): void
    {
        // 必要的设置
    }
}
