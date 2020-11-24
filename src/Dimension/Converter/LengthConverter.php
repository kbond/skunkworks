<?php

namespace Zenstruck\Dimension\Converter;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class LengthConverter extends UnitConverter
{
    protected static function build(): void
    {
        static::addSIUnit(Unit::nativeLinearFactory('m'), ['meter', 'meters', 'metre', 'metres']);
        static::add(Unit::linearFactory('ft', 0.3048), ['feet', 'foot', "'"]);
        static::add(Unit::linearFactory('in', 0.0254), ['inch', 'inches', '"']);
        static::add(Unit::linearFactory('mi', 1609.344), ['mile', 'miles']);
        static::add(Unit::linearFactory('yd', 0.9144), ['yard', 'yards']);
        static::add(Unit::linearFactory('M', 1852), ['nautical mile', 'nautical mile', 'nm', 'NM']);
        static::add(Unit::linearFactory('mil', 10000)); // Scandinavian mil
        static::add(Unit::linearFactory('AU', 149597870700), ['au', 'astronomical unit', 'astronomical units']); // Astronomical Unit
    }
}
