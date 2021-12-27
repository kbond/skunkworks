<?php

namespace Zenstruck;

use Zenstruck\Url\Stringable;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Mailto implements \Stringable
{
    use Stringable;

    private Url $url;

    private function __construct(?string $value = null)
    {
        $url = Url::new($value);

        $this->url = $url
            ->withScheme('mailto')
            ->withPath($url->path()->trim())
            ->withoutHost()
            ->withoutPort()
            ->withoutUser()
            ->withoutFragment()
            ->withOnlyQueryParams('subject', 'body', 'cc', 'bcc')
        ;
    }

    /**
     * @param string|self|null $value
     */
    public static function new($value = null): self
    {
        return $value instanceof self ? $value : new self(Url::new($value));
    }

    public function to(): array
    {
        return \array_values(\array_filter(\array_map('trim', $this->url->path()->segments(','))));
    }

    public function cc(): array
    {
        return \array_values(\array_filter(\array_map('trim', \explode(',', $this->url->query()->get('cc')))));
    }

    public function bcc(): array
    {
        return \array_values(\array_filter(\array_map('trim', \explode(',', $this->url->query()->get('bcc')))));
    }

    public function subject(): ?string
    {
        return $this->url->query()->get('subject');
    }

    public function body(): ?string
    {
        return $this->url->query()->get('body');
    }

    public function withTo(string ...$to): self
    {
        $mailto = clone $this;
        $mailto->url = $this->url->withPath(\implode(',', $to));

        return $mailto;
    }

    public function addTo(string $email, ?string $name = null): self
    {
        return $this->withTo(...\array_merge($this->to(), [self::createEmail($email, $name)]));
    }

    public function withoutTo(): self
    {
        $mailto = clone $this;
        $mailto->url = $this->url->withoutPath();

        return $mailto;
    }

    public function withSubject(string $subject): self
    {
        $mailto = clone $this;
        $mailto->url = $this->url->withQueryParam('subject', $subject);

        return $mailto;
    }

    public function withoutSubject(): self
    {
        $mailto = clone $this;
        $mailto->url = $this->url->withoutQueryParams('subject');

        return $mailto;
    }

    public function withBody(string $body): self
    {
        $mailto = clone $this;
        $mailto->url = $this->url->withQueryParam('body', $body);

        return $mailto;
    }

    public function withoutBody(): self
    {
        $mailto = clone $this;
        $mailto->url = $this->url->withoutQueryParams('body');

        return $mailto;
    }

    public function withCc(string ...$cc): self
    {
        $mailto = clone $this;

        if (empty($cc)) {
            $mailto->url = $this->url->withoutQueryParams('cc');

            return $mailto;
        }

        $mailto->url = $this->url->withQueryParam('cc', \implode(',', $cc));

        return $mailto;
    }

    public function addCc(string $email, ?string $name = null): self
    {
        return $this->withCc(...\array_merge($this->cc(), [self::createEmail($email, $name)]));
    }

    public function withoutCc(): self
    {
        return $this->withCc();
    }

    public function withBcc(string ...$bcc): self
    {
        $mailto = clone $this;

        if (empty($bcc)) {
            $mailto->url = $this->url->withoutQueryParams('bcc');

            return $mailto;
        }

        $mailto->url = $this->url->withQueryParam('bcc', \implode(',', $bcc));

        return $mailto;
    }

    public function addBcc(string $email, ?string $name = null): self
    {
        return $this->withBcc(...\array_merge($this->bcc(), [self::createEmail($email, $name)]));
    }

    public function withoutBcc(): self
    {
        return $this->withBcc();
    }

    protected function generateString(): string
    {
        return $this->url;
    }

    private static function createEmail(string $email, ?string $name = null): string
    {
        return $name ? "{$name} <{$email}>" : $email;
    }
}
