<?php

namespace Zenstruck\Url\Bridge\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class UrlExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('url', 'Zenstruck\url'),
            new TwigFilter('mailto', 'Zenstruck\mailto'),
        ];
    }
}
