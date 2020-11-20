<?php

namespace Zenstruck\Dimension\Converter;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class InformationConverter extends UnitConverter
{
    protected static function build(): void
    {
        static::add(Unit::nativeLinearFactory('B'), ['byte', 'bytes']);
        static::add(Unit::linearFactory('bit', 1 / 8), ['bits']);
        static::add(Unit::linearFactory('kB', 1e3), ['kilobyte', 'kilobytes']);
        static::add(Unit::linearFactory('KiB', 1024), ['kibibyte', 'kibibytes']);
        static::add(Unit::linearFactory('MB', 1e6), ['megabyte', 'megabytes']);
        static::add(Unit::linearFactory('MiB', 1024 * 1024), ['mebibyte', 'mebibytes']);
        static::add(Unit::linearFactory('GB', 1e9), ['gigabyte', 'gigabytes']);
        static::add(Unit::linearFactory('GiB', 1024 * 1024 * 1024), ['gibibyte', 'gibibytes']);
        static::add(Unit::linearFactory('TB', 1e12), ['terabyte', 'terabytes']);
        static::add(Unit::linearFactory('TiB', 1024 * 1024 * 1024 * 1024), ['tebibyte', 'tebibytes']);
        static::add(Unit::linearFactory('PB', 1e15), ['petabyte', 'petabytes']);
        static::add(Unit::linearFactory('PiB', 1024 * 1024 * 1024 * 1024 * 1024), ['pebibyte', 'pebibytes']);
        static::add(Unit::linearFactory('EB', 1e18), ['exabyte', 'exabytes']);
        static::add(Unit::linearFactory('EiB', 1024 * 1024 * 1024 * 1024 * 1024 * 1024), ['exbibyte', 'exbibytes']);
        static::add(Unit::linearFactory('ZB', 1e21), ['zettabyte', 'zettabytes']);
        static::add(Unit::linearFactory('ZiB', 1024 * 1024 * 1024 * 1024 * 1024 * 1024 * 1024), ['zebibyte', 'zebibytes']);
        static::add(Unit::linearFactory('YB', 1e21), ['yottabyte', 'yottabytes']);
        static::add(Unit::linearFactory('YiB', 1024 * 1024 * 1024 * 1024 * 1024 * 1024 * 1024 * 1024), ['yobibyte', 'yobibytes']);
    }
}
