<?php

namespace Zenstruck\Url\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
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
        $url = 'https://user:pass@example.com:8080/path/123?q=abc#test';

        $urls = [
            Url::new($url),
            Url::new(new class($url) {
                private $url;

                public function __construct($url)
                {
                    $this->url = $url;
                }

                public function __toString(): string
                {
                    return $this->url;
                }
            }),
        ];

        foreach ($urls as $url) {
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
    }

    /**
     * @test
     */
    public function can_parse_symfony_request(): void
    {
        $url = Url::new(Request::create('https://user:pass@example.com:8080/path/123?q=abc#test'));

        $this->assertSame('https', (string) $url->scheme());
        $this->assertSame('user:pass@example.com:8080', (string) $url->authority());
        $this->assertSame('user:pass', $url->authority()->userInfo());
        $this->assertSame('user', $url->user());
        $this->assertSame('pass', $url->pass());
        $this->assertSame('example.com', (string) $url->host());
        $this->assertSame(8080, $url->port());
        $this->assertSame('/path/123', (string) $url->path());
        $this->assertSame('q=abc', (string) $url->query());
        $this->assertSame('', $url->fragment()); // Symfony Requests do not contain a fragment
        $this->assertSame('https://user:pass@example.com:8080/path/123?q=abc', (string) $url);
    }

    /**
     * @test
     */
    public function can_parse_symfony_request_without_user_info(): void
    {
        $url = Url::new(Request::create('https://example.com:8080/path/123?q=abc#test'));

        $this->assertSame('https', (string) $url->scheme());
        $this->assertSame('example.com:8080', (string) $url->authority());
        $this->assertNull($url->authority()->userInfo());
        $this->assertNull($url->user());
        $this->assertNull($url->pass());
        $this->assertSame('example.com', (string) $url->host());
        $this->assertSame(8080, $url->port());
        $this->assertSame('/path/123', (string) $url->path());
        $this->assertSame('q=abc', (string) $url->query());
        $this->assertSame('', $url->fragment()); // Symfony Requests do not contain a fragment
        $this->assertSame('https://example.com:8080/path/123?q=abc', (string) $url);
    }

    /**
     * @test
     */
    public function can_transform_and_retrieve_parts_individually(): void
    {
        $url = Url::new()
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
        $url = Url::new('https://user:pass@example.com:8080/path/123?q=abc#test')
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
        $url = Url::new('http://example.com?foo=bar')
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
        $this->assertSame($input, (string) Url::new($input));
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
        $url = Url::new('file:///var/run/foo.txt');

        $this->assertSame('/var/run/foo.txt', (string) $url->path());
        $this->assertSame('file', (string) $url->scheme());

        $url = Url::new('file:/var/run/foo.txt');

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

        Url::new($input);
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

        Url::new('//example.com')->withPort(100000);
    }

    /**
     * @test
     */
    public function with_port_cannot_be_negative(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid port: -1. Must be between 0 and 65535.');

        Url::new('//example.com')->withPort(-1);
    }

    /**
     * @test
     */
    public function can_parse_falsey_url_parts(): void
    {
        $url = Url::new('0://0:0@0/0?0#0');

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
        $url = Url::new()
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
        $url = Url::new('HTTP://example.com');

        $this->assertSame('http', (string) $url->scheme());
        $this->assertSame('http://example.com', (string) $url);

        $url = Url::new('//example.com')->withScheme('HTTP');

        $this->assertSame('http', (string) $url->scheme());
        $this->assertSame('http://example.com', (string) $url);
    }

    /**
     * @test
     */
    public function host_is_normalized_to_lowercase(): void
    {
        $url = Url::new('//eXaMpLe.CoM');

        $this->assertSame('example.com', (string) $url->host());
        $this->assertSame('//example.com', (string) $url);

        $url = Url::new()->withHost('eXaMpLe.CoM');

        $this->assertSame('example.com', (string) $url->host());
        $this->assertSame('//example.com', (string) $url);
    }

    /**
     * @test
     */
    public function port_can_be_removed(): void
    {
        $url = Url::new('http://example.com:8080')->withPort(null);

        $this->assertNull($url->port());
        $this->assertSame('http://example.com', (string) $url);
    }

    /**
     * @test
     */
    public function immutability(): void
    {
        $url = Url::new('http://user@example.com');

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
    public function manipulating_parts_urlencodes_them(): void
    {
        $url = Url::new('https://example.com')
            ->withUser('foo@bar.com')
            ->withPass('pass#word')
            ->withPath('/foo bar/baz')
            ->withQuery(['foo' => 'bar baz'])
        ;

        $this->assertSame('https://foo%40bar.com:pass%23word@example.com/foo%20bar/baz?foo=bar%20baz', $url->toString());
        $this->assertSame('foo%40bar.com:pass%23word', $url->authority()->userInfo());
    }

    /**
     * @test
     */
    public function parts_are_url_decoded(): void
    {
        $url = Url::new('http://foo%40bar.com:pass%23word@example.com/foo%20bar/baz?foo=bar%20baz');

        $this->assertSame('foo@bar.com', $url->user());
        $this->assertSame('pass#word', $url->pass());
        $this->assertSame('/foo bar/baz', $url->path()->toString());
        $this->assertSame(['foo' => 'bar baz'], $url->query()->all());
    }

    /**
     * @test
     */
    public function prefixing_fragment_with_hash_removes_it(): void
    {
        $this->assertSame('fragment', Url::new()->withFragment('fragment')->fragment());
        $this->assertSame('fragment', Url::new()->withFragment('#fragment')->fragment());
    }

    /**
     * @test
     */
    public function default_return_values_of_getters(): void
    {
        $url = Url::new();

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
        $this->assertTrue(Url::new('https://example.com/foo')->isAbsolute());
        $this->assertFalse(Url::new('example.com/foo')->isAbsolute());
        $this->assertFalse(Url::new('/foo')->isAbsolute());
    }

    /**
     * @test
     */
    public function cannot_add_pass_without_user(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot have a password without a username.');

        Url::new('https://example.com/foo')->withPass('pass');
    }

    /**
     * @test
     */
    public function path_without_host(): void
    {
        $this->assertSame('https://example.com', Url::new('https://example.com')->toString());
        $this->assertSame('https://example.com/foo', Url::new('https://example.com')->withPath('foo')->toString());
        $this->assertSame('https://example.com/foo', Url::new('https://example.com')->withPath('/foo')->toString());
        $this->assertSame('https://example.com/', Url::new('https://example.com/')->toString());
        $this->assertSame('foo/bar', Url::new()->withPath('foo/bar')->toString());
        $this->assertSame('/foo/bar', Url::new()->withPath('/foo/bar')->toString());
        $this->assertSame('//example.com/foo/bar', Url::new('foo/bar')->withHost('example.com')->toString());
        $this->assertSame('//example.com/foo/bar', Url::new('/foo/bar')->withHost('example.com')->toString());
    }

    /**
     * @test
     */
    public function can_get_extension(): void
    {
        $this->assertNull(Url::new('https://example.com/foo')->path()->extension());
        $this->assertSame('txt', Url::new('https://example.com/foo.txt')->path()->extension());
        $this->assertSame('txt', Url::new('/foo.txt')->path()->extension());
        $this->assertSame('txt', Url::new('file:///foo.txt')->path()->extension());
    }

    /**
     * @test
     */
    public function can_get_dirname(): void
    {
        $this->assertSame('/', Url::new('https://example.com/')->path()->dirname());
        $this->assertSame('/', Url::new('https://example.com')->path()->dirname());
        $this->assertSame('/', Url::new('https://example.com/foo.txt')->path()->dirname());
        $this->assertSame('/', Url::new('/foo.txt')->path()->dirname());
        $this->assertSame('/', Url::new('file:///foo.txt')->path()->dirname());
        $this->assertSame('/foo/bar', Url::new('https://example.com/foo/bar/baz.txt')->path()->dirname());
        $this->assertSame('/foo/bar', Url::new('https://example.com/foo/bar/baz')->path()->dirname());
        $this->assertSame('/foo/bar', Url::new('https://example.com/foo/bar/baz/')->path()->dirname());
    }

    /**
     * @test
     */
    public function can_get_filename(): void
    {
        $this->assertNull(Url::new('https://example.com/')->path()->filename());
        $this->assertNull(Url::new('https://example.com')->path()->filename());
        $this->assertSame('foo', Url::new('https://example.com/foo.txt')->path()->filename());
        $this->assertSame('foo', Url::new('/foo.txt')->path()->filename());
        $this->assertSame('foo', Url::new('file:///foo.txt')->path()->filename());
        $this->assertSame('baz', Url::new('https://example.com/foo/bar/baz.txt')->path()->filename());
        $this->assertSame('baz', Url::new('https://example.com/foo/bar/baz')->path()->filename());
        $this->assertSame('baz', Url::new('https://example.com/foo/bar/baz/')->path()->filename());
    }

    /**
     * @test
     */
    public function can_get_basename(): void
    {
        $this->assertNull(Url::new('https://example.com/')->path()->basename());
        $this->assertNull(Url::new('https://example.com')->path()->basename());
        $this->assertSame('foo.txt', Url::new('https://example.com/foo.txt')->path()->basename());
        $this->assertSame('foo.txt', Url::new('/foo.txt')->path()->basename());
        $this->assertSame('foo.txt', Url::new('file:///foo.txt')->path()->basename());
        $this->assertSame('baz.txt', Url::new('https://example.com/foo/bar/baz.txt')->path()->basename());
        $this->assertSame('baz', Url::new('https://example.com/foo/bar/baz')->path()->basename());
        $this->assertSame('baz', Url::new('https://example.com/foo/bar/baz/')->path()->basename());
    }

    /**
     * @test
     */
    public function can_get_the_host_tld(): void
    {
        $this->assertNull(Url::new('/foo')->host()->tld());
        $this->assertNull(Url::new('http://localhost/foo')->host()->tld());
        $this->assertSame('com', Url::new('https://example.com/foo')->host()->tld());
        $this->assertSame('com', Url::new('https://sub1.sub2.example.com/foo')->host()->tld());
    }

    /**
     * @test
     */
    public function can_trim_path(): void
    {
        $this->assertSame('', Url::new('http://localhost')->path()->trim());
        $this->assertSame('foo/bar', Url::new('http://localhost/foo/bar')->path()->trim());
        $this->assertSame('foo/bar', Url::new('http://localhost/foo/bar/')->path()->trim());
        $this->assertSame('', Url::new('http://localhost')->path()->ltrim());
        $this->assertSame('foo/bar', Url::new('http://localhost/foo/bar')->path()->ltrim());
        $this->assertSame('foo/bar/', Url::new('http://localhost/foo/bar/')->path()->ltrim());
        $this->assertSame('', Url::new('http://localhost')->path()->rtrim());
        $this->assertSame('/foo/bar', Url::new('http://localhost/foo/bar')->path()->rtrim());
        $this->assertSame('/foo/bar', Url::new('http://localhost/foo/bar/')->path()->rtrim());
    }

    /**
     * @test
     * @dataProvider validAbsolutePaths
     */
    public function can_get_absolute_path($url, $expected): void
    {
        $this->assertSame($expected, Url::new($url)->path()->absolute());
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

        Url::new($url)->path()->absolute();
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
        $this->assertSame([], Url::new('http://localhost')->path()->segments());
        $this->assertSame([], Url::new('http://localhost/')->path()->segments());
        $this->assertSame(['foo', 'bar'], Url::new('http://localhost/foo/bar')->path()->segments());
        $this->assertSame(['foo', 'bar'], Url::new('http://localhost/foo/bar/')->path()->segments());
        $this->assertSame(['foo', 'bar'], Url::new('/foo/bar/')->path()->segments());
        $this->assertSame(['foo', 'bar'], Url::new('foo/bar')->path()->segments());
        $this->assertSame(['foo', 'bar'], Url::new('foo/bar/')->path()->segments());

        $this->assertNull(Url::new('http://localhost')->path()->segment(0));
        $this->assertSame('default', Url::new('http://localhost')->path()->segment(0, 'default'));
        $this->assertNull(Url::new('http://localhost')->path()->segment(2));
        $this->assertNull(Url::new('http://localhost/')->path()->segment(1));
        $this->assertSame('foo', Url::new('http://localhost/foo/bar')->path()->segment(0));
        $this->assertSame('bar', Url::new('http://localhost/foo/bar')->path()->segment(1));
        $this->assertNull(Url::new('http://localhost/foo/bar')->path()->segment(2));
    }

    /**
     * @test
     */
    public function can_append_path(): void
    {
        $this->assertSame('http://localhost', Url::new('http://localhost')->appendPath('')->toString());
        $this->assertSame('http://localhost/', Url::new('http://localhost/')->appendPath('')->toString());
        $this->assertSame('http://localhost/', Url::new('http://localhost')->appendPath('/')->toString());
        $this->assertSame('http://localhost/', Url::new('http://localhost/')->appendPath('/')->toString());
        $this->assertSame('http://localhost/', Url::new('http://localhost')->appendPath('/')->toString());
        $this->assertSame('http://localhost/foo', Url::new('http://localhost')->appendPath('foo')->toString());
        $this->assertSame('http://localhost/foo', Url::new('http://localhost')->appendPath('/foo')->toString());
        $this->assertSame('http://localhost/foo/bar', Url::new('http://localhost')->appendPath('/foo/bar')->toString());
        $this->assertSame('http://localhost/foo/bar/baz', Url::new('http://localhost/foo')->appendPath('/bar/baz')->toString());
        $this->assertSame('http://localhost/foo/bar/baz', Url::new('http://localhost/foo/')->appendPath('/bar/baz')->toString());
        $this->assertSame('http://localhost/foo/bar/baz', Url::new('http://localhost/foo')->appendPath('bar/baz')->toString());
        $this->assertSame('http://localhost/foo/bar/baz', Url::new('http://localhost/foo/')->appendPath('bar/baz')->toString());
        $this->assertSame('http://localhost/foo/bar/baz/', Url::new('http://localhost/foo')->appendPath('/bar/baz/')->toString());
        $this->assertSame('http://localhost/foo/bar/baz/', Url::new('http://localhost/foo/')->appendPath('/bar/baz/')->toString());
        $this->assertSame('http://localhost/foo/bar/baz/', Url::new('http://localhost/foo')->appendPath('bar/baz/')->toString());
        $this->assertSame('http://localhost/foo/bar/baz/', Url::new('http://localhost/foo/')->appendPath('bar/baz/')->toString());
        $this->assertSame('foo', Url::new()->appendPath('foo')->toString());
        $this->assertSame('/foo', Url::new()->appendPath('/foo')->toString());
        $this->assertSame('foo/bar', Url::new('foo')->appendPath('bar')->toString());
        $this->assertSame('foo/bar', Url::new('foo')->appendPath('/bar')->toString());
        $this->assertSame('/foo/bar', Url::new('/foo')->appendPath('bar')->toString());
        $this->assertSame('/foo/bar', Url::new('/foo')->appendPath('/bar')->toString());
    }

    /**
     * @test
     */
    public function can_prepend_path(): void
    {
        $this->assertSame('http://localhost', Url::new('http://localhost')->prependPath('')->toString());
        $this->assertSame('http://localhost/', Url::new('http://localhost')->prependPath('/')->toString());
        $this->assertSame('http://localhost/', Url::new('http://localhost/')->prependPath('/')->toString());
        $this->assertSame('http://localhost/', Url::new('http://localhost')->prependPath('/')->toString());
        $this->assertSame('http://localhost/foo', Url::new('http://localhost')->prependPath('foo')->toString());
        $this->assertSame('http://localhost/foo', Url::new('http://localhost')->prependPath('/foo')->toString());
        $this->assertSame('http://localhost/foo/bar', Url::new('http://localhost')->prependPath('/foo/bar')->toString());
        $this->assertSame('http://localhost/bar/baz/foo', Url::new('http://localhost/foo')->prependPath('/bar/baz')->toString());
        $this->assertSame('http://localhost/bar/baz/foo/', Url::new('http://localhost/foo/')->prependPath('/bar/baz')->toString());
        $this->assertSame('http://localhost/bar/baz/foo', Url::new('http://localhost/foo')->prependPath('bar/baz')->toString());
        $this->assertSame('http://localhost/bar/baz/foo/', Url::new('http://localhost/foo/')->prependPath('bar/baz')->toString());
        $this->assertSame('http://localhost/bar/baz/foo', Url::new('http://localhost/foo')->prependPath('/bar/baz/')->toString());
        $this->assertSame('http://localhost/bar/baz/foo/', Url::new('http://localhost/foo/')->prependPath('/bar/baz/')->toString());
        $this->assertSame('http://localhost/bar/baz/foo', Url::new('http://localhost/foo')->prependPath('bar/baz/')->toString());
        $this->assertSame('http://localhost/bar/baz/foo/', Url::new('http://localhost/foo/')->prependPath('bar/baz/')->toString());
        $this->assertSame('foo', Url::new()->prependPath('foo')->toString());
        $this->assertSame('/foo', Url::new()->prependPath('/foo')->toString());
        $this->assertSame('bar/foo', Url::new('foo')->prependPath('bar')->toString());
        $this->assertSame('/bar/foo', Url::new('foo')->prependPath('/bar')->toString());
        $this->assertSame('/bar/foo', Url::new('/foo')->prependPath('/bar')->toString());
        $this->assertSame('/bar/foo', Url::new('/foo')->prependPath('bar')->toString()); // absolute path must remain absolute
    }

    /**
     * @test
     */
    public function can_get_scheme_segments(): void
    {
        $this->assertSame([], Url::new('/foo')->scheme()->segments());
        $this->assertSame(['http'], Url::new('http://localhost/foo/bar/')->scheme()->segments());
        $this->assertSame(['foo', 'bar'], Url::new('foo+bar://localhost/foo/bar')->scheme()->segments());

        $this->assertNull(Url::new('/foo')->scheme()->segment(0));
        $this->assertSame('default', Url::new('/foo')->scheme()->segment(0, 'default'));
        $this->assertNull(Url::new('/foo')->scheme()->segment(2));
        $this->assertSame('foo', Url::new('foo://localhost')->scheme()->segment(0));
        $this->assertSame('foo', Url::new('foo+bar://localhost')->scheme()->segment(0));
        $this->assertSame('bar', Url::new('foo+bar://localhost')->scheme()->segment(1));
        $this->assertNull(Url::new('foo+bar://localhost')->scheme()->segment(2));
    }

    /**
     * @test
     */
    public function scheme_equals(): void
    {
        $this->assertFalse(Url::new('/foo')->scheme()->equals('http'));
        $this->assertTrue(Url::new('/foo')->scheme()->equals(''));
        $this->assertTrue(Url::new('http://localhost/foo')->scheme()->equals('http'));
        $this->assertFalse(Url::new('http://localhost/foo')->scheme()->equals('https'));
    }

    /**
     * @test
     */
    public function scheme_in(): void
    {
        $this->assertFalse(Url::new('/foo')->scheme()->in(['http', 'https']));
        $this->assertTrue(Url::new('/foo')->scheme()->in(['http', 'https', '']));
        $this->assertTrue(Url::new('http://localhost/foo')->scheme()->in(['http', 'https']));
        $this->assertFalse(Url::new('ftp://localhost/foo')->scheme()->in(['http', 'https']));
    }

    /**
     * @test
     */
    public function scheme_contains(): void
    {
        $this->assertFalse(Url::new('/foo')->scheme()->contains('ftp'));
        $this->assertTrue(Url::new('foo+bar://localhost/foo')->scheme()->contains('foo'));
        $this->assertTrue(Url::new('foo+bar://localhost/foo')->scheme()->contains('bar'));
        $this->assertFalse(Url::new('foo+bar://localhost/foo')->scheme()->contains('ftp'));
    }

    /**
     * @test
     */
    public function can_get_host_segments(): void
    {
        $this->assertSame([], Url::new('/foo')->host()->segments());
        $this->assertSame(['localhost'], Url::new('http://localhost/foo/bar/')->host()->segments());
        $this->assertSame(['local', 'host'], Url::new('http://local.host/foo/bar')->host()->segments());

        $this->assertNull(Url::new('/foo')->host()->segment(0));
        $this->assertSame('default', Url::new('/foo')->host()->segment(1, 'default'));
        $this->assertNull(Url::new('/foo')->host()->segment(1));
        $this->assertSame('localhost', Url::new('http://localhost')->host()->segment(0));
        $this->assertSame('local', Url::new('http://local.host')->host()->segment(0));
        $this->assertSame('host', Url::new('http://local.host')->host()->segment(1));
        $this->assertNull(Url::new('http://local.host')->host()->segment(2));
    }

    /**
     * @test
     */
    public function can_read_query_array(): void
    {
        $url = Url::new('/');

        $this->assertSame([], $url->query()->all());
        $this->assertNull($url->query()->get('foo'));
        $this->assertSame('default', $url->query()->get('foo', 'default'));
        $this->assertFalse($url->query()->has('foo'));

        $url = Url::new('/?a=b&c=d');

        $this->assertSame(['a' => 'b', 'c' => 'd'], $url->query()->all());
        $this->assertSame('d', $url->query()->get('c'));
        $this->assertTrue($url->query()->has('a'));
    }

    /**
     * @test
     */
    public function can_manipulate_query_params(): void
    {
        $url = Url::new('/?a=b&c=d&e=f');

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
     */
    public function can_get_query_param_as_boolean(): void
    {
        $this->assertTrue(Url::new('/?foo=true')->query()->getBool('foo'));
        $this->assertTrue(Url::new('/?foo=1')->query()->getBool('foo'));
        $this->assertFalse(Url::new('/?foo=false')->query()->getBool('foo'));
        $this->assertFalse(Url::new('/?foo=0')->query()->getBool('foo'));
        $this->assertFalse(Url::new('/?foo=something')->query()->getBool('foo'));
        $this->assertTrue(Url::new('/')->query()->getBool('foo', true));
        $this->assertFalse(Url::new('/')->query()->getBool('foo', false));
        $this->assertFalse(Url::new('/?foo[]=bar')->query()->getBool('foo', true));
    }

    /**
     * @test
     */
    public function can_get_query_param_as_int(): void
    {
        $this->assertSame(5, Url::new('/?foo=5')->query()->getInt('foo'));
        $this->assertSame(0, Url::new('/?foo=something')->query()->getInt('foo'));
        $this->assertSame(0, Url::new('/')->query()->getInt('foo'));
        $this->assertSame(1, Url::new('/?foo[]=bar')->query()->getInt('foo'));
        $this->assertSame(3, Url::new('/')->query()->getInt('foo', 3));
    }

    /**
     * @test
     */
    public function can_pass_throwable_to_query_get_default(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('invalid');

        Url::new('/?foo=5')->query()->get('bar', new \RuntimeException('invalid'));
    }

    /**
     * @test
     */
    public function can_pass_throwable_to_query_get_bool_default(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('invalid');

        Url::new('/?foo=5')->query()->getBool('bar', new \RuntimeException('invalid'));
    }

    /**
     * @test
     */
    public function can_pass_throwable_to_query_get_int_default(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('invalid');

        Url::new('/?foo=5')->query()->getInt('bar', new \RuntimeException('invalid'));
    }

    /**
     * @test
     * @dataProvider urlComponentsEncodingProvider
     */
    public function url_components_are_properly_encoded($input, $expectedPath, $expectedQ, $expectedUser, $expectedPass, $expectedFragment, $expectedString): void
    {
        $url = Url::new($input);

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
