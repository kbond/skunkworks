<?php

namespace Zenstruck {
    /**
     * @see Dimension::create()
     */
    function dimension($value, ?string $unit = null): Dimension
    {
        return Dimension::create($value, $unit);
    }
}

namespace Zenstruck\Dimension {
    use Zenstruck\Dimension;
    use Zenstruck\Dimension\Converter\DurationConverter;

    /**
     * @param string|float|int|Dimension $value Valid information dimension or bytes
     */
    function humanize_bytes($value, ?string $format = null): string
    {
        return InformationHumanizer::decimal($value, $format);
    }

    /**
     * @param string|float|int|Dimension $value Valid information dimension or bytes
     */
    function humanize_bytes_binary($value, ?string $format = null): string
    {
        return InformationHumanizer::binary($value, $format);
    }

    /**
     * @author Fabien Potencier <fabien@symfony.com>
     *
     * @param string|float|int|Dimension $value Valid duration dimension or seconds
     */
    function humanize_duration($value): string
    {
        $seconds = (new DurationConverter())->convert(Dimension::create($value, 's'), 's')->quantity();
        static $timeFormats = [
            [0, '< 1 sec'],
            [1, '1 sec'],
            [2, 'secs', 1],
            [60, '1 min'],
            [120, 'mins', 60],
            [3600, '1 hr'],
            [7200, 'hrs', 3600],
            [86400, '1 day'],
            [172800, 'days', 86400],
        ];

        foreach ($timeFormats as $i => $format) {
            if ($seconds >= $format[0]) {
                if ((isset($timeFormats[$i + 1]) && $seconds < $timeFormats[$i + 1][0]) || $i === \count($timeFormats) - 1) {
                    if (2 === \count($format)) {
                        return $format[1];
                    }

                    return \floor($seconds / $format[2]).' '.$format[1];
                }
            }
        }
    }
}
