<?php

namespace Zenstruck\Utilities\Bridge\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class FunctionsExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('null_trim', 'Zenstruck\Utilities\Functions\null_trim'),
            new TwigFilter('remove_whitespace', 'Zenstruck\Utilities\Functions\remove_whitespace'),
            new TwigFilter('truncate_word', 'Zenstruck\Utilities\Functions\truncate_word'),
            new TwigFilter('value', 'Zenstruck\Utilities\Functions\value'),
        ];
    }
}
