<?php

namespace Zenstruck\Dimension;

use Zenstruck\Dimension;
use Zenstruck\Dimension\Converter\InformationConverter;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 */
final class InformationHumanizer
{
    private const DECIMAL_UNITS = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    private const BINARY_UNITS = ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB'];

    public static function decimal($value, ?string $format = null): string
    {
        return self::humanize($value, $format, self::DECIMAL_UNITS, 1000);
    }

    public static function binary($value, ?string $format = null): string
    {
        return self::humanize($value, $format, self::BINARY_UNITS, 1024);
    }

    private static function humanize($value, ?string $format, array $units, int $factor): string
    {
        $i = 0;
        $quantity = (new InformationConverter())
            ->convert(Dimension::create($value, 'B'), 'B')
            ->quantity()
        ;

        while (($quantity / $factor) >= 1 && $i < (\count($units) - 1)) {
            $quantity /= $factor;
            ++$i;
        }

        return Dimension::create($quantity, $units[$i])->format($format);
    }
}
