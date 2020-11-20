<?php

namespace Zenstruck\Dimension;

use Zenstruck\Dimension;
use Zenstruck\Dimension\Exception\ConversionNotPossible;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface Converter
{
    /**
     * @throws ConversionNotPossible
     */
    public function convert(Dimension $from, string $to): Dimension;
}
