<?php

namespace Zenstruck\Utilities;

/**
 * Array wrapper to allow accessing/manipulating nested array items
 * by "delimiter" notation ("dot" by default").
 *
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ArrayAccessor implements \ArrayAccess
{
    private array $value;
    private string $delimiter;

    /**
     * @param string $delimiter Change to customize the notation delimiter
     */
    public function __construct(array $value = [], string $delimiter = '.')
    {
        $this->value = $value;
        $this->delimiter = $delimiter;
    }

    public function all(): array
    {
        return $this->value;
    }

    /**
     * Get an item using "delimiter" notation.
     *
     * @author Taylor Otwell <taylor@laravel.com>
     * @source https://github.com/laravel/framework/blob/e9483c441d5f0c8598d438d6024db8b1a7aa55fe/src/Illuminate/Collections/Arr.php#L286
     *
     * @param \Throwable|mixed|null $default
     *
     * @return mixed $default if no match
     *
     * @throws \Throwable If passed as default and no match
     */
    public function get(string $path, $default = null)
    {
        // first check if path exists on array
        if (\array_key_exists($path, $this->value)) {
            return $this->value[$path];
        }

        $current = &$this->value;

        foreach (\explode($this->delimiter, $path) as $segment) {
            if (!\is_array($current) || !\array_key_exists($segment, $current)) {
                if ($default instanceof \Throwable) {
                    throw $default;
                }

                return value($default);
            }

            $current = &$current[$segment];
        }

        return $current;
    }

    /**
     * Check if an item or items exist using "delimiter" notation.
     */
    public function has(string ...$paths): bool
    {
        if (empty($paths)) {
            return false;
        }

        foreach ($paths as $path) {
            try {
                $this->get($path, new \OutOfBoundsException());
            } catch (\OutOfBoundsException $e) {
                return false;
            }
        }

        return true;
    }

    /**
     * Set item to a given value using "delimiter" notation.
     *
     * @author Taylor Otwell <taylor@laravel.com>
     * @source https://github.com/laravel/framework/blob/e9483c441d5f0c8598d438d6024db8b1a7aa55fe/src/Illuminate/Collections/Arr.php#L553
     *
     * @param mixed $value
     */
    public function set(string $path, $value): self
    {
        $current = &$this->value;
        $keys = \explode($this->delimiter, $path);

        foreach ($keys as $i => $key) {
            if (1 === \count($keys)) {
                break;
            }

            unset($keys[$i]);

            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (!isset($current[$key]) || !\is_array($current[$key])) {
                $current[$key] = [];
            }

            $current = &$current[$key];
        }

        $current[\array_shift($keys)] = $value;

        return $this;
    }

    /**
     * Remove one or many items using "delimiter" notation.
     *
     * @author Taylor Otwell <taylor@laravel.com>
     * @source https://github.com/laravel/framework/blob/e9483c441d5f0c8598d438d6024db8b1a7aa55fe/src/Illuminate/Collections/Arr.php#L241
     */
    public function unset(string ...$paths): self
    {
        foreach ($paths as $path) {
            // if the exact key exists in the top-level, remove it
            if (\array_key_exists($path, $this->value)) {
                unset($this->value[$path]);

                continue;
            }

            $parts = \explode($this->delimiter, $path);

            // clean up before each pass
            $array = &$this->value;

            while (\count($parts) > 1) {
                $part = \array_shift($parts);

                if (!isset($array[$part]) || !\is_array($array[$part])) {
                    continue 2;
                }

                $array = &$array[$part];
            }

            unset($array[\array_shift($parts)]);
        }

        return $this;
    }

    public function offsetExists($offset): bool
    {
        return $this->has($offset);
    }

    /**
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value): void
    {
        $this->set($offset, $value);
    }

    public function offsetUnset($offset): void
    {
        $this->unset($offset);
    }
}
