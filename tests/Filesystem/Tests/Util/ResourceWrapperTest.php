<?php

namespace Zenstruck\Filesystem\Tests\Util;

use PHPUnit\Framework\TestCase;
use Zenstruck\Filesystem\Util\ResourceWrapper;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ResourceWrapperTest extends TestCase
{
    /**
     * @test
     */
    public function can_create_in_memory_resource(): void
    {
        $this->assertSame('some data', ResourceWrapper::inMemory()->write('some data')->contents());
        $this->assertSame('different data', \stream_get_contents(ResourceWrapper::inMemory()->write('different data')->rewind()->get()));
    }

    /**
     * @test
     */
    public function can_create_from_string(): void
    {
        $this->assertSame('some data', ResourceWrapper::fromString('some data')->contents());
        $this->assertSame('different data', \stream_get_contents(ResourceWrapper::fromString('different data')->get()));
    }
}
