<?php

namespace Zenstruck\Url;

use Rize\UriTemplate;
use Zenstruck\Url;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Template
{
    private UriTemplate $template;

    /**
     * @see UriTemplate::__construct()
     */
    public function __construct(string $baseUri = '', array $baseParameters = [])
    {
        $this->template = new UriTemplate($baseUri, $baseParameters);
    }

    /**
     * Alias for {@see __construct()}.
     */
    public static function new(string $baseUri = '', array $baseParameters = []): self
    {
        return new self($baseUri, $baseParameters);
    }

    /**
     * @see UriTemplate::expand()
     */
    public function expand(string $uri, array $parameters = []): Url
    {
        return Url::new($this->template->expand($uri, $parameters));
    }

    /**
     * Non "strict" extraction.
     *
     * @see UriTemplate::extract()
     */
    public function extract(string $template, string $uri): array
    {
        return $this->template->extract($template, $uri);
    }

    /**
     * "strict" extraction.
     *
     * @see UriTemplate::extract()
     *
     * @throws \RuntimeException if extraction fails
     */
    public function extractOrFail(string $template, string $uri): array
    {
        if (null === $value = $this->template->extract($template, $uri, true)) {
            throw new \RuntimeException("Unable to extract template \"{$template}\" for uri \"{$uri}\".");
        }

        return $value;
    }
}
