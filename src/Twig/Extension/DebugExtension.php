<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\DdExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class DebugExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            new TwigFilter('dd', [DdExtensionRuntime::class, 'dd']),
            new TwigFilter('dump', [DdExtensionRuntime::class, 'dump']),
        ];
    }
}
