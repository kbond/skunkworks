<?php

namespace Zenstruck\Dimension\Converter;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class MassConverter extends UnitConverter
{
    protected static function build(): void
    {
        static::addSIUnit(Unit::nativeLinearFactory('g'), ['gram', 'grams']);
        static::add(Unit::linearFactory('t', 1e6), ['ton', 'tons', 'tonne', 'tonnes']);
        static::add(Unit::linearFactory('lb', 453.59237), ['lbs', 'pound', 'pounds']);
        static::add(Unit::linearFactory('oz', 453.59237 / 16), ['ounce', 'ounces']);
        static::add(Unit::linearFactory('st', 453.59237 * 14), ['stone', 'stones']);
    }
}
