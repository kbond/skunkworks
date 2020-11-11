<?php

namespace Zenstruck\Utilities\Sql;

/**
 * Helper for normalizing SQL LIKE patterns with custom wildcard characters.
 *
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Pattern implements \Stringable
{
    /** @var string|null */
    private $value;

    /** @var string|null */
    private $wildcard;

    /** @var string */
    private $format = '%s';

    /**
     * @param string|null $wildcard custom wildcard character to replace with "%" (ie "*")
     */
    public function __construct(?string $value, ?string $wildcard = null)
    {
        $this->value = $value;
        $this->wildcard = $wildcard;
    }

    public function __toString(): string
    {
        $value = \sprintf($this->format, $this->value);

        if ($this->wildcard) {
            return \str_replace($this->wildcard, '%', $value);
        }

        return $value;
    }

    /**
     * "Contains" pattern ("value" => "%value%").
     *
     * @see Pattern::__construct()
     */
    public static function contains(?string $value, ?string $wildcard = null): self
    {
        $pattern = new self($value, $wildcard);
        $pattern->format = '%%%s%%';

        return $pattern;
    }

    /**
     * "Begins with" pattern ("value" => "value%").
     *
     * @see Pattern::__construct()
     */
    public static function beginsWith(?string $value, ?string $wildcard = null): self
    {
        $pattern = new self($value, $wildcard);
        $pattern->format = '%s%%';

        return $pattern;
    }

    /**
     * "Ends with" pattern ("value" => "%value").
     *
     * @see Pattern::__construct()
     */
    public static function endsWith(?string $value, ?string $wildcard = null): self
    {
        $pattern = new self($value, $wildcard);
        $pattern->format = '%%%s';

        return $pattern;
    }
}
