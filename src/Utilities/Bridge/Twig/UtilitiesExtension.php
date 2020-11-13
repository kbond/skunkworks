<?php

namespace Zenstruck\Utilities\Bridge\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class UtilitiesExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('null_trim', 'Zenstruck\Utilities\null_trim'),
            new TwigFilter('remove_whitespace', 'Zenstruck\Utilities\remove_whitespace'),
            new TwigFilter('truncate_word', 'Zenstruck\Utilities\truncate_word'),
            new TwigFilter('value', 'Zenstruck\Utilities\value'),
        ];
    }
}
