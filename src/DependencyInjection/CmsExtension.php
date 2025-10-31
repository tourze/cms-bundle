<?php

declare(strict_types=1);

namespace CmsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\SymfonyDependencyServiceLoader\AutoExtension;

#[Autoconfigure(public: true)]
class CmsExtension extends AutoExtension
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
