# CmsBundle 测试计划

## 测试策略

CmsBundle测试采用以下多层次测试策略：

1. **单元测试**：位于`Tests/Unit`目录，测试独立组件功能，不依赖外部服务。
2. **Procedure测试**：位于`Tests/Procedure`目录，测试JSON-RPC过程调用，使用模拟对象。
3. **集成测试**：位于`Tests/Integration`目录，测试组件间集成，使用测试内核和内存数据库。

## 依赖项解决方案

### 问题：缺少CacheInterface和其他服务依赖

问题描述：测试失败是因为缺少必要的服务依赖，特别是"Psr\SimpleCache\CacheInterface"。测试试图加载和测试依赖于JsonRPC服务的Procedure类，但测试内核没有提供这些依赖项的实现。

解决方案：

1. 创建模拟服务实现：
   - `MockSimpleCache` (实现 `CacheInterface`)
   - `MockLogger` (实现 `LoggerInterface`)
   - `MockPaginator` (实现 `PaginatorInterface`)
   - `MockLockService` (实现 `LockService`)
   - `MockSecurity` (实现 `Security`)
   - `MockPropertyAccessor` (实现 `PropertyAccessor`)
   - `MockValidator` (实现 `ValidatorInterface`)
   - `MockEventDispatcher` (实现 `EventDispatcherInterface`)

2. 在测试内核中注册服务：
   - 通过服务配置器在容器配置阶段注册服务
   - 使用容器别名确保特殊服务名称被正确解析
   - 添加必要的Bundle（JsonRPCLockBundle、LockServiceBundle）

## 测试方法

### 直接测试Procedure

这种方法绕过JSON-RPC调用机制，直接实例化和测试Procedure类：

```php
$procedure = new AdminCreateCmsCategory($categoryRepository, $entityManager);
$procedure->title = '测试分类';
$result = $procedure->execute();
```

优点：
- 简单直接，易于调试
- 减少对外部依赖的需求
- 可以精确控制测试条件

### 通过容器测试Procedure

此方法从容器获取Procedure实例，测试依赖注入是否正确：

```php
$procedure = $container->get(AdminCreateCmsCategory::class);
$procedure->title = '容器测试分类';
$result = $procedure->execute();
```

优点：
- 测试依赖注入是否正确配置
- 验证服务可以从容器中解析

### 模拟JSON-RPC调用

此方法模拟完整的JSON-RPC调用流程：

```php
$params = new JsonRpcParams(['title' => 'JsonRPC测试分类']);
$request = new JsonRpcRequest();
$request->setMethod('AdminCreateCmsCategory');
$request->setParams($params);
$result = $procedure->__invoke($request);
```

优点：
- 测试完整的调用流程，包括参数解析
- 验证LockableProcedure的加锁逻辑

## 最佳实践和建议

1. **独立测试Procedure**：不要通过完整的JsonRPC架构测试Procedure，而是直接测试它们的功能，这样可以减少测试的复杂性。

2. **使用真实的数据库**：对于集成测试，使用内存数据库进行测试，以验证实际的数据库操作。

3. **模拟外部依赖**：为外部服务创建模拟实现，确保测试的可靠性和独立性。

4. **避免使用合成服务**：使用服务别名代替合成服务，这样可以简化测试内核并提高可靠性。

5. **明确注册所需的Bundle**：确保测试内核注册了测试所需的所有Bundle。

## 已知问题

1. 由于Procedure类使用了ServiceMethodsSubscriberTrait，它们需要一个包含特定名称服务的容器实例。必须确保这些服务在容器中可用，否则会导致运行时错误。

2. LockableProcedure的依赖于多个外部服务，这增加了测试的复杂性。考虑重构设计，减少依赖或提供更简单的测试机制。

3. 有些服务可能需要更复杂的模拟逻辑，特别是LockService和Security服务。当前实现可能不足以测试所有场景。

## 测试范围

CmsBundle 提供了内容管理系统的核心功能，包括分类、模型和内容实体的管理。测试范围包括：

1. 核心实体与关系测试
2. 数据仓库方法测试
3. 服务层功能测试
4. Procedure 操作测试
5. 集成测试

## 测试进度

| 测试类型 | 状态 | 完成测试类 | 备注 |
|---------|------|-----------|------|
| 单元测试 | 进行中 | AdminCreateCmsCategoryTest | 已完成Category创建Procedure的测试 |
| 集成测试 | 进行中 | CmsBundleIntegrationTest | Bundle注册、服务注册、实体映射等基础功能 |

## 当前测试覆盖情况

### Procedure 测试

| Procedure 类 | 测试类 | 状态 |
|-------------|-------|------|
| AdminCreateCmsCategory | AdminCreateCmsCategoryTest | 完成 |

### 集成测试

集成测试验证了以下功能：

- Bundle 注册
- 服务容器配置
- 实体映射
- 数据库架构生成

## 后续计划

1. 完善所有 Procedure 的单元测试
2. 增加 Repository 层的测试
3. 增加 Service 层的测试
4. 提高测试覆盖率 