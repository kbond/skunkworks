<?php

namespace Zenstruck\Url;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class LowercasePart extends Part
{
    public function __construct(string $value)
    {
        parent::__construct(\mb_strtolower($value));
    }
}
