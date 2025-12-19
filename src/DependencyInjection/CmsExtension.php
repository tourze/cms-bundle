<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\DependencyInjection;

use Tourze\SymfonyDependencyServiceLoader\AutoExtension;

final class CmsExtension extends AutoExtension
{
    public function getAlias(): string
    {
        return 'cms';
    }

    protected function getConfigDir(): string
    {
        return __DIR__.'/../Resources/config';
    }
}
