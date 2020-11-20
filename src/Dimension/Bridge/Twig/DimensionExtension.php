<?php

namespace Zenstruck\Dimension\Bridge\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class DimensionExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('dimension', 'Zenstruck\dimension'),
            new TwigFilter('humanize_duration', 'Zenstruck\Dimension\humanize_duration'),
            new TwigFilter('humanize_bytes', 'Zenstruck\Dimension\humanize_bytes'),
            new TwigFilter('humanize_bytes_binary', 'Zenstruck\Dimension\humanize_bytes_binary'),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('dimension', 'Zenstruck\dimension'),
        ];
    }
}
