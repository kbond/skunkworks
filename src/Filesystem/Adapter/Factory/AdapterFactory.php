<?php

namespace Zenstruck\Filesystem\Adapter\Factory;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Zenstruck\Filesystem\Adapter;
use Zenstruck\Filesystem\Adapter\Factory;
use Zenstruck\Filesystem\Exception\UnableToParseDsn;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class AdapterFactory implements Factory
{
    private iterable $factories;
    private ?array $cachedFactories = null;

    /**
     * @param Factory[] $factories
     */
    public function __construct(iterable $factories = [])
    {
        $this->factories = $factories;
    }

    public function create(\Stringable $dsn): Adapter
    {
        foreach ($this->factories() as $factory) {
            if ($factory instanceof FactoryAware) {
                $factory->setFactory($this);
            }

            try {
                return $factory->create($dsn);
            } catch (UnableToParseDsn $e) {
                continue;
            }
        }

        throw new UnableToParseDsn("Unable to create adapter for DSN \"{$dsn}\".");
    }

    /**
     * @return Factory[]
     */
    private function factories(): array
    {
        if ($this->cachedFactories) {
            return $this->cachedFactories;
        }

        $this->cachedFactories = [];

        foreach ($this->factories as $factory) {
            $this->cachedFactories[] = $factory;
        }

        if (\interface_exists(HttpClientInterface::class)) {
            $this->cachedFactories[] = new HttpClientAdapterFactory();
        }

        $this->cachedFactories[] = new InMemoryAdapterFactory();
        $this->cachedFactories[] = new StreamAdapterFactory();
        $this->cachedFactories[] = new UrlPrefixAdapterFactory();
        $this->cachedFactories[] = new TempFileAdapterFactory();

        return $this->cachedFactories;
    }
}
