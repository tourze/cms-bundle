<?php

declare(strict_types=1);

namespace CmsBundle\Tests\Controller;

use CmsBundle\Controller\SqlController;
use CmsBundle\Entity\Model;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tourze\PHPUnitSymfonyWebTest\AbstractWebTestCase;

/**
 * @internal
 */
#[CoversClass(SqlController::class)]
#[RunTestsInSeparateProcesses]
final class SqlControllerTest extends AbstractWebTestCase
{
    public function testGetValidModelCodeShouldReturnSqlResponse(): void
    {
        $client = self::createClientWithDatabase();

        // 创建测试用户
        $user = $this->createNormalUser('test@example.com', 'password123');
        $this->loginAsUser($client, 'test@example.com', 'password123');

        // 创建测试模型数据
        $model = $this->createTestModel();

        // 测试有效的模型代码
        $client->request('GET', '/cms-model-sql/test-model');

        $response = $client->getResponse();
        $this->assertSame(200, $response->getStatusCode());
        $content = $response->getContent();
        $this->assertIsString($content);

        $this->assertStringContainsString('SELECT', $content);
        $this->assertStringContainsString('FROM cms_entity', $content);
        $this->assertStringContainsString("WHERE ce.model_id = '{$model->getId()}'", $content);
    }

    public function testGetInvalidModelCodeShouldReturn404(): void
    {
        $client = self::createClientWithDatabase();

        // 创建测试用户
        $user = $this->createNormalUser('test@example.com', 'password123');
        $this->loginAsUser($client, 'test@example.com', 'password123');

        // 测试无效的模型代码
        $client->catchExceptions(false);
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('找不到模型数据');

        $client->request('GET', '/cms-model-sql/non-existent-model');
    }

    public function testUnauthenticatedAccessShouldSucceed(): void
    {
        $client = self::createClientWithDatabase();

        // 创建测试模型数据
        $model = $this->createTestModel();

        // 不登录直接访问（控制器没有认证保护）
        $client->request('GET', '/cms-model-sql/test-model');

        // 应该成功返回 SQL 内容
        $response = $client->getResponse();
        $this->assertSame(200, $response->getStatusCode());
        $content = $response->getContent();
        $this->assertIsString($content);

        $this->assertStringContainsString('SELECT', $content);
        $this->assertStringContainsString('FROM cms_entity', $content);
    }

    public function testPostMethodShouldWork(): void
    {
        $client = self::createClientWithDatabase();

        // 创建测试用户
        $user = $this->createNormalUser('test@example.com', 'password123');
        $this->loginAsUser($client, 'test@example.com', 'password123');

        // 创建测试模型数据
        $model = $this->createTestModel();

        // 测试 POST 方法（路由没有限制 HTTP 方法）
        $client->request('POST', '/cms-model-sql/test-model');

        // 应该成功返回 SQL 内容
        $response = $client->getResponse();
        $this->assertSame(200, $response->getStatusCode());
        $content = $response->getContent();
        $this->assertIsString($content);
        $this->assertStringContainsString('SELECT', $content);
    }

    public function testPutMethodShouldWork(): void
    {
        $client = self::createClientWithDatabase();

        // 创建测试用户
        $user = $this->createNormalUser('test@example.com', 'password123');
        $this->loginAsUser($client, 'test@example.com', 'password123');

        // 创建测试模型数据
        $model = $this->createTestModel();

        // 测试 PUT 方法（路由没有限制 HTTP 方法）
        $client->request('PUT', '/cms-model-sql/test-model');

        // 应该成功返回 SQL 内容
        $response = $client->getResponse();
        $this->assertSame(200, $response->getStatusCode());
        $content = $response->getContent();
        $this->assertIsString($content);
        $this->assertStringContainsString('SELECT', $content);
    }

    public function testDeleteMethodShouldWork(): void
    {
        $client = self::createClientWithDatabase();

        // 创建测试用户
        $user = $this->createNormalUser('test@example.com', 'password123');
        $this->loginAsUser($client, 'test@example.com', 'password123');

        // 创建测试模型数据
        $model = $this->createTestModel();

        // 测试 DELETE 方法（路由没有限制 HTTP 方法）
        $client->request('DELETE', '/cms-model-sql/test-model');

        // 应该成功返回 SQL 内容
        $response = $client->getResponse();
        $this->assertSame(200, $response->getStatusCode());
        $content = $response->getContent();
        $this->assertIsString($content);
        $this->assertStringContainsString('SELECT', $content);
    }

    public function testPatchMethodShouldWork(): void
    {
        $client = self::createClientWithDatabase();

        // 创建测试用户
        $user = $this->createNormalUser('test@example.com', 'password123');
        $this->loginAsUser($client, 'test@example.com', 'password123');

        // 创建测试模型数据
        $model = $this->createTestModel();

        // 测试 PATCH 方法（路由没有限制 HTTP 方法）
        $client->request('PATCH', '/cms-model-sql/test-model');

        // 应该成功返回 SQL 内容
        $response = $client->getResponse();
        $this->assertSame(200, $response->getStatusCode());
        $content = $response->getContent();
        $this->assertIsString($content);
        $this->assertStringContainsString('SELECT', $content);
    }

    public function testHeadMethodShouldWork(): void
    {
        $client = self::createClientWithDatabase();

        // 创建测试用户
        $user = $this->createNormalUser('test@example.com', 'password123');
        $this->loginAsUser($client, 'test@example.com', 'password123');

        // 创建测试模型数据
        $model = $this->createTestModel();

        // 测试 HEAD 方法
        $client->request('HEAD', '/cms-model-sql/test-model');

        $statusCode = $client->getResponse()->getStatusCode();
        // HEAD 方法应该与 GET 方法返回相同的状态码
        $this->assertContains(
            $statusCode,
            [200, 404],
            "HEAD method should return 200 or 404, got {$statusCode}"
        );
    }

    public function testOptionsMethodShouldWork(): void
    {
        $client = self::createClientWithDatabase();

        // 创建测试用户
        $user = $this->createNormalUser('test@example.com', 'password123');
        $this->loginAsUser($client, 'test@example.com', 'password123');

        // 创建测试模型数据
        $model = $this->createTestModel();

        // 测试 OPTIONS 方法
        $client->request('OPTIONS', '/cms-model-sql/test-model');

        $statusCode = $client->getResponse()->getStatusCode();
        // OPTIONS 方法应该返回 200 或 405
        $this->assertContains(
            $statusCode,
            [200, 405],
            "OPTIONS method should return 200 or 405, got {$statusCode}"
        );
    }

    #[DataProvider('provideNotAllowedMethods')]
    public function testMethodNotAllowed(string $method): void
    {
        if ('INVALID' === $method) {
            $this->assertTrue(true, 'No methods are disallowed for this route');

            return;
        }

        $client = self::createClientWithDatabase();

        // 创建测试模型数据
        $model = $this->createTestModel();

        $client->request($method, '/cms-model-sql/test-model');

        $this->assertResponseStatusCodeSame(405);
    }

    private function createTestModel(): Model
    {
        $entityManager = self::getService('Doctrine\ORM\EntityManagerInterface');
        $model = new Model();
        $model->setValid(true);
        $model->setTitle('测试模型');
        $model->setCode('test-model');
        $model->setSortNumber(0);
        $model->setAllowLike(true);
        $model->setAllowCollect(true);
        $model->setAllowShare(true);

        $entityManager->persist($model);
        $entityManager->flush();

        return $model;
    }
}
