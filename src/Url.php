<?php

namespace Zenstruck\Utilities;

use Zenstruck\Utilities\Url\Authority;
use Zenstruck\Utilities\Url\Host;
use Zenstruck\Utilities\Url\Path;
use Zenstruck\Utilities\Url\Query;
use Zenstruck\Utilities\Url\Scheme;

/**
 * Wrapper for parse_url().
 *
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Url implements \Stringable
{
    private Scheme $scheme;
    private Authority $authority;
    private Path $path;
    private Query $query;
    private string $fragment;

    public function __construct(?string $value = null)
    {
        if (false === $components = \parse_url($value)) {
            throw new \InvalidArgumentException("Unable to parse \"{$value}\".");
        }

        $this->scheme = new Scheme($components['scheme'] ?? '');
        $this->path = new Path($components['path'] ?? '');
        $this->query = new Query($components['query'] ?? []);
        $this->fragment = \rawurldecode($components['fragment'] ?? '');
        $this->authority = new Authority(
            $components['host'] ?? '',
            $components['user'] ?? null,
            $components['pass'] ?? null,
            $components['port'] ?? null
        );
    }

    public function __toString(): string
    {
        $ret = '';

        if ('' !== $scheme = $this->scheme->value()) {
            $ret .= "{$scheme}:";
        }

        if ($authority = (string) $this->authority) {
            $ret .= "//{$authority}";
        }

        $ret .= $this->path->encoded();

        if ('' !== $query = (string) $this->query) {
            $ret .= "?{$query}";
        }

        if ('' !== $this->fragment) {
            $ret .= '#'.\rawurlencode($this->fragment);
        }

        return $ret;
    }

    public function scheme(): Scheme
    {
        return $this->scheme;
    }

    public function host(): Host
    {
        return $this->authority->host();
    }

    public function port(): ?int
    {
        return $this->authority->port();
    }

    public function user(): ?string
    {
        return $this->authority->username();
    }

    public function pass(): ?string
    {
        return $this->authority->password();
    }

    public function path(): Path
    {
        return $this->path;
    }

    public function query(): Query
    {
        return $this->query;
    }

    public function fragment(): string
    {
        return $this->fragment;
    }

    public function authority(): Authority
    {
        return $this->authority;
    }

    public function isAbsolute(): bool
    {
        return '' !== (string) $this->scheme;
    }

    public function withHost(?string $host): self
    {
        $url = clone $this;
        $url->authority = $this->authority->withHost($host);

        return $url;
    }

    public function withoutHost(): self
    {
        return $this->withHost(null);
    }

    public function withScheme(?string $scheme): self
    {
        $url = clone $this;
        $url->scheme = new Scheme((string) $scheme);

        return $url;
    }

    public function withoutScheme(): self
    {
        return $this->withScheme(null);
    }

    public function withPort(?int $port): self
    {
        $url = clone $this;
        $url->authority = $this->authority->withPort($port);

        return $url;
    }

    public function withoutPort(): self
    {
        return $this->withPort(null);
    }

    public function withUser(?string $user): self
    {
        $url = clone $this;
        $url->authority = $this->authority->withUsername($user);

        return $url;
    }

    public function withoutUser(): self
    {
        return $this->withUser(null);
    }

    public function withPass(?string $pass): self
    {
        $url = clone $this;
        $url->authority = $this->authority->withPassword($pass);

        return $url;
    }

    public function withoutPass(): self
    {
        return $this->withPass(null);
    }

    public function withPath(?string $path): self
    {
        $url = clone $this;
        $url->path = new Path((string) $path);

        return $url;
    }

    public function withoutPath(): self
    {
        return $this->withPath(null);
    }

    public function withQuery(?array $query): self
    {
        $url = clone $this;
        $url->query = new Query($query ?? []);

        return $url;
    }

    /**
     * @param mixed $value
     */
    public function withQueryParam(string $param, $value): self
    {
        $url = clone $this;
        $url->query = $this->query->withQueryParam($param, $value);

        return $url;
    }

    public function withOnlyQueryParams(string ...$params): self
    {
        $url = clone $this;
        $url->query = $this->query->withOnlyQueryParams(...$params);

        return $url;
    }

    public function withoutQuery(): self
    {
        return $this->withQuery(null);
    }

    public function withoutQueryParams(string ...$params): self
    {
        $url = clone $this;
        $url->query = $this->query->withoutQueryParams(...$params);

        return $url;
    }

    public function withFragment(?string $fragment): self
    {
        $url = clone $this;
        $url->fragment = \ltrim($fragment, '#');

        return $url;
    }

    public function withoutFragment(): self
    {
        return $this->withFragment(null);
    }
}
