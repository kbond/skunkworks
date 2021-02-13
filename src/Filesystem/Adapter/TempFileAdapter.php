<?php

namespace Zenstruck\Filesystem\Adapter;

use Zenstruck\Filesystem\Feature\AccessRealFile;
use Zenstruck\Filesystem\TempFile;
use Zenstruck\Filesystem\Util\ResourceWrapper;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class TempFileAdapter extends AdapterWrapper
{
    public function realFile(string $path): \SplFileInfo
    {
        $resource = ResourceWrapper::wrap($this->read($path));

        try {
            return TempFile::forStream($resource->get());
        } finally {
            $resource->close();
        }
    }

    public function supports(string $feature): bool
    {
        if (AccessRealFile::class === $feature) {
            return true;
        }

        return parent::supports($feature);
    }
}
