<?php

namespace Zenstruck\Url\Bridge\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

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

    public function getFunctions(): array
    {
        return [
            new TwigFunction('url', 'Zenstruck\url'),
            new TwigFunction('mailto', 'Zenstruck\mailto'),
        ];
    }
}
