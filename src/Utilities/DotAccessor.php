<?php

namespace Zenstruck\Utilities;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class DotAccessor
{
    public function __construct(private array|object &$value)
    {
    }

    /**
     * Get an item using "dot" notation.
     *
     * @author Taylor Otwell <taylor@laravel.com>
     * @source https://github.com/laravel/framework/blob/e9483c441d5f0c8598d438d6024db8b1a7aa55fe/src/Illuminate/Collections/Arr.php#L286
     *
     * @template T
     *
     * @param T $default
     *
     * @return T $default if no match
     */
    public function get(string $path, mixed $default = null): mixed
    {
        // first check if path exists on array
        if (\is_array($this->value) && \array_key_exists($path, $this->value)) {
            return $this->value[$path];
        }

        if (\is_array($this->value)) {
            $current = &$this->value;
        } else {
            $current = $this->value;
        }

        foreach (\explode('.', $path) as $segment) {
            if (\is_array($current) && \array_key_exists($segment, $current)) {
                $current = &$current[$segment];

                continue;
            }

            if (!\is_object($current)) {
                return $default;
            }

            if (\property_exists($current, $segment)) {
                $current = &$current->$segment;

                continue;
            }

            if (\method_exists($current, $segment)) {
                $current = $current->$segment();

                continue;
            }

            foreach (['get', 'has', 'is'] as $prefix) {
                if (\method_exists($current, $method = $prefix.\ucfirst($segment))) {
                    $current = $current->$method();

                    continue 2;
                }
            }

            return $default;
        }

        return $current;
    }

    /**
     * Check if an item or items exist using "dot" notation.
     */
    public function has(string ...$paths): bool
    {
        if (!$paths) {
            return false;
        }

        foreach ($paths as $path) {
            if ('__DEFAULT__' === $this->get($path, '__DEFAULT__')) {
                return false;
            }
        }

        return true;
    }
}
