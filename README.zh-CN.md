# CMS Bundle

[English](README.md) | [中文](README.zh-CN.md)

![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-blue)
![License](https://img.shields.io/badge/license-MIT-green)
![Build Status](https://img.shields.io/badge/build-passing-brightgreen)
![Coverage](https://img.shields.io/badge/coverage-80%25-yellowgreen)

一个功能全面的 Symfony CMS (内容管理系统) 包，
提供内容管理、分类组织、标签管理和统计功能。

## 目录

- [功能特性](#功能特性)
- [安装](#安装)
- [配置](#配置)
- [使用方法](#使用方法)
  - [基础实体结构](#基础实体结构)
  - [服务类](#服务类)
  - [JSON-RPC 接口](#json-rpc-接口)
  - [事件系统](#事件系统)
  - [Twig 扩展](#twig-扩展)
- [数据库结构](#数据库结构)
- [高级用法](#高级用法)
- [测试](#测试)
- [系统要求](#系统要求)
- [贡献](#贡献)
- [许可证](#许可证)

## 功能特性

- **内容管理**: 基于 Entity-Attribute-Value (EAV) 的内容模型
- **分类系统**: 层次化内容分类管理
- **标签管理**: 灵活的标签系统和标签组
- **搜索统计**: 内容搜索日志和访问统计
- **事件系统**: 内容收藏、点赞和访问跟踪
- **JSON-RPC API**: RESTful API 接口支持
- **后台管理**: EasyAdmin 集成的后台管理界面
- **Twig 扩展**: 模板助手函数

## 安装

```bash
composer require tourze/cms-bundle
```

## 配置

在 `config/bundles.php` 中添加 bundle：

```php
return [
    // ...
    CmsBundle\CmsBundle::class => ['all' => true],
];
```

## 使用方法

### 基础实体结构

该包提供了 6 个核心实体：

- **Category**: 支持树形结构的层次化内容分类
- **SearchLog**: 搜索历史跟踪和关键词分析
- **Tag**: 具有唯一名称的内容标签系统
- **TagGroup**: 标签的组织和分组
- **Topic**: 支持推荐的内容主题/文章
- **VisitStat**: 按日期统计的访问统计

### 服务类

#### ContentService
在 EAV 属性中按关键词搜索内容：

```php
use CmsBundle\Service\ContentService;

class YourController
{
    public function __construct(
        private ContentService $contentService
    ) {}
    
    public function searchContent(string $keyword): void
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $this->contentService->searchByKeyword($queryBuilder, $keyword);
    }
}
```

#### StatService
异步更新访问统计：

```php
use CmsBundle\Service\StatService;

class YourController
{
    public function __construct(
        private StatService $statService
    ) {}
    
    public function updateStats(int $entityId): void
    {
        $this->statService->updateStat($entityId);
    }
}
```

### JSON-RPC 接口

该包提供以下 JSON-RPC 端点：

#### 内容操作
- `GetCmsEntityList`: 获取支持分类/模型过滤的分页内容列表
- `GetCmsEntityDetail`: 获取详细内容信息并跟踪访问

#### 分类管理
- `GetCmsCategoryList`: 按模型获取分类列表
- `GetCmsCategoryDetail`: 获取分类详情
- `AdminCreateCmsCategory`: 创建新分类（管理员）
- `AdminGetCmsCategoryList`: 管理员分类管理
- `AdminGetCmsCategoryTree`: 层次化分类树结构

### 事件系统

该包为内容交互分发事件：

- `CollectEntityEvent`: 内容被收藏/书签时
- `LikeEntityEvent`: 内容被点赞时
- `VisitEntityEvent`: 内容被访问时

### Twig 扩展

在模板中使用 CMS 功能：

```html
{# 获取单个实体详情 #}
{% set entity = get_cms_entity_detail(123) %}
{{ entity.title }}

{# 按模型获取实体列表 #}
{% set entities = get_cms_entity_list('article', 10, 0) %}
{% for entity in entities %}
    <h2>{{ entity.title }}</h2>
{% endfor %}
```

## 数据库结构

该包创建以下数据表：

- `cms_category`: 支持树形结构的层次化内容分类
- `ims_cms_search`: 带关键词跟踪的搜索历史日志
- `cms_tag`: 具有唯一名称的内容标签
- `cms_tag_group`: 标签组织分组
- `cms_topic`: 支持推荐的内容主题
- `cms_visit_stat`: 按日期统计的访问统计

## 高级用法

### 自定义实体模型

扩展 EAV 模型系统以支持自定义内容类型：

```php
use CmsBundle\Service\EntityService;use CmsBundle\Service\ModelService;

class CustomContentService
{
    public function __construct(
        private ModelService $modelService,
        private EntityService $entityService
    ) {}
    
    public function createCustomEntity(string $modelCode, array $data): Entity
    {
        $model = $this->modelService->findValidModelByCode($modelCode);
        // 创建和填充实体...
    }
}
```

### 高级搜索集成

使用 ContentService 实现复杂搜索场景：

```php
use CmsBundle\Service\ContentService;

$qb = $entityRepository->createQueryBuilder('e');
$this->contentService->searchByKeyword($qb, '搜索关键词', $model);
$results = $qb->getQuery()->getResult();
```

### 跨模块集成

与其他模块集成时，始终使用 Service 层而不是直接访问 Repository：

```php
// ✅ 正确 - 使用 Service 层
$entityService->findEntityBy(['id' => $id]);

// ❌ 错误 - 直接访问 Repository 违反架构原则
$entityRepository->findOneBy(['id' => $id]);
```

## 测试

运行测试套件：

```bash
./vendor/bin/phpunit packages/cms-bundle/tests
```

## 系统要求

- PHP 8.1+
- Symfony 6.4+
- Doctrine ORM 3.0+

## 贡献

欢迎贡献代码！请阅读我们的贡献指南并提交拉取请求。

## 许可证

MIT 许可证
