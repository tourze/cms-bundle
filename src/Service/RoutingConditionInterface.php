<?php

namespace CmsBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;

interface RoutingConditionInterface
{
    public function check(RequestContext $context, Request $request): bool;
}
