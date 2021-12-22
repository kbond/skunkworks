<?php

namespace Zenstruck\Url\SignedUrl;

use Zenstruck\Url;
use Zenstruck\Url\SignedUrl;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Builder implements \Stringable
{
    private Url $url;
    private string $secret;
    private ?\DateTimeInterface $expiresAt = null;
    private ?string $singleUseToken = null;

    public function __construct(Url $url, string $secret)
    {
        $this->url = $url;
        $this->secret = $secret;
    }

    public function __toString(): string
    {
        return $this->create();
    }

    /**
     * Set an expiry for the signed url.
     *
     * @param \DateTimeInterface|string|int $when \DateTimeInterface: the exact time the link should expire
     *                                            string: used to construct a datetime object (ie "+1 hour")
     *                                            int: # of seconds until the link expires
     */
    public function expires($when): self
    {
        if (\is_numeric($when)) {
            $when = \DateTime::createFromFormat('U', \time() + $when);
        }

        if (\is_string($when)) {
            $when = new \DateTime($when);
        }

        if (!$when instanceof \DateTimeInterface) {
            throw new \InvalidArgumentException(\sprintf('%s is not a valid expires at.', get_debug_type($when)));
        }

        $this->expiresAt = $when;

        return $this;
    }

    /**
     * Make the signed url "single-use".
     *
     * @param string $token This value MUST change once the URL is considered "used"
     */
    public function singleUse(string $token): self
    {
        $this->singleUseToken = $token;

        return $this;
    }

    public function create(): SignedUrl
    {
        return SignedUrl::create($this->url, $this->secret, $this->expiresAt, $this->singleUseToken);
    }
}
