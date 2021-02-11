<?php

namespace Zenstruck\Filesystem\Feature;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 */
interface All extends AccessUrl, DeleteDirectory, MoveDirectory, DeleteFile, MoveFile, ReadDirectory, CopyDirectory, CreateDirectory, CopyFile, WriteFile, AccessRealFile, AccessRealDirectory, FileChecksum
{
}
