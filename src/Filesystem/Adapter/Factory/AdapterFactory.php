<?php

namespace Zenstruck\Filesystem\Adapter\Factory;

use League\Flysystem\Ftp\FtpAdapter;
use Zenstruck\Filesystem\Adapter;
use Zenstruck\Filesystem\Adapter\Factory;
use Zenstruck\Filesystem\Exception\UnableToParseDsn;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class AdapterFactory implements Factory
{
    private iterable $factories;
    private static ?array $defaultFactories = null;

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
    private function factories(): iterable
    {
        yield from $this->factories;
        yield from self::defaultFactories();
    }

    private static function defaultFactories(): iterable
    {
        if (self::$defaultFactories) {
            yield from self::$defaultFactories;
        }

        if (\class_exists(FtpAdapter::class)) {
            yield self::$defaultFactories[] = new FlysystemFtpAdapterFactory();
        }

        yield self::$defaultFactories[] = new InMemoryAdapterFactory();
        yield self::$defaultFactories[] = new StreamAdapterFactory();
        yield self::$defaultFactories[] = new UrlPrefixAdapterFactory();
        yield self::$defaultFactories[] = new TempFileAdapterFactory();
    }
}
