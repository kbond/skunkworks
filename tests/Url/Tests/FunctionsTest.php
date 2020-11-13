<?php

namespace Zenstruck\Url\Tests;

use PHPUnit\Framework\TestCase;
use function Zenstruck\mailto;
use function Zenstruck\url;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class FunctionsTest extends TestCase
{
    /**
     * @test
     */
    public function url(): void
    {
        $this->assertSame('example.com', (string) url('https://example.com:8080/foo')->host());
    }

    /**
     * @test
     */
    public function mailto(): void
    {
        $this->assertSame('subject', mailto('kevin@example.com?subject=subject')->subject());
    }
}
