<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\Tests\Repository;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\CmsBundle\Entity\Model;
use Tourze\CmsBundle\Repository\ModelRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(ModelRepository::class)]
#[RunTestsInSeparateProcesses]
final class ModelRepositoryTest extends AbstractRepositoryTestCase
{
    private ModelRepository $repository;

    /**
     * 测试find方法查找存在的实体.
     */

    /**
     * 测试findAll方法当有记录时返回实体数组.
     */

    /**
     * 测试findOneBy方法匹配条件时返回实体.
     */

    /**
     * 测试findOneBy方法排序逻辑.
     */

    /**
     * 测试findOneBy方法IS NULL查询 - sortNumber字段.
     */

    /**
     * 测试findBy方法匹配条件时返回实体数组.
     */

    /**
     * 测试findBy方法排序逻辑.
     */

    /**
     * 测试findBy方法分页参数.
     */

    /**
     * 测试findBy方法IS NULL查询 - sortNumber字段返回所有匹配实体.
     */

    /**
     * 测试count方法IS NULL查询 - sortNumber字段.
     */

    /**
     * 测试save方法.
     */
    public function testSaveMethodShouldPersistEntity(): void
    {
        $model = new Model();
        $model->setCode('test_save_method_'.uniqid());
        $model->setTitle('Test Save Method');
        $model->setValid(true);

        $this->repository->save($model);

        $this->assertNotNull($model->getId());

        $savedModel = $this->repository->find($model->getId());
        $this->assertInstanceOf(Model::class, $savedModel);
        $this->assertSame($model->getCode(), $savedModel->getCode());
    }

    /**
     * 测试save方法不刷新.
     */
    public function testSaveMethodWithoutFlush(): void
    {
        $model = new Model();
        $model->setCode('test_save_no_flush_'.uniqid());
        $model->setTitle('Test Save No Flush');
        $model->setValid(true);

        $this->repository->save($model, false);

        $entityManager = self::getService(EntityManagerInterface::class);
        $entityManager->flush();

        $this->assertNotNull($model->getId());
    }

    /**
     * 测试remove方法.
     */
    public function testRemoveMethodShouldDeleteEntity(): void
    {
        $model = new Model();
        $model->setCode('test_remove_method_'.uniqid());
        $model->setTitle('Test Remove Method');
        $model->setValid(true);
        $this->repository->save($model);

        $modelId = $model->getId();
        $this->repository->remove($model);

        $deletedModel = $this->repository->find($modelId);
        $this->assertNull($deletedModel);
    }

    /**
     * 测试remove方法不刷新.
     */
    public function testRemoveMethodWithoutFlush(): void
    {
        $model = new Model();
        $model->setCode('test_remove_no_flush_'.uniqid());
        $model->setTitle('Test Remove No Flush');
        $model->setValid(true);
        $this->repository->save($model);

        $modelId = $model->getId();
        $this->repository->remove($model, false);

        $entityManager = self::getService(EntityManagerInterface::class);
        $entityManager->flush();

        $deletedModel = $this->repository->find($modelId);
        $this->assertNull($deletedModel);
    }

    /**
     * 测试可空字段的IS NULL查询 - contentSorts字段.
     */
    public function testFindByWithContentSortsFieldIsNull(): void
    {
        $model = new Model();
        $model->setCode('test_null_content_sorts_'.uniqid());
        $model->setTitle('Test Null Content Sorts');
        $model->setValid(true);
        $model->setContentSorts(null);
        $this->repository->save($model);

        $result = $this->repository->findBy(['contentSorts' => null]);

        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, \count($result));
    }

    /**
     * 测试可空字段的count IS NULL查询 - contentSorts字段.
     */
    public function testCountWithContentSortsFieldIsNull(): void
    {
        $model = new Model();
        $model->setCode('test_count_content_sorts_null_'.uniqid());
        $model->setTitle('Test Count Content Sorts Null');
        $model->setValid(true);
        $model->setContentSorts(null);
        $this->repository->save($model);

        $result = $this->repository->count(['contentSorts' => null]);

        $this->assertIsInt($result);
        $this->assertGreaterThanOrEqual(1, $result);
    }

    /**
     * 测试可空字段的IS NULL查询 - topicSorts字段.
     */
    public function testFindByWithTopicSortsFieldIsNull(): void
    {
        $model = new Model();
        $model->setCode('test_null_topic_sorts_'.uniqid());
        $model->setTitle('Test Null Topic Sorts');
        $model->setValid(true);
        $model->setTopicSorts(null);
        $this->repository->save($model);

        $result = $this->repository->findBy(['topicSorts' => null]);

        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, \count($result));
    }

    /**
     * 测试可空字段的count IS NULL查询 - topicSorts字段.
     */
    public function testCountWithTopicSortsFieldIsNull(): void
    {
        $model = new Model();
        $model->setCode('test_count_topic_sorts_null_'.uniqid());
        $model->setTitle('Test Count Topic Sorts Null');
        $model->setValid(true);
        $model->setTopicSorts(null);
        $this->repository->save($model);

        $result = $this->repository->count(['topicSorts' => null]);

        $this->assertIsInt($result);
        $this->assertGreaterThanOrEqual(1, $result);
    }

    /**
     * 测试可空字段的IS NULL查询 - valid字段.
     */
    public function testFindByWithValidFieldIsNull(): void
    {
        $model = new Model();
        $model->setCode('test_null_valid_'.uniqid());
        $model->setTitle('Test Null Valid');
        $model->setValid(null);
        $this->repository->save($model);

        $result = $this->repository->findBy(['valid' => null]);

        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, \count($result));
    }

    /**
     * 测试可空字段的count IS NULL查询 - valid字段.
     */
    public function testCountWithValidFieldIsNull(): void
    {
        $model = new Model();
        $model->setCode('test_count_valid_null_'.uniqid());
        $model->setTitle('Test Count Valid Null');
        $model->setValid(null);
        $this->repository->save($model);

        $result = $this->repository->count(['valid' => null]);

        $this->assertIsInt($result);
        $this->assertGreaterThanOrEqual(1, $result);
    }

    /**
     * 测试关联查询 - 通过attributes关联查询.
     */
    public function testFindByWithAssociationQuery(): void
    {
        $model = new Model();
        $model->setCode('test_association_'.uniqid());
        $model->setTitle('Test Association Model');
        $model->setValid(true);
        $this->repository->save($model);

        $qb = $this->repository->createQueryBuilder('m')
            ->leftJoin('m.attributes', 'a')
            ->where('m.valid = :valid')
            ->setParameter('valid', true)
        ;

        $result = $qb->getQuery()->getResult();

        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, \count($result));
        $this->assertContainsOnlyInstancesOf(Model::class, $result);
    }

    /**
     * 测试关联查询 - 通过entities关联查询.
     */
    public function testFindByWithEntitiesAssociationQuery(): void
    {
        $model = new Model();
        $model->setCode('test_entities_association_'.uniqid());
        $model->setTitle('Test Entities Association Model');
        $model->setValid(true);
        $this->repository->save($model);

        $qb = $this->repository->createQueryBuilder('m')
            ->leftJoin('m.entities', 'e')
            ->where('m.valid = :valid')
            ->setParameter('valid', true)
        ;

        $result = $qb->getQuery()->getResult();

        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, \count($result));
        $this->assertContainsOnlyInstancesOf(Model::class, $result);
    }

    /**
     * 测试复杂查询 - 多条件组合查询.
     */
    public function testComplexQueryWithMultipleCriteria(): void
    {
        $model = new Model();
        $model->setCode('test_complex_'.uniqid());
        $model->setTitle('Test Complex Query');
        $model->setValid(true);
        $model->setAllowLike(true);
        $model->setAllowCollect(false);
        $this->repository->save($model);

        $result = $this->repository->findBy([
            'valid' => true,
            'allowLike' => true,
            'allowCollect' => false,
        ]);

        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, \count($result));
        $this->assertContainsOnlyInstancesOf(Model::class, $result);
    }

    protected function createNewEntity(): object
    {
        $model = new Model();
        $model->setCode('test-model-'.uniqid());
        $model->setTitle('Test Model '.uniqid());
        $model->setValid(true);

        return $model;
    }

    protected function onSetUp(): void
    {
        $this->repository = self::getService(ModelRepository::class);

        // 检查当前测试是否需要特殊处理
        $currentTest = $this->name();
        if ('testFindAllWhenNoRecordsExistShouldReturnEmptyArray' === $currentTest) {
            // 清理所有Model数据以确保测试在空数据库上运行
            $em = self::getService(EntityManagerInterface::class);
            $em->createQuery('DELETE FROM '.Model::class)->execute();
        } elseif ('testCountWithDataFixtureShouldReturnGreaterThanZero' === $currentTest) {
            // 为计数测试创建测试数据
            $this->createTestDataForCountTest();
        }
    }

    protected function getRepository(): ModelRepository
    {
        return $this->repository;
    }

    private function createTestDataForCountTest(): void
    {
        $model = new Model();
        $model->setCode('test-model-'.uniqid());
        $model->setTitle('Test Model '.uniqid());
        $model->setValid(true);
        $this->repository->save($model);
    }
}
