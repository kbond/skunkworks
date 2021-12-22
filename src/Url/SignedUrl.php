<?php

namespace Zenstruck\Url;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\UriSigner;
use Zenstruck\Url;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class SignedUrl implements \Stringable
{
    use Stringable;

    private const SIGNATURE_KEY = '_hash';
    private const EXPIRES_AT_KEY = '_expires';
    private const SINGLE_USE_TOKEN_KEY = '_token';

    private Url $url;

    private function __construct(Url $url)
    {
        $this->url = $url;
    }

    /**
     * @param string|Url|Request|null $url
     */
    public static function create($url, string $secret, ?\DateTimeInterface $expiresAt = null, ?string $singleUseToken = null): self
    {
        $url = Url::new($url);

        if ($expiresAt) {
            $url = $url->withQueryParam(self::EXPIRES_AT_KEY, $expiresAt->getTimestamp());
        }

        if ($singleUseToken) {
            $url = self::signer($singleUseToken, self::SINGLE_USE_TOKEN_KEY)->sign($url);
        }

        return new self(Url::new(self::signer($secret, self::SIGNATURE_KEY)->sign($url)));
    }

    /**
     * Create from a "raw" url. Call {@see verify()} to verify.
     *
     * @param string|Url|Request|null $value {@see Url::new()}
     *
     * @return self May not be "verified" as this point
     */
    public static function from($value): self
    {
        return new self(Url::new($value));
    }

    /**
     * @param string|null $singleUseToken If passed, this value MUST change once the URL is considered "used"
     *
     * @throws \RuntimeException If unable to verify (todo, named exceptions)
     */
    public function verify(string $secret, ?string $singleUseToken = null): void
    {
        if (!self::signer($secret, self::SIGNATURE_KEY)->check($this->url)) {
            throw new \RuntimeException('Invalid signature'); // todo, named exception
        }

        $expiresAt = $this->query()->getInt(self::EXPIRES_AT_KEY);

        if ($expiresAt && \time() >= $expiresAt) {
            throw new \RuntimeException('Url has expired'); // todo, named exception
        }

        $singleUseSignature = $this->query()->get(self::SINGLE_USE_TOKEN_KEY);

        if (!$singleUseSignature && !$singleUseToken) {
            return;
        }

        if ($singleUseSignature && !$singleUseToken) {
            throw new \RuntimeException('Given Url is single use but this was not expected.'); // todo, named exception
        }

        if (!$singleUseSignature && $singleUseToken) {
            throw new \RuntimeException('Expected single user Url.'); // todo, named exception
        }

        $url = $this->url->withoutQueryParams(self::SIGNATURE_KEY);

        if (!self::signer($singleUseToken, self::SINGLE_USE_TOKEN_KEY)->check($url)) {
            throw new \RuntimeException('Url has already been used.'); // todo, named exception
        }
    }

    /**
     * @param string|null $singleUseToken If passed, this value MUST change once the URL is considered "used"
     */
    public function isVerified(string $secret, ?string $singleUseToken = null): bool
    {
        try {
            $this->verify($secret, $singleUseToken);

            return true;
        } catch (\RuntimeException $e) { // todo, named exception
            return false;
        }
    }

    public function expiresAt(): ?\DateTimeImmutable
    {
        if ($timestamp = $this->query()->getInt(self::EXPIRES_AT_KEY)) {
            return \DateTimeImmutable::createFromFormat('U', $timestamp);
        }

        return null;
    }

    public function isTemporary(): bool
    {
        return $this->query()->has(self::EXPIRES_AT_KEY);
    }

    public function isSingleUse(): bool
    {
        return $this->query()->has(self::SINGLE_USE_TOKEN_KEY);
    }

    public function scheme(): Scheme
    {
        return $this->url->scheme();
    }

    public function host(): Host
    {
        return $this->url->host();
    }

    public function port(): ?int
    {
        return $this->url->port();
    }

    public function user(): ?string
    {
        return $this->url->user();
    }

    public function pass(): ?string
    {
        return $this->url->pass();
    }

    public function path(): Path
    {
        return $this->url->path();
    }

    public function query(): Query
    {
        return $this->url->query();
    }

    public function fragment(): string
    {
        return $this->url->fragment();
    }

    public function authority(): Authority
    {
        return $this->url->authority();
    }

    public function isAbsolute(): bool
    {
        return $this->url->isAbsolute();
    }

    protected function generateString(): string
    {
        return $this->url;
    }

    private static function signer(string $secret, string $parameter): UriSigner
    {
        return new UriSigner($secret, $parameter);
    }
}
