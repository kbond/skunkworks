<?php

namespace Zenstruck\Filesystem\Adapter;

use Zenstruck\Filesystem\Feature\AccessRealFile;
use Zenstruck\Filesystem\TempFile;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class TempFileAdapter extends AdapterWrapper
{
    public function realFile(string $path): \SplFileInfo
    {
        return TempFile::forStream($this->read($path));
    }

    public function supports(string $feature): bool
    {
        if (AccessRealFile::class === $feature) {
            return true;
        }

        return parent::supports($feature);
    }
}
