<?php

namespace Zenstruck\Filesystem\Tests\Multi;

use Symfony\Contracts\Service\ServiceProviderInterface;
use Zenstruck\Filesystem\MultiFilesystem;
use Zenstruck\Filesystem\Tests\MultiFilesystemTest;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ServiceProviderMultiFilesystemTest extends MultiFilesystemTest
{
    protected function createForArray(array $filesystems): MultiFilesystem
    {
        return new MultiFilesystem(
            new class($filesystems) implements ServiceProviderInterface {
                private array $filesystems;

                public function __construct(array $filesystems)
                {
                    $this->filesystems = $filesystems;
                }

                public function get($id)
                {
                    return $this->filesystems[$id];
                }

                public function has($id)
                {
                    return \array_key_exists($id, $this->filesystems);
                }

                public function getProvidedServices(): array
                {
                    return $this->filesystems;
                }
            }
        );
    }
}
