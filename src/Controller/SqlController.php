<?php

declare(strict_types=1);

namespace CmsBundle\Controller;

use CmsBundle\Service\ModelService;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class SqlController extends AbstractController
{
    #[Route(path: '/cms-model-sql/{code}', name: 'cms-model-sql')]
    public function __invoke(string $code, ModelService $modelService, Connection $connection): Response
    {
        $model = $modelService->findValidModelByCode($code);
        if (null === $model) {
            throw new NotFoundHttpException('找不到模型数据');
        }

        $selectParts = [
            'ce.id',
            'ce.title',
            'ce.create_time',
            'ce.update_time',
        ];
        $sqlLines = [];

        foreach ($model->getSortedAttributes() as $attribute) {
            $alias = "v{$attribute->getId()}";
            $attributeName = $attribute->getName();
            if (null === $attributeName) {
                continue;
            }
            $name = $connection->getDatabasePlatform()->quoteSingleIdentifier($attributeName);
            $sqlLines[] = "LEFT JOIN cms_value AS {$alias} ON (ce.id = {$alias}.entity_id AND {$alias}.attribute_id = '{$attribute->getId()}')";
            $selectParts[] = "{$alias}.data AS {$name}";
        }

        $selectParts = implode(', ', $selectParts);
        array_unshift($sqlLines, "SELECT {$selectParts} FROM cms_entity AS ce");

        $sqlLines[] = "WHERE ce.model_id = '{$model->getId()}'";

        return new Response(trim(implode("\n", $sqlLines)));
    }
}
