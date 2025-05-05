<?php

namespace CmsBundle\Controller;

use CmsBundle\Repository\ModelRepository;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

class SqlController extends AbstractController
{
    #[Route('/cms-model-sql/{code}', name: 'cms-model-sql')]
    public function main(string $code, ModelRepository $modelRepository, Connection $connection): Response
    {
        $model = $modelRepository->findOneBy([
            'code' => $code,
        ]);
        if (!$model) {
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
            $name = $connection->getDatabasePlatform()->quoteIdentifier($attribute->getName());
            $sqlLines[] = "LEFT JOIN cms_value AS {$alias} ON (ce.id = {$alias}.entity_id AND {$alias}.attribute_id = '{$attribute->getId()}')";
            $selectParts[] = "{$alias}.data AS {$name}";
        }

        $selectParts = implode(', ', $selectParts);
        array_unshift($sqlLines, "SELECT {$selectParts} FROM cms_entity AS ce");

        $sqlLines[] = "WHERE ce.model_id = '{$model->getId()}'";

        return new Response(trim(implode("\n", $sqlLines)));
    }
}
