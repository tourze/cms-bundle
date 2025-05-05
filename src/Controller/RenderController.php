<?php

namespace CmsBundle\Controller;

use CmsBundle\Repository\EntityRepository;
use CmsBundle\Repository\ModelRepository;
use CmsBundle\Repository\RenderTemplateRepository;
use CmsBundle\Service\RoutingCondition;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Twig\Environment;

class RenderController extends AbstractController
{
    public function __construct(
        private readonly Environment $twig,
        private readonly ModelRepository $modelRepository,
        private readonly EntityRepository $entityRepository,
        private readonly RenderTemplateRepository $templateRepository,
    ) {
    }

    #[Route(
        '/{path}',
        name: 'cms-render-main',
        requirements: ['path' => Requirement::CATCH_ALL],
        condition: "service('cms_routing_condition').check(context, request)",
        priority: -999
    )]
    public function main(string $path, Request $request): Response
    {
        $template = $this->templateRepository->findOneBy([
            'id' => $request->attributes->get(RoutingCondition::TEMPLATE_KEY),
            'valid' => true,
        ]);
        if (!$template) {
            throw new NotFoundHttpException('查找模板失败');
        }

        $model = null;
        if ($request->attributes->has('model_code')) {
            $model = $this->modelRepository->findOneBy([
                'code' => $request->attributes->get('model_code'),
                'valid' => true,
            ]);
        }
        if ($request->attributes->has('model_id')) {
            $model = $this->modelRepository->findOneBy([
                'code' => $request->attributes->get('model_id'),
                'valid' => true,
            ]);
        }

        $entity = null;
        if ($request->attributes->has('entity_id')) {
            $entity = $this->entityRepository->findOneBy([
                'id' => $request->attributes->get('entity_id'),
            ]);
        }
        if ($entity && !$model) {
            $model = $entity->getModel();
        }

        $twigTemplate = $this->twig->createTemplate($template->getContent());
        $content = $twigTemplate->render([
            ...$request->attributes->all(),
            'path' => $path,
            'model' => $model,
            'entity' => $entity,
        ]);
        $response = new Response();
        $response->setStatusCode(Response::HTTP_OK);
        $response->setContent($content);

        return $response;
    }
}
