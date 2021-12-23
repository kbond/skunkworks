<?php

namespace Zenstruck;

/**
 * @param Url|string|null $value
 */
function url($value = null): Url
{
    return Url::new($value);
}

/**
 * @param Mailto|string|null $value
 */
function mailto($value = null): Mailto
{
    return $value instanceof Mailto ? $value : new Mailto($value);
}
