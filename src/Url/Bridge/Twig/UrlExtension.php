<?php

namespace Zenstruck\Url\Bridge\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Zenstruck\Mailto;
use Zenstruck\Url;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class UrlExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('url', [Url::class, 'new']),
            new TwigFilter('mailto', [Mailto::class, 'new']),
        ];
    }
}
