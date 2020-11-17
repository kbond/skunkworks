<?php

namespace Zenstruck\Dsn\Tests;

use PHPUnit\Framework\TestCase;
use Zenstruck\Dsn;
use Zenstruck\Dsn\Group;
use Zenstruck\Mailto;
use Zenstruck\Url;
use Zenstruck\Url\Scheme;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class DsnTest extends TestCase
{
    /**
     * @test
     * @dataProvider valueProvider
     */
    public function can_parse($input, $expectedClass, $expectedString)
    {
        $dsn = Dsn::parse($input);

        $this->assertInstanceOf($expectedClass, $dsn);
        $this->assertSame($expectedString, (string) $dsn);
    }

    public static function valueProvider(): iterable
    {
        yield ['mailto:kevin@example.com', Mailto::class, 'mailto:kevin%40example.com'];
        yield ['http://www.example.com', Url::class, 'http://www.example.com'];
        yield ['null', Url::class, 'null'];
        yield ['null://', Scheme::class, 'null'];
        yield ['In-Memory://', Scheme::class, 'in-memory'];
        yield ['failover(smtp://default mail+api://default)', Group::class, 'failover(smtp://default mail+api://default)'];
        yield ['failover(smtp://default roundrobin(mail+api://default postmark+api://default))', Group::class, 'failover(smtp://default roundrobin(mail+api://default postmark+api://default))'];
        yield ['failover()', Url::class, 'failover%28%29'];
    }

    /**
     * @test
     */
    public function can_parse_group_dsn(): void
    {
        /** @var Group $dsn */
        $dsn = Dsn::parse('failover(smtp://default mail+api://default)');

        $this->assertInstanceOf(Group::class, $dsn);
        $this->assertSame('failover', $dsn->name());
        $this->assertInstanceOf(Url::class, $dsn->children()[0]);
        $this->assertSame('smtp', $dsn->children()[0]->scheme()->toString());
        $this->assertInstanceOf(Url::class, $dsn->children()[1]);
        $this->assertSame('mail+api', $dsn->children()[1]->scheme()->toString());
    }

    /**
     * @test
     */
    public function can_parse_nested_group_dsn(): void
    {
        /** @var Group $dsn */
        $dsn = Dsn::parse('failover(smtp://default roundrobin(mail+api://default mailto:kevin) failover(mail+api://default roundrobin(mail+api://default)))');

        $this->assertInstanceOf(Group::class, $dsn);
        $this->assertCount(3, $dsn->children());
        $this->assertInstanceOf(Url::class, $dsn->children()[0]);
        $this->assertSame('smtp://default', (string) $dsn->children()[0]);
        $this->assertInstanceOf(Group::class, $dsn->children()[1]);
        $this->assertSame('roundrobin(mail+api://default mailto:kevin)', (string) $dsn->children()[1]);
        $this->assertCount(2, $dsn->children()[1]->children());
        $this->assertCount(2, $dsn->children()[2]->children());
        $this->assertCount(1, $dsn->children()[2]->children()[1]->children());
    }
}
