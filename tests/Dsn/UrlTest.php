<?php

namespace Zenstruck\Utilities\Tests\Dsn;

use PHPUnit\Framework\TestCase;
use Zenstruck\Utilities\Dsn\Url;

/**
 * @source https://github.com/guzzle/psr7/blob/7858757f390bbe4b3d81762a97d6e6e786bb70ad/tests/UriTest.php
 *
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class UrlTest extends TestCase
{
    /**
     * @test
     */
    public function parses_provided_dsn(): void
    {
        $dsn = new Url('https://user:pass@example.com:8080/path/123?q=abc#test');

        $this->assertSame('https', (string) $dsn->scheme());
        $this->assertSame('user:pass@example.com:8080', (string) $dsn->authority());
        $this->assertSame('user:pass', $dsn->authority()->userInfo());
        $this->assertSame('user', $dsn->user());
        $this->assertSame('pass', $dsn->pass());
        $this->assertSame('example.com', (string) $dsn->host());
        $this->assertSame(8080, $dsn->port());
        $this->assertSame('/path/123', (string) $dsn->path());
        $this->assertSame('q=abc', (string) $dsn->query());
        $this->assertSame('test', $dsn->fragment());
        $this->assertSame('https://user:pass@example.com:8080/path/123?q=abc#test', (string) $dsn);
    }

    /**
     * @test
     */
    public function can_transform_and_retrieve_parts_individually(): void
    {
        $dsn = (new Url())
            ->withScheme('https')
            ->withHost('example.com')
            ->withUser('user')
            ->withPass('pass')
            ->withPort(8080)
            ->withPath('/path/123')
            ->withQuery(['q' => 'abc'])
            ->withFragment('test')
        ;

        $this->assertSame('https', (string) $dsn->scheme());
        $this->assertSame('user:pass@example.com:8080', (string) $dsn->authority());
        $this->assertSame('user:pass', $dsn->authority()->userInfo());
        $this->assertSame('example.com', (string) $dsn->host());
        $this->assertSame(8080, $dsn->port());
        $this->assertSame('/path/123', (string) $dsn->path());
        $this->assertSame('q=abc', (string) $dsn->query());
        $this->assertSame('test', $dsn->fragment());
        $this->assertSame('https://user:pass@example.com:8080/path/123?q=abc#test', (string) $dsn);
    }

    /**
     * @test
     */
    public function can_remove_all_parts(): void
    {
        $dsn = (new Url('https://user:pass@example.com:8080/path/123?q=abc#test'))
            ->withoutHost()
            ->withoutPass()
            ->withoutUser()
            ->withoutFragment()
            ->withoutQuery()
            ->withoutPort()
            ->withoutPath()
            ->withoutScheme()
        ;

        $this->assertSame('', (string) $dsn);
    }

    /**
     * @test
     */
    public function can_transform_query_with_array(): void
    {
        $dsn = (new Url('http://example.com?foo=bar'))
            ->withQuery(['q' => 'abc'])
        ;

        $this->assertSame('q=abc', (string) $dsn->query());
        $this->assertSame('http://example.com?q=abc', (string) $dsn);
    }

    /**
     * @test
     * @dataProvider getValidDsns
     */
    public function valid_dsns_stay_valid(string $input): void
    {
        $dsn = new Url($input);

        $this->assertSame($input, (string) $dsn);
    }

    public static function getValidDsns(): iterable
    {
        return [
            ['urn:path-rootless'],
            //['urn:path:with:colon'], todo
            ['urn:/path-absolute'],
            ['urn:/'],
            // only scheme with empty path
            ['urn:'],
            // only path
            ['/'],
            ['relative/'],
            ['0'],
            // same document reference
            [''],
            // network path without scheme
            ['//example.org'],
            ['//example.org/'],
            //['//example.org?q#h'], // todo
            // only query
            //['?q'], // todo
            ['?q=abc&foo=bar'],
            // only fragment
            ['#fragment'],
            // dot segments are not removed automatically
            ['./foo/../bar'],
            [''],
            ['/'],
            ['var/run/foo.txt'],
            //[':foo'], // todo
            ['/var/run/foo.txt'],
            ['/var/run/foo.txt?foo=bar'],
            ['file://var/run/foo.txt'],
            ['http://username:password@hostname:9090/path?arg=value#anchor'],
            ['http://username@hostname/path?arg=value#anchor'],
            ['http://hostname/path?arg=value#anchor'],
            ['ftp://username@hostname/path?arg=value#anchor'],
        ];
    }

    /**
     * @test
     */
    public function can_parse_filesystem_path_dsn(): void
    {
        $dsn = new Url('file:///var/run/foo.txt');

        $this->assertSame('/var/run/foo.txt', (string) $dsn->path());
        $this->assertSame('file', (string) $dsn->scheme());

        $dsn = new Url('file:/var/run/foo.txt');

        $this->assertSame('/var/run/foo.txt', (string) $dsn->path());
        $this->assertSame('file', (string) $dsn->scheme());
    }

    /**
     * @test
     * @dataProvider getInvalidDsns
     */
    public function invalid_dsns_throw_exception(string $input): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Unable to parse \"{$input}\".");

        new Url($input);
    }

    public function getInvalidDsns(): iterable
    {
        return [
            // parse_url() requires the host component which makes sense for http(s)
            // but not when the scheme is not known or different. So '//' or '///' is
            // currently invalid as well but should not according to RFC 3986.
            ['http://'],
            ['urn://host:with:colon'], // host cannot contain ":"
        ];
    }

    /**
     * @test
     */
    public function port_must_be_valid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid port: 100000. Must be between 0 and 65535.');

        (new Url('//example.com'))->withPort(100000);
    }

    /**
     * @test
     */
    public function with_port_cannot_be_negative(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid port: -1. Must be between 0 and 65535.');

        (new Url('//example.com'))->withPort(-1);
    }

    /**
     * @test
     */
    public function can_parse_falsey_dsn_parts(): void
    {
        $dsn = new Url('0://0:0@0/0?0#0');

        $this->assertSame('0', (string) $dsn->scheme());
        $this->assertSame('0:0@0', (string) $dsn->authority());
        $this->assertSame('0:0', $dsn->authority()->userInfo());
        $this->assertSame('0', (string) $dsn->host());
        $this->assertSame('/0', (string) $dsn->path());
        $this->assertSame([0 => ''], $dsn->query()->all());
        $this->assertSame('0', $dsn->fragment());
        $this->assertSame('0://0:0@0/0?0=#0', (string) $dsn);
    }

    /**
     * @test
     */
    public function can_construct_falsey_dsn_parts(): void
    {
        $dsn = (new Url())
            ->withScheme('0')
            ->withHost('0')
            ->withUser('0')
            ->withPass('0')
            ->withPath('/0')
            ->withQuery([])
            ->withFragment('0')
        ;

        $this->assertSame('0', (string) $dsn->scheme());
        $this->assertSame('0:0@0', (string) $dsn->authority());
        $this->assertSame('0:0', $dsn->authority()->userInfo());
        $this->assertSame('0', (string) $dsn->host());
        $this->assertSame('/0', (string) $dsn->path());
        $this->assertSame('', (string) $dsn->query());
        $this->assertSame('0', $dsn->fragment());
        $this->assertSame('0://0:0@0/0#0', (string) $dsn);
    }

    /**
     * @test
     */
    public function scheme_is_normalized_to_lowercase(): void
    {
        $dsn = new Url('HTTP://example.com');

        $this->assertSame('http', (string) $dsn->scheme());
        $this->assertSame('http://example.com', (string) $dsn);

        $dsn = (new Url('//example.com'))->withScheme('HTTP');

        $this->assertSame('http', (string) $dsn->scheme());
        $this->assertSame('http://example.com', (string) $dsn);
    }

    /**
     * @test
     */
    public function host_is_normalized_to_lowercase(): void
    {
        $dsn = new Url('//eXaMpLe.CoM');

        $this->assertSame('example.com', (string) $dsn->host());
        $this->assertSame('//example.com', (string) $dsn);

        $dsn = (new Url())->withHost('eXaMpLe.CoM');

        $this->assertSame('example.com', (string) $dsn->host());
        $this->assertSame('//example.com', (string) $dsn);
    }

    /**
     * @test
     */
    public function port_can_be_removed(): void
    {
        $dsn = (new Url('http://example.com:8080'))->withPort(null);

        $this->assertNull($dsn->port());
        $this->assertSame('http://example.com', (string) $dsn);
    }

    /**
     * @test
     */
    public function immutability(): void
    {
        $dsn = new Url('http://user@example.com');

        $this->assertNotSame($dsn, $dsn->withScheme('https'));
        $this->assertNotSame($dsn, $dsn->withUser('user'));
        $this->assertNotSame($dsn, $dsn->withPass('pass'));
        $this->assertNotSame($dsn, $dsn->withHost('example.com'));
        $this->assertNotSame($dsn, $dsn->withPort(8080));
        $this->assertNotSame($dsn, $dsn->withPath('/path/123'));
        $this->assertNotSame($dsn, $dsn->withQuery(['q' => 'abc']));
        $this->assertNotSame($dsn, $dsn->withFragment('test'));
        $this->assertNotSame($dsn, $dsn->withoutQueryParams('test'));
        $this->assertNotSame($dsn, $dsn->withQueryParam('test', 'value'));
    }

    /**
     * @test
     */
    public function adding_user_and_pass_urlencodes_them(): void
    {
        $dsn = (new Url())->withUser('foo@bar.com')->withPass('pass#word');

        $this->assertSame('foo%40bar.com:pass%23word', $dsn->authority()->userInfo());
    }

    /**
     * @test
     */
    public function user_and_pass_are_urldecoded(): void
    {
        $dsn = new Url('http://foo%40bar.com:pass%23word@example.com');

        $this->assertSame('foo@bar.com', $dsn->user());
        $this->assertSame('pass#word', $dsn->pass());
    }

    /**
     * @test
     */
    public function prefixing_fragment_with_hash_removes_it(): void
    {
        $this->assertSame('fragment', (new Url())->withFragment('fragment')->fragment());
        $this->assertSame('fragment', (new Url())->withFragment('#fragment')->fragment());
    }

    /**
     * @test
     */
    public function default_return_values_of_getters(): void
    {
        $dsn = new Url();

        $this->assertSame('', (string) $dsn->scheme());
        $this->assertSame('', (string) $dsn->authority());
        $this->assertNull($dsn->authority()->userInfo());
        $this->assertSame('', (string) $dsn->host());
        $this->assertNull($dsn->port());
        $this->assertSame('', (string) $dsn->path());
        $this->assertSame('', (string) $dsn->query());
        $this->assertSame('', $dsn->fragment());
    }

    /**
     * @test
     */
    public function absolute(): void
    {
        $this->assertTrue((new Url('https://example.com/foo'))->isAbsolute());
        $this->assertFalse((new Url('example.com/foo'))->isAbsolute());
        $this->assertFalse((new Url('/foo'))->isAbsolute());
    }

    /**
     * @test
     */
    public function cannot_add_pass_without_user(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot have a password without a username.');

        (new Url('https://example.com/foo'))->withPass('pass');
    }

    /**
     * @test
     */
    public function can_get_extension(): void
    {
        $this->assertNull((new Url('https://example.com/foo'))->path()->extension());
        $this->assertSame('txt', (new Url('https://example.com/foo.txt'))->path()->extension());
        $this->assertSame('txt', (new Url('/foo.txt'))->path()->extension());
        $this->assertSame('txt', (new Url('file:///foo.txt'))->path()->extension());
    }

    /**
     * @test
     */
    public function can_get_the_host_tld(): void
    {
        $this->assertNull((new Url('/foo'))->host()->tld());
        $this->assertNull((new Url('http://localhost/foo'))->host()->tld());
        $this->assertSame('com', (new Url('https://example.com/foo'))->host()->tld());
        $this->assertSame('com', (new Url('https://sub1.sub2.example.com/foo'))->host()->tld());
    }

    /**
     * @test
     */
    public function can_trim_path(): void
    {
        $this->assertSame('', (new Url('http://localhost'))->path()->trim());
        $this->assertSame('foo/bar', (new Url('http://localhost/foo/bar'))->path()->trim());
        $this->assertSame('foo/bar', (new Url('http://localhost/foo/bar/'))->path()->trim());
        $this->assertSame('', (new Url('http://localhost'))->path()->ltrim());
        $this->assertSame('foo/bar', (new Url('http://localhost/foo/bar'))->path()->ltrim());
        $this->assertSame('foo/bar/', (new Url('http://localhost/foo/bar/'))->path()->ltrim());
        $this->assertSame('', (new Url('http://localhost'))->path()->rtrim());
        $this->assertSame('/foo/bar', (new Url('http://localhost/foo/bar'))->path()->rtrim());
        $this->assertSame('/foo/bar', (new Url('http://localhost/foo/bar/'))->path()->rtrim());
    }

    /**
     * @test
     */
    public function can_get_path_segments(): void
    {
        $this->assertSame([], (new Url('http://localhost'))->path()->segments());
        $this->assertSame([], (new Url('http://localhost/'))->path()->segments());
        $this->assertSame(['foo', 'bar'], (new Url('http://localhost/foo/bar'))->path()->segments());
        $this->assertSame(['foo', 'bar'], (new Url('http://localhost/foo/bar/'))->path()->segments());
        $this->assertSame(['foo', 'bar'], (new Url('/foo/bar/'))->path()->segments());
        $this->assertSame(['foo', 'bar'], (new Url('foo/bar'))->path()->segments());
        $this->assertSame(['foo', 'bar'], (new Url('foo/bar/'))->path()->segments());

        $this->assertNull((new Url('http://localhost'))->path()->segment(1));
        $this->assertSame('default', (new Url('http://localhost'))->path()->segment(1, 'default'));
        $this->assertNull((new Url('http://localhost'))->path()->segment(2));
        $this->assertNull((new Url('http://localhost/'))->path()->segment(1));
        $this->assertSame('foo', (new Url('http://localhost/foo/bar'))->path()->segment(1));
        $this->assertSame('bar', (new Url('http://localhost/foo/bar'))->path()->segment(2));
        $this->assertNull((new Url('http://localhost/foo/bar'))->path()->segment(3));
    }

    /**
     * @test
     */
    public function can_get_scheme_segments(): void
    {
        $this->assertSame([], (new Url('/foo'))->scheme()->segments());
        $this->assertSame(['http'], (new Url('http://localhost/foo/bar/'))->scheme()->segments());
        $this->assertSame(['foo', 'bar'], (new Url('foo+bar://localhost/foo/bar'))->scheme()->segments());

        $this->assertNull((new Url('/foo'))->scheme()->segment(1));
        $this->assertSame('default', (new Url('/foo'))->scheme()->segment(1, 'default'));
        $this->assertNull((new Url('/foo'))->scheme()->segment(1));
        $this->assertNull((new Url('/foo'))->scheme()->segment(2));
        $this->assertSame('foo', (new Url('foo://localhost'))->scheme()->segment(1));
        $this->assertSame('foo', (new Url('foo+bar://localhost'))->scheme()->segment(1));
        $this->assertSame('bar', (new Url('foo+bar://localhost'))->scheme()->segment(2));
        $this->assertNull((new Url('foo+bar://localhost'))->scheme()->segment(3));
    }

    /**
     * @test
     */
    public function can_get_host_segments(): void
    {
        $this->assertSame([], (new Url('/foo'))->host()->segments());
        $this->assertSame(['localhost'], (new Url('http://localhost/foo/bar/'))->host()->segments());
        $this->assertSame(['local', 'host'], (new Url('http://local.host/foo/bar'))->host()->segments());

        $this->assertNull((new Url('/foo'))->host()->segment(1));
        $this->assertSame('default', (new Url('/foo'))->host()->segment(1, 'default'));
        $this->assertNull((new Url('/foo'))->host()->segment(1));
        $this->assertNull((new Url('/foo'))->host()->segment(2));
        $this->assertSame('localhost', (new Url('http://localhost'))->host()->segment(1));
        $this->assertSame('local', (new Url('http://local.host'))->host()->segment(1));
        $this->assertSame('host', (new Url('http://local.host'))->host()->segment(2));
        $this->assertNull((new Url('http://local.host'))->host()->segment(3));
    }

    /**
     * @test
     */
    public function can_read_query_array(): void
    {
        $dsn = new Url('/');

        $this->assertSame([], $dsn->query()->all());
        $this->assertNull($dsn->query()->get('foo'));
        $this->assertSame('default', $dsn->query()->get('foo', 'default'));
        $this->assertFalse($dsn->query()->has('foo'));

        $dsn = new Url('/?a=b&c=d');

        $this->assertSame(['a' => 'b', 'c' => 'd'], $dsn->query()->all());
        $this->assertSame('d', $dsn->query()->get('c'));
        $this->assertTrue($dsn->query()->has('a'));
    }

    /**
     * @test
     */
    public function can_manipulate_query_params(): void
    {
        $dsn = new Url('/?a=b&c=d&e=f');

        $this->assertSame('/?c=d&e=f', (string) $dsn->withoutQueryParams('a'));
        $this->assertSame('/?c=d', (string) $dsn->withoutQueryParams('a', 'e'));

        $this->assertSame('/', (string) $dsn->withOnlyQueryParams('z'));
        $this->assertSame('/?a=b&e=f', (string) $dsn->withOnlyQueryParams('a', 'e', 'z'));

        $this->assertSame('/?a=foo&c=d&e=f', (string) $dsn->withQueryParam('a', 'foo'));
        $this->assertSame('/?a=b&c=d&e=f&foo=bar', (string) $dsn->withQueryParam('foo', 'bar'));
        $this->assertSame(['a' => 'b', 'c' => 'd', 'e' => 'f', 'foo' => [1, 2]], $dsn->withQueryParam('foo', [1, 2])->query()->all());
        $this->assertSame('/?a=b&c=d&e=f&foo%5B0%5D=1&foo%5B1%5D=2', (string) $dsn->withQueryParam('foo', [1, 2]));
        $this->assertSame('/?a=b&c=d&e=f&foo%5Bg%5D=h', (string) $dsn->withQueryParam('foo', ['g' => 'h']));
        $this->assertSame(['a' => 'b', 'c' => 'd', 'e' => 'f', 'foo' => ['g' => 'h']], $dsn->withQueryParam('foo', ['g' => 'h'])->query()->all());
    }

    /**
     * @test
     * @dataProvider urlComponentsEncodingProvider
     */
    public function url_components_are_properly_encoded($input, $expectedPath, $expectedQ, $expectedUser, $expectedPass, $expectedFragment, $expectedString): void
    {
        $url = new Url($input);

        $this->assertSame($expectedPath, $url->path()->value());
        $this->assertSame($expectedPath, (string) $url->path());
        $this->assertSame($expectedQ, $url->query()->get('q'));
        $this->assertSame($expectedUser, $url->user());
        $this->assertSame($expectedPass, $url->pass());
        $this->assertSame($expectedFragment, $url->fragment());
        $this->assertSame($expectedString, (string) $url);
    }

    public static function urlComponentsEncodingProvider(): iterable
    {
        // todo nested query and encoded query keys
        yield 'Percent encode spaces' => ['http://k b:p d@host/pa th/s b?q=va lue#frag ment', '/pa th/s b', 'va lue', 'k b', 'p d', 'frag ment', 'http://k%20b:p%20d@host/pa%20th/s%20b?q=va%20lue#frag%20ment'];
        yield 'Already encoded' => ['http://k%20b:p%20d@host/pa%20th/s%20b?q=va%20lue#frag%20ment', '/pa th/s b', 'va lue', 'k b', 'p d', 'frag ment', 'http://k%20b:p%20d@host/pa%20th/s%20b?q=va%20lue#frag%20ment'];
        yield 'Path segments not encoded' => ['/pa/th//two?q=va/lue#frag/ment', '/pa/th//two', 'va/lue', null, null, 'frag/ment', '/pa/th//two?q=va%2Flue#frag%2Fment'];
    }
}
