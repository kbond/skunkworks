<?php

namespace Zenstruck\Dimension\Converter;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class DurationConverter extends UnitConverter
{
    protected static function build(): void
    {
        static::addSIUnit(Unit::nativeLinearFactory('s'), ['sec', 'secs', 'second', 'seconds']);
        static::add(Unit::linearFactory('m', 60), ['min', 'mins', 'minute', 'minutes']);
        static::add(Unit::linearFactory('h', 3600), ['hr', 'hrs', 'hour', 'hours']);
        static::add(Unit::linearFactory('d', 86400), ['day', 'days']);
        static::add(Unit::linearFactory('w', 604800), ['wk', 'wks', 'week', 'weeks']);
        static::add(Unit::linearFactory('y', 31556952), ['yr', 'yrs', 'year', 'years']); // Gregorian year, understood as 365.2425 days
        static::add(Unit::linearFactory('jyr', 31557600), ['julian year', 'julian years']); // Julian year, understood as 365.25 days
    }
}
