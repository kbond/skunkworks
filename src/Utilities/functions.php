<?php

namespace Zenstruck\Utilities;

use Zenstruck\Utilities\Sql\Pattern;

/**
 * Replaces "&nbsp;" with a single space and converts multiple sequential
 * spaces into a single space.
 *
 * @author Kevin Bond <kevinbond@gmail.com>
 */
function remove_whitespace(?string $value): string
{
    return \preg_replace('/\s+/', ' ', \str_replace('&nbsp;', ' ', $value));
}

/**
 * Similar to core "trim" but returns null instead of an empty string
 * When an array is passed, all elements get processed recursively.
 *
 * @param string|array $data
 *
 * @return array|string|null
 *
 * @author Kevin Bond <kevinbond@gmail.com>
 */
function null_trim($data, ?string $charlist = null)
{
    if (\is_array($data)) {
        return \array_map(
            static function($value) use ($charlist) {
                return null_trim($value, $charlist);
            },
            $data
        );
    }

    $trimmed = null === $charlist ? \trim($data) : \trim($data, $charlist);

    return '' === $trimmed ? null : $trimmed;
}

/**
 * Truncates text to a length without breaking words. Trims and removes
 * whitespace before truncating.
 *
 * @author Kevin Bond <kevinbond@gmail.com>
 */
function truncate_word(?string $value, int $length = 255, string $suffix = '...'): string
{
    $output = remove_whitespace(\trim($value));

    if (\mb_strlen($output) > $length) {
        $output = \wordwrap($output, $length - \mb_strlen($suffix));
        $output = \mb_substr($output, 0, \mb_strpos($output, "\n"));
        $output .= $suffix;
    }

    return \mb_strlen($output) > $length ? '' : $output;
}

/**
 * @see ArrayAccessor
 *
 * @todo move to collection component
 */
function array_accessor(array $value = []): ArrayAccessor
{
    return new ArrayAccessor($value);
}

/**
 * If passed value is a closure, execute and return. Otherwise,
 * return the value as is.
 *
 * @param \Closure|mixed $value
 *
 * @return mixed
 *
 * @author Taylor Otwell <taylor@laravel.com>
 */
function value($value)
{
    return $value instanceof \Closure ? $value() : $value;
}

/**
 * @see Pattern::__construct()
 */
function sql_pattern(?string $value, ?string $wildcard = '*'): string
{
    return new Pattern($value, $wildcard);
}

/**
 * @see Pattern::contains()
 */
function sql_pattern_contains(?string $value, ?string $wildcard = '*'): string
{
    return Pattern::contains($value, $wildcard);
}

/**
 * @see Pattern::beginsWith()
 */
function sql_pattern_begins_with(?string $value, ?string $wildcard = '*'): string
{
    return Pattern::beginsWith($value, $wildcard);
}

/**
 * @see Pattern::endsWith()
 */
function sql_pattern_ends_with(?string $value, ?string $wildcard = '*'): string
{
    return Pattern::endsWith($value, $wildcard);
}
