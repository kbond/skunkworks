<?php

namespace Zenstruck\Url;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Scheme extends LowercasePart
{
    private const DEFAULT_DELIMITER = '+';

    public function __construct(string $value)
    {
        if ('://' === \mb_substr($value, -3)) {
            $value = \mb_substr($value, 0, -3);
        }

        parent::__construct($value);
    }

    /**
     * @return array The scheme exploded with $delimiter
     */
    public function segments(string $delimiter = self::DEFAULT_DELIMITER): array
    {
        return \array_filter(\explode($delimiter, $this->toString()));
    }

    /**
     * @param int $index 1-based
     */
    public function segment(int $index, ?string $default = null, string $delimiter = self::DEFAULT_DELIMITER): ?string
    {
        return $this->segments($delimiter)[$index - 1] ?? $default;
    }
}
