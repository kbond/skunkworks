<?php

namespace Zenstruck;

use Zenstruck\Dimension\Converter;
use Zenstruck\Dimension\Converter\MultiConverter;
use Zenstruck\Dimension\Exception\ConversionNotPossible;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Dimension implements \Stringable, \JsonSerializable
{
    private const ARRAY_KEY_QUANTITY = 'quantity';
    private const ARRAY_KEY_UNIT = 'unit';

    private static ?string $defaultFormat = null;
    private static ?Converter $converter = null;
    private static ?\NumberFormatter $numberFormatter = null;

    private float $quantity;
    private string $unit;

    /**
     * @param int|float $quantity
     */
    public function __construct($quantity, string $unit)
    {
        if (!\is_numeric($quantity)) {
            throw new \InvalidArgumentException(\sprintf('Quantity must be a number, "%s" given.', get_debug_type($quantity)));
        }

        $this->quantity = (float) $quantity;
        $this->unit = $unit;
    }

    public function __toString(): string
    {
        return $this->format();
    }

    /**
     * @param string|array|self $value
     */
    public static function create($value, ?string $unit = null): self
    {
        if ($value instanceof self) {
            return $value;
        }

        if (\is_array($value) && isset($value[self::ARRAY_KEY_QUANTITY], $value[self::ARRAY_KEY_UNIT])) {
            $unit = $value[self::ARRAY_KEY_UNIT];
            $value = $value[self::ARRAY_KEY_QUANTITY];
        }

        if (null !== $unit && \is_numeric($value)) {
            return new self($value, $unit);
        }

        if (!\is_string($value)) {
            throw new \InvalidArgumentException(\sprintf('Unable to parse "%s" as a dimension.', get_debug_type($value)));
        }

        if (\preg_match('#^(-?[\d,]+(.[\d,]+)?)([\s\-_]+)?(.+)$#', \trim($value), $matches)) {
            return new self(\str_replace(',', '', $matches[1]), $matches[4]);
        }

        try {
            if (\is_array($decoded = \json_decode($value, true, 2, \JSON_THROW_ON_ERROR))) {
                return self::create($decoded);
            }
        } catch (\JsonException $e) {
        }

        throw new \InvalidArgumentException("\"{$value}\" is an invalid dimensional value.");
    }

    public static function setDefaultFormat(string $format): void
    {
        self::$defaultFormat = $format;
    }

    public static function setConverter(Converter $converter): void
    {
        self::$converter = $converter;
    }

    public static function setNumberFormatter(\NumberFormatter $formatter): void
    {
        self::$numberFormatter = $formatter;
    }

    public function quantity(): float
    {
        return $this->quantity;
    }

    public function unit(): string
    {
        return $this->unit;
    }

    /**
     * @param string|null $format To use the number formatter, use "# u" with "#" being
     *                            the formatted quantity and "u" being the units.
     *                            To use sprintf formatting, use "%f u" with "%f" being
     *                            any valid sprintf float formatter (ie "%.4f") and "u"
     *                            being the units.
     */
    public function format(?string $format = null): string
    {
        $format = \preg_replace_callback_array(
            [
                '/u/' => fn() => $this->unit(),
                '/#/' => fn() => self::formatNumber($this->quantity()),
            ],
            $format ?? self::defaultFormat()
        );

        return \sprintf($format, $this->quantity(), $this->unit());
    }

    /**
     * @throws ConversionNotPossible
     */
    public function convertTo(string $unit): self
    {
        if ($unit === $this->unit) {
            return $this;
        }

        return self::converter()->convert($this, $unit);
    }

    public function jsonSerialize(): array
    {
        return [self::ARRAY_KEY_QUANTITY => $this->quantity(), self::ARRAY_KEY_UNIT => $this->unit()];
    }

    private static function defaultFormat(): string
    {
        return self::$defaultFormat ??= \class_exists(\NumberFormatter::class) ? '# u' : '%g u';
    }

    private static function converter(): Converter
    {
        return self::$converter ??= MultiConverter::createDefault();
    }

    private static function formatNumber(float $quantity): string
    {
        if (!self::$numberFormatter) {
            self::$numberFormatter = new \NumberFormatter(\Locale::getDefault(), \NumberFormatter::DECIMAL);
            self::$numberFormatter->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, 2);
            self::$numberFormatter->setAttribute(\NumberFormatter::ROUNDING_MODE, \NumberFormatter::ROUND_HALFUP);
        }

        return self::$numberFormatter->format($quantity);
    }
}
