<?php

namespace Zenstruck\Filesystem\Adapter;

use Zenstruck\Filesystem\Adapter;
use Zenstruck\Filesystem\Feature\AccessUrl;
use Zenstruck\Url;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class UrlPrefixAdapter extends AdapterWrapper
{
    /** @var Url[] */
    private array $prefixes;

    /**
     * @param string|Url ...$prefixes
     */
    public function __construct(Adapter $adapter, ...$prefixes)
    {
        parent::__construct($adapter);

        if (!\count($prefixes)) {
            throw new \InvalidArgumentException('At least one prefix is required.');
        }

        $this->prefixes = \array_values(\array_map(static fn($prefix) => Url::create($prefix), $prefixes));
    }

    public function url(string $path): Url
    {
        if (1 === \count($this->prefixes)) {
            return $this->prefixes[0]->appendPath($path);
        }

        /**
         * @source https://github.com/symfony/symfony/blob/294195157c3690b869ff6295713a69ff38b3039c/src/Symfony/Component/Asset/UrlPackage.php#L115
         */
        $key = (int) \fmod(\hexdec(\mb_substr(\hash('sha256', $path), 0, 10)), \count($this->prefixes));

        return $this->prefixes[$key]->appendPath($path);
    }

    public function supports(string $feature): bool
    {
        if (AccessUrl::class === $feature) {
            return true;
        }

        return parent::supports($feature);
    }
}
