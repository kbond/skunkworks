<?php

namespace Zenstruck\Dimension\Converter;

use Zenstruck\Dimension;
use Zenstruck\Dimension\Converter;
use Zenstruck\Dimension\Exception\ConversionNotPossible;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class MultiConverter implements Converter
{
    private iterable $converters;

    /**
     * @param Converter[] $converters
     */
    public function __construct(iterable $converters)
    {
        $this->converters = $converters;
    }

    public static function createDefault(): self
    {
        return new self([
            new LengthConverter(),
            new MassConverter(),
            new TemperatureConverter(),
            new InformationConverter(),
            new DurationConverter(),
        ]);
    }

    public function convert(Dimension $from, string $to): Dimension
    {
        foreach ($this->converters as $converter) {
            try {
                return $converter->convert($from, $to);
            } catch (ConversionNotPossible $e) {
                continue;
            }
        }

        throw new ConversionNotPossible("No converter registered to convert \"{$from->unit()}\" to \"{$to}\".");
    }
}
