<?php

namespace Zenstruck\Url\Tests;

use PHPUnit\Framework\TestCase;
use Zenstruck\Url;

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
    public function parses_provided_url(): void
    {
        $url = new Url('https://user:pass@example.com:8080/path/123?q=abc#test');

        $this->assertSame('https', (string) $url->scheme());
        $this->assertSame('user:pass@example.com:8080', (string) $url->authority());
        $this->assertSame('user:pass', $url->authority()->userInfo());
        $this->assertSame('user', $url->user());
        $this->assertSame('pass', $url->pass());
        $this->assertSame('example.com', (string) $url->host());
        $this->assertSame(8080, $url->port());
        $this->assertSame('/path/123', (string) $url->path());
        $this->assertSame('q=abc', (string) $url->query());
        $this->assertSame('test', $url->fragment());
        $this->assertSame('https://user:pass@example.com:8080/path/123?q=abc#test', (string) $url);
    }

    /**
     * @test
     */
    public function can_transform_and_retrieve_parts_individually(): void
    {
        $url = (new Url())
            ->withScheme('https')
            ->withHost('example.com')
            ->withUser('user')
            ->withPass('pass')
            ->withPort(8080)
            ->withPath('/path/123')
            ->withQuery(['q' => 'abc'])
            ->withFragment('test')
        ;

        $this->assertSame('https', (string) $url->scheme());
        $this->assertSame('user:pass@example.com:8080', (string) $url->authority());
        $this->assertSame('user:pass', $url->authority()->userInfo());
        $this->assertSame('example.com', (string) $url->host());
        $this->assertSame(8080, $url->port());
        $this->assertSame('/path/123', (string) $url->path());
        $this->assertSame('q=abc', (string) $url->query());
        $this->assertSame('test', $url->fragment());
        $this->assertSame('https://user:pass@example.com:8080/path/123?q=abc#test', (string) $url);
    }

    /**
     * @test
     */
    public function can_remove_all_parts(): void
    {
        $url = (new Url('https://user:pass@example.com:8080/path/123?q=abc#test'))
            ->withoutHost()
            ->withoutPass()
            ->withoutUser()
            ->withoutFragment()
            ->withoutQuery()
            ->withoutPort()
            ->withoutPath()
            ->withoutScheme()
        ;

        $this->assertSame('', (string) $url);
    }

    /**
     * @test
     */
    public function can_transform_query_with_array(): void
    {
        $url = (new Url('http://example.com?foo=bar'))
            ->withQuery(['q' => 'abc'])
        ;

        $this->assertSame('q=abc', (string) $url->query());
        $this->assertSame('http://example.com?q=abc', (string) $url);
    }

    /**
     * @test
     * @dataProvider getValidUrls
     */
    public function valid_urls_stay_valid(string $input): void
    {
        $this->assertSame($input, (string) new Url($input));
    }

    public static function getValidUrls(): iterable
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
            ['file:///var/run/foo.txt'],
            ['http://username:password@hostname:9090/path?arg=value#anchor'],
            ['http://username@hostname/path?arg=value#anchor'],
            ['http://hostname/path?arg=value#anchor'],
            ['ftp://username@hostname/path?arg=value#anchor'],
        ];
    }

    /**
     * @test
     */
    public function can_parse_filesystem_path_url(): void
    {
        $url = new Url('file:///var/run/foo.txt');

        $this->assertSame('/var/run/foo.txt', (string) $url->path());
        $this->assertSame('file', (string) $url->scheme());

        $url = new Url('file:/var/run/foo.txt');

        $this->assertSame('/var/run/foo.txt', (string) $url->path());
        $this->assertSame('file', (string) $url->scheme());
    }

    /**
     * @test
     * @dataProvider getInvalidUrls
     */
    public function invalid_urls_throw_exception(string $input): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Unable to parse \"{$input}\".");

        new Url($input);
    }

    public function getInvalidUrls(): iterable
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
    public function can_parse_falsey_url_parts(): void
    {
        $url = new Url('0://0:0@0/0?0#0');

        $this->assertSame('0', (string) $url->scheme());
        $this->assertSame('0:0@0', (string) $url->authority());
        $this->assertSame('0:0', $url->authority()->userInfo());
        $this->assertSame('0', (string) $url->host());
        $this->assertSame('/0', (string) $url->path());
        $this->assertSame([0 => ''], $url->query()->all());
        $this->assertSame('0', $url->fragment());
        $this->assertSame('0://0:0@0/0?0=#0', (string) $url);
    }

    /**
     * @test
     */
    public function can_construct_falsey_url_parts(): void
    {
        $url = (new Url())
            ->withScheme('0')
            ->withHost('0')
            ->withUser('0')
            ->withPass('0')
            ->withPath('/0')
            ->withQuery([])
            ->withFragment('0')
        ;

        $this->assertSame('0', (string) $url->scheme());
        $this->assertSame('0:0@0', (string) $url->authority());
        $this->assertSame('0:0', $url->authority()->userInfo());
        $this->assertSame('0', (string) $url->host());
        $this->assertSame('/0', (string) $url->path());
        $this->assertSame('', (string) $url->query());
        $this->assertSame('0', $url->fragment());
        $this->assertSame('0://0:0@0/0#0', (string) $url);
    }

    /**
     * @test
     */
    public function scheme_is_normalized_to_lowercase(): void
    {
        $url = new Url('HTTP://example.com');

        $this->assertSame('http', (string) $url->scheme());
        $this->assertSame('http://example.com', (string) $url);

        $url = (new Url('//example.com'))->withScheme('HTTP');

        $this->assertSame('http', (string) $url->scheme());
        $this->assertSame('http://example.com', (string) $url);
    }

    /**
     * @test
     */
    public function host_is_normalized_to_lowercase(): void
    {
        $url = new Url('//eXaMpLe.CoM');

        $this->assertSame('example.com', (string) $url->host());
        $this->assertSame('//example.com', (string) $url);

        $url = (new Url())->withHost('eXaMpLe.CoM');

        $this->assertSame('example.com', (string) $url->host());
        $this->assertSame('//example.com', (string) $url);
    }

    /**
     * @test
     */
    public function port_can_be_removed(): void
    {
        $url = (new Url('http://example.com:8080'))->withPort(null);

        $this->assertNull($url->port());
        $this->assertSame('http://example.com', (string) $url);
    }

    /**
     * @test
     */
    public function immutability(): void
    {
        $url = new Url('http://user@example.com');

        $this->assertNotSame($url, $url->withScheme('https'));
        $this->assertNotSame($url, $url->withUser('user'));
        $this->assertNotSame($url, $url->withPass('pass'));
        $this->assertNotSame($url, $url->withHost('example.com'));
        $this->assertNotSame($url, $url->withPort(8080));
        $this->assertNotSame($url, $url->withPath('/path/123'));
        $this->assertNotSame($url, $url->withQuery(['q' => 'abc']));
        $this->assertNotSame($url, $url->withFragment('test'));
        $this->assertNotSame($url, $url->withoutQueryParams('test'));
        $this->assertNotSame($url, $url->withQueryParam('test', 'value'));
    }

    /**
     * @test
     */
    public function adding_user_and_pass_urlencodes_them(): void
    {
        $url = (new Url())->withUser('foo@bar.com')->withPass('pass#word');

        $this->assertSame('foo%40bar.com:pass%23word', $url->authority()->userInfo());
    }

    /**
     * @test
     */
    public function user_and_pass_are_urldecoded(): void
    {
        $url = new Url('http://foo%40bar.com:pass%23word@example.com');

        $this->assertSame('foo@bar.com', $url->user());
        $this->assertSame('pass#word', $url->pass());
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
        $url = new Url();

        $this->assertSame('', (string) $url->scheme());
        $this->assertSame('', (string) $url->authority());
        $this->assertNull($url->authority()->userInfo());
        $this->assertSame('', (string) $url->host());
        $this->assertNull($url->port());
        $this->assertSame('', (string) $url->path());
        $this->assertSame('', (string) $url->query());
        $this->assertSame('', $url->fragment());
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
    public function path_without_host(): void
    {
        $this->assertSame('https://example.com', (new Url('https://example.com'))->toString());
        $this->assertSame('https://example.com/foo', (new Url('https://example.com'))->withPath('foo')->toString());
        $this->assertSame('https://example.com/foo', (new Url('https://example.com'))->withPath('/foo')->toString());
        $this->assertSame('https://example.com/', (new Url('https://example.com/'))->toString());
        $this->assertSame('foo/bar', (new Url())->withPath('foo/bar')->toString());
        $this->assertSame('/foo/bar', (new Url())->withPath('/foo/bar')->toString());
        $this->assertSame('//example.com/foo/bar', (new Url('foo/bar'))->withHost('example.com')->toString());
        $this->assertSame('//example.com/foo/bar', (new Url('/foo/bar'))->withHost('example.com')->toString());
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
     * @dataProvider validAbsolutePaths
     */
    public function can_get_absolute_path($url, $expected): void
    {
        $this->assertSame($expected, Url::create($url)->path()->absolute());
    }

    public static function validAbsolutePaths(): iterable
    {
        yield ['http://localhost', '/'];
        yield ['http://localhost/', '/'];
        yield ['http://localhost//', '/'];
        yield ['http://localhost/./', '/'];
        yield ['http://localhost/foo/bar', '/foo/bar'];
        yield ['http://localhost/foo/bar/', '/foo/bar/'];
        yield ['foo/bar', '/foo/bar'];
        yield ['/foo/bar', '/foo/bar'];
        yield ['foo/bar/../baz', '/foo/baz'];
        yield ['/foo/bar/../baz', '/foo/baz'];
        yield ['/foo/bar/../baz/../qux', '/foo/qux'];
        yield ['foo/bar/../..', '/'];
        yield ['foo/bar/../../', '/'];
        yield ['foo/bar/.//baz/.././..', '/foo'];
        yield ['foo/bar/.//baz/.././../', '/foo/'];
    }

    /**
     * @test
     * @dataProvider invalidAbsolutePaths
     */
    public function cannot_get_absolute_path_outside_root($url): void
    {
        $this->expectException(Url\Exception\PathOutsideRoot::class);

        Url::create($url)->path()->absolute();
    }

    public static function invalidAbsolutePaths(): iterable
    {
        yield ['http://localhost/..'];
        yield ['http://localhost/../'];
        yield ['http://localhost/foo/bar/../../..'];
        yield ['http://localhost/foo/bar/../../../'];
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
    public function can_append_path(): void
    {
        $this->assertSame('http://localhost', (new Url('http://localhost'))->appendPath('')->toString());
        $this->assertSame('http://localhost/', (new Url('http://localhost/'))->appendPath('')->toString());
        $this->assertSame('http://localhost/', (new Url('http://localhost'))->appendPath('/')->toString());
        $this->assertSame('http://localhost/', (new Url('http://localhost/'))->appendPath('/')->toString());
        $this->assertSame('http://localhost/', (new Url('http://localhost'))->appendPath('/')->toString());
        $this->assertSame('http://localhost/foo', (new Url('http://localhost'))->appendPath('foo')->toString());
        $this->assertSame('http://localhost/foo', (new Url('http://localhost'))->appendPath('/foo')->toString());
        $this->assertSame('http://localhost/foo/bar', (new Url('http://localhost'))->appendPath('/foo/bar')->toString());
        $this->assertSame('http://localhost/foo/bar/baz', (new Url('http://localhost/foo'))->appendPath('/bar/baz')->toString());
        $this->assertSame('http://localhost/foo/bar/baz', (new Url('http://localhost/foo/'))->appendPath('/bar/baz')->toString());
        $this->assertSame('http://localhost/foo/bar/baz', (new Url('http://localhost/foo'))->appendPath('bar/baz')->toString());
        $this->assertSame('http://localhost/foo/bar/baz', (new Url('http://localhost/foo/'))->appendPath('bar/baz')->toString());
        $this->assertSame('http://localhost/foo/bar/baz/', (new Url('http://localhost/foo'))->appendPath('/bar/baz/')->toString());
        $this->assertSame('http://localhost/foo/bar/baz/', (new Url('http://localhost/foo/'))->appendPath('/bar/baz/')->toString());
        $this->assertSame('http://localhost/foo/bar/baz/', (new Url('http://localhost/foo'))->appendPath('bar/baz/')->toString());
        $this->assertSame('http://localhost/foo/bar/baz/', (new Url('http://localhost/foo/'))->appendPath('bar/baz/')->toString());
        $this->assertSame('foo', (new Url())->appendPath('foo')->toString());
        $this->assertSame('/foo', (new Url())->appendPath('/foo')->toString());
        $this->assertSame('foo/bar', (new Url('foo'))->appendPath('bar')->toString());
        $this->assertSame('foo/bar', (new Url('foo'))->appendPath('/bar')->toString());
        $this->assertSame('/foo/bar', (new Url('/foo'))->appendPath('bar')->toString());
        $this->assertSame('/foo/bar', (new Url('/foo'))->appendPath('/bar')->toString());
    }

    /**
     * @test
     */
    public function can_prepend_path(): void
    {
        $this->assertSame('http://localhost', (new Url('http://localhost'))->prependPath('')->toString());
        $this->assertSame('http://localhost/', (new Url('http://localhost'))->prependPath('/')->toString());
        $this->assertSame('http://localhost/', (new Url('http://localhost/'))->prependPath('/')->toString());
        $this->assertSame('http://localhost/', (new Url('http://localhost'))->prependPath('/')->toString());
        $this->assertSame('http://localhost/foo', (new Url('http://localhost'))->prependPath('foo')->toString());
        $this->assertSame('http://localhost/foo', (new Url('http://localhost'))->prependPath('/foo')->toString());
        $this->assertSame('http://localhost/foo/bar', (new Url('http://localhost'))->prependPath('/foo/bar')->toString());
        $this->assertSame('http://localhost/bar/baz/foo', (new Url('http://localhost/foo'))->prependPath('/bar/baz')->toString());
        $this->assertSame('http://localhost/bar/baz/foo/', (new Url('http://localhost/foo/'))->prependPath('/bar/baz')->toString());
        $this->assertSame('http://localhost/bar/baz/foo', (new Url('http://localhost/foo'))->prependPath('bar/baz')->toString());
        $this->assertSame('http://localhost/bar/baz/foo/', (new Url('http://localhost/foo/'))->prependPath('bar/baz')->toString());
        $this->assertSame('http://localhost/bar/baz/foo', (new Url('http://localhost/foo'))->prependPath('/bar/baz/')->toString());
        $this->assertSame('http://localhost/bar/baz/foo/', (new Url('http://localhost/foo/'))->prependPath('/bar/baz/')->toString());
        $this->assertSame('http://localhost/bar/baz/foo', (new Url('http://localhost/foo'))->prependPath('bar/baz/')->toString());
        $this->assertSame('http://localhost/bar/baz/foo/', (new Url('http://localhost/foo/'))->prependPath('bar/baz/')->toString());
        $this->assertSame('foo', (new Url())->prependPath('foo')->toString());
        $this->assertSame('/foo', (new Url())->prependPath('/foo')->toString());
        $this->assertSame('bar/foo', (new Url('foo'))->prependPath('bar')->toString());
        $this->assertSame('/bar/foo', (new Url('foo'))->prependPath('/bar')->toString());
        $this->assertSame('/bar/foo', (new Url('/foo'))->prependPath('/bar')->toString());
        $this->assertSame('/bar/foo', (new Url('/foo'))->prependPath('bar')->toString()); // absolute path must remain absolute
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
    public function scheme_equals(): void
    {
        $this->assertFalse((new Url('/foo'))->scheme()->equals('http'));
        $this->assertTrue((new Url('/foo'))->scheme()->equals(''));
        $this->assertTrue((new Url('http://localhost/foo'))->scheme()->equals('http'));
        $this->assertFalse((new Url('http://localhost/foo'))->scheme()->equals('https'));
    }

    /**
     * @test
     */
    public function scheme_in(): void
    {
        $this->assertFalse((new Url('/foo'))->scheme()->in(['http', 'https']));
        $this->assertTrue((new Url('/foo'))->scheme()->in(['http', 'https', '']));
        $this->assertTrue((new Url('http://localhost/foo'))->scheme()->in(['http', 'https']));
        $this->assertFalse((new Url('ftp://localhost/foo'))->scheme()->in(['http', 'https']));
    }

    /**
     * @test
     */
    public function scheme_contains(): void
    {
        $this->assertFalse((new Url('/foo'))->scheme()->contains('ftp'));
        $this->assertTrue((new Url('foo+bar://localhost/foo'))->scheme()->contains('foo'));
        $this->assertTrue((new Url('foo+bar://localhost/foo'))->scheme()->contains('bar'));
        $this->assertFalse((new Url('foo+bar://localhost/foo'))->scheme()->contains('ftp'));
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
        $url = new Url('/');

        $this->assertSame([], $url->query()->all());
        $this->assertNull($url->query()->get('foo'));
        $this->assertSame('default', $url->query()->get('foo', 'default'));
        $this->assertFalse($url->query()->has('foo'));

        $url = new Url('/?a=b&c=d');

        $this->assertSame(['a' => 'b', 'c' => 'd'], $url->query()->all());
        $this->assertSame('d', $url->query()->get('c'));
        $this->assertTrue($url->query()->has('a'));
    }

    /**
     * @test
     */
    public function can_manipulate_query_params(): void
    {
        $url = new Url('/?a=b&c=d&e=f');

        $this->assertSame('/?c=d&e=f', (string) $url->withoutQueryParams('a'));
        $this->assertSame('/?c=d', (string) $url->withoutQueryParams('a', 'e'));

        $this->assertSame('/', (string) $url->withOnlyQueryParams('z'));
        $this->assertSame('/?a=b&e=f', (string) $url->withOnlyQueryParams('a', 'e', 'z'));

        $this->assertSame('/?a=foo&c=d&e=f', (string) $url->withQueryParam('a', 'foo'));
        $this->assertSame('/?a=b&c=d&e=f&foo=bar', (string) $url->withQueryParam('foo', 'bar'));
        $this->assertSame(['a' => 'b', 'c' => 'd', 'e' => 'f', 'foo' => [1, 2]], $url->withQueryParam('foo', [1, 2])->query()->all());
        $this->assertSame('/?a=b&c=d&e=f&foo%5B0%5D=1&foo%5B1%5D=2', (string) $url->withQueryParam('foo', [1, 2]));
        $this->assertSame('/?a=b&c=d&e=f&foo%5Bg%5D=h', (string) $url->withQueryParam('foo', ['g' => 'h']));
        $this->assertSame(['a' => 'b', 'c' => 'd', 'e' => 'f', 'foo' => ['g' => 'h']], $url->withQueryParam('foo', ['g' => 'h'])->query()->all());
    }

    /**
     * @test
     * @dataProvider urlComponentsEncodingProvider
     */
    public function url_components_are_properly_encoded($input, $expectedPath, $expectedQ, $expectedUser, $expectedPass, $expectedFragment, $expectedString): void
    {
        $url = new Url($input);

        $this->assertSame($expectedPath, $url->path()->toString());
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
