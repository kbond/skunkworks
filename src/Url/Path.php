<?php

namespace Zenstruck\Url;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Path extends Part
{
    private const DEFAULT_DELIMITER = '/';

    public function __construct(string $value)
    {
        parent::__construct(\implode('/', \array_map('rawurldecode', \explode('/', $value))));
    }

    /**
     * @return array The path exploded with $delimiter
     */
    public function segments(string $delimiter = self::DEFAULT_DELIMITER): array
    {
        return \array_filter(\explode($delimiter, $this->trim()));
    }

    /**
     * @param int $index 1-based
     */
    public function segment(int $index, ?string $default = null, string $delimiter = self::DEFAULT_DELIMITER): ?string
    {
        return $this->segments($delimiter)[$index - 1] ?? $default;
    }

    public function trim(): string
    {
        return \trim($this->toString(), '/');
    }

    public function rtrim(): string
    {
        return \rtrim($this->toString(), '/');
    }

    public function ltrim(): string
    {
        return \ltrim($this->toString(), '/');
    }

    public function absolute(): string
    {
        return '/'.$this->ltrim();
    }

    public function extension(): ?string
    {
        return \pathinfo($this->toString(), PATHINFO_EXTENSION) ?: null;
    }

    public function encoded(): string
    {
        return \implode('/', \array_map('rawurlencode', \explode('/', $this->toString())));
    }

    public function isAbsolute(): bool
    {
        return 0 === \mb_strpos($this->toString(), '/');
    }
}
