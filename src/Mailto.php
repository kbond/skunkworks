<?php

namespace Zenstruck\Utilities;

use function Zenstruck\Utilities\Functions\null_trim;

/**
 * @experimental
 *
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Mailto implements \Stringable
{
    /** @var Url */
    private $dsn;

    public function __construct(?string $value = null)
    {
        $dsn = (new Url($value));

        $this->dsn = $dsn
            ->withScheme('mailto')
            ->withPath($dsn->path()->trim())
            ->withoutHost()
            ->withoutPort()
            ->withoutUser()
            ->withoutFragment()
            ->withOnlyQueryParams('subject', 'body', 'cc', 'bcc')
        ;
    }

    public function __toString(): string
    {
        return (string) $this->dsn;
    }

    public function to(): array
    {
        return \array_filter(\array_map('trim', $this->dsn->path()->segments(',')));
    }

    public function cc(): array
    {
        return \array_filter(\array_map('trim', \explode(',', $this->dsn->query()->get('cc'))));
    }

    public function bcc(): array
    {
        return \array_filter(\array_map('trim', \explode(',', $this->dsn->query()->get('bcc'))));
    }

    public function subject(): ?string
    {
        return $this->dsn->query()->get('subject');
    }

    public function body(): ?string
    {
        return $this->dsn->query()->get('body');
    }

    public function withTo(string ...$to): self
    {
        $mailto = clone $this;
        $mailto->dsn = $this->dsn->withPath(null_trim(\implode(',', $to)));

        return $mailto;
    }

    public function addTo(string $email, ?string $name = null): self
    {
        return $this->withTo(...\array_merge($this->to(), [self::createEmail($email, $name)]));
    }

    public function withoutTo(): self
    {
        $mailto = clone $this;
        $mailto->dsn = $this->dsn->withoutPath();

        return $mailto;
    }

    public function withSubject(string $subject): self
    {
        $mailto = clone $this;
        $mailto->dsn = $this->dsn->withQueryParam('subject', $subject);

        return $mailto;
    }

    public function withoutSubject(): self
    {
        $mailto = clone $this;
        $mailto->dsn = $this->dsn->withoutQueryParams('subject');

        return $mailto;
    }

    public function withBody(string $body): self
    {
        $mailto = clone $this;
        $mailto->dsn = $this->dsn->withQueryParam('body', $body);

        return $mailto;
    }

    public function withoutBody(): self
    {
        $mailto = clone $this;
        $mailto->dsn = $this->dsn->withoutQueryParams('body');

        return $mailto;
    }

    public function withCc(string ...$cc): self
    {
        $mailto = clone $this;

        if (empty($cc)) {
            $mailto->dsn = $this->dsn->withoutQueryParams('cc');

            return $mailto;
        }

        $mailto->dsn = $this->dsn->withQueryParam('cc', null_trim(\implode(',', $cc)));

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
            $mailto->dsn = $this->dsn->withoutQueryParams('bcc');

            return $mailto;
        }

        $mailto->dsn = $this->dsn->withQueryParam('bcc', null_trim(\implode(',', $bcc)));

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

    private static function createEmail(string $email, ?string $name = null): string
    {
        return $name ? "{$name} <{$email}>" : $email;
    }
}
