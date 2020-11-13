<?php

namespace Zenstruck\Utilities\Url;

/**
 * @experimental
 *
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Host extends LowercasePart
{
    private const DEFAULT_DELIMITER = '.';

    /**
     * @return array The host exploded with $delimiter
     */
    public function segments(string $delimiter = self::DEFAULT_DELIMITER): array
    {
        return \array_filter(\explode($delimiter, $this->value()));
    }

    /**
     * @param int $index 1-based
     */
    public function segment(int $index, ?string $default = null, string $delimiter = self::DEFAULT_DELIMITER): ?string
    {
        return $this->segments($delimiter)[$index - 1] ?? $default;
    }

    public function tld(): ?string
    {
        $segments = $this->segments(self::DEFAULT_DELIMITER);

        return \in_array(\count($segments), [0, 1], true) ? null : $segments[\count($segments) - 1];
    }
}
