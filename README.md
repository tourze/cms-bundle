# CMS Bundle

[English](README.md) | [中文](README.zh-CN.md)

![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-blue)
![License](https://img.shields.io/badge/license-MIT-green)
![Build Status](https://img.shields.io/badge/build-passing-brightgreen)
![Coverage](https://img.shields.io/badge/coverage-80%25-yellowgreen)

A comprehensive CMS (Content Management System) bundle for Symfony applications,
providing content management, category organization, tagging, and statistics features.

## Table of Contents

- [Features](#features)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
  - [Basic Entity Structure](#basic-entity-structure)
  - [Services](#services)
  - [JSON-RPC Procedures](#json-rpc-procedures)
  - [Events](#events)
  - [Twig Extensions](#twig-extensions)
- [Database Schema](#database-schema)
- [Advanced Usage](#advanced-usage)
- [Testing](#testing)
- [Requirements](#requirements)
- [Contributing](#contributing)
- [License](#license)

## Features

- **Content Management**: Entity-Attribute-Value (EAV) based content model
- **Category System**: Hierarchical content categorization
- **Tag Management**: Flexible tagging system with tag groups
- **Search & Statistics**: Content search logs and visit statistics
- **Event System**: Content collection, liking, and visit tracking
- **JSON-RPC API**: RESTful API endpoints for content operations
- **Admin Interface**: EasyAdmin integration for backend management
- **Twig Extensions**: Template helpers for CMS functionality

## Installation

```bash
composer require tourze/cms-bundle
```

## Configuration

Add the bundle to your `config/bundles.php`:

```php
return [
    // ...
    CmsBundle\CmsBundle::class => ['all' => true],
];
```

## Usage

### Basic Entity Structure

The bundle provides 6 core entities:

- **Category**: Hierarchical content categorization with tree structure
- **SearchLog**: Search history tracking and keyword analytics
- **Tag**: Content tagging system with unique tag names
- **TagGroup**: Organization and grouping of tags
- **Topic**: Content topics/articles with recommendation support
- **VisitStat**: Daily visit statistics tracking

### Services

#### ContentService
Search content by keywords in EAV attributes:

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
Update visit statistics asynchronously:

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

### JSON-RPC Procedures

The bundle provides JSON-RPC endpoints for:

#### Content Operations
- `GetCmsEntityList`: Retrieve paginated content lists with category/model filtering
- `GetCmsEntityDetail`: Get detailed content information with visit tracking

#### Category Management
- `GetCmsCategoryList`: Fetch category listings by model
- `GetCmsCategoryDetail`: Get category details
- `AdminCreateCmsCategory`: Create new categories (admin)
- `AdminGetCmsCategoryList`: Admin category management
- `AdminGetCmsCategoryTree`: Hierarchical category tree structure

### Events

The bundle dispatches events for content interactions:

- `CollectEntityEvent`: When content is collected/bookmarked
- `LikeEntityEvent`: When content is liked
- `VisitEntityEvent`: When content is visited

### Twig Extensions

Use CMS functionality in templates:

```html
{# Get single entity detail #}
{% set entity = get_cms_entity_detail(123) %}
{{ entity.title }}

{# Get entity list by model #}
{% set entities = get_cms_entity_list('article', 10, 0) %}
{% for entity in entities %}
    <h2>{{ entity.title }}</h2>
{% endfor %}
```

## Database Schema

The bundle creates the following tables:

- `cms_category`: Hierarchical content categories with tree structure
- `ims_cms_search`: Search history logs with keyword tracking
- `cms_tag`: Content tags with unique names
- `cms_tag_group`: Tag organization groups
- `cms_topic`: Content topics with recommendation support
- `cms_visit_stat`: Daily visit statistics

## Advanced Usage

### Custom Entity Models

Extend the EAV model system for custom content types:

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
        // Create and populate entity...
    }
}
```

### Advanced Search Integration

Implement complex search scenarios using ContentService:

```php
use CmsBundle\Service\ContentService;

$qb = $entityRepository->createQueryBuilder('e');
$this->contentService->searchByKeyword($qb, 'search term', $model);
$results = $qb->getQuery()->getResult();
```

### Cross-Module Integration

When integrating with other modules, always use Service layer instead of direct Repository access:

```php
// ✅ Correct - Use Service layer
$entityService->findEntityBy(['id' => $id]);

// ❌ Wrong - Direct Repository access violates architecture
$entityRepository->findOneBy(['id' => $id]);
```

## Testing

Run the test suite:

```bash
./vendor/bin/phpunit packages/cms-bundle/tests
```

## Requirements

- PHP 8.1+
- Symfony 6.4+
- Doctrine ORM 3.0+

## Contributing

Contributions are welcome! Please read our contributing guidelines and submit pull requests.

## License

MIT License