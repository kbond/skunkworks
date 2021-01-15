<?php

namespace Zenstruck\Filesystem\Bridge\HttpFoundation;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Filesystem\Feature\AccessRealFile;
use Zenstruck\Filesystem\Node\File;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ResponseFactory
{
    public static function create(File $file, int $status = 200, array $headers = []): Response
    {
        if ($file->supports(AccessRealFile::class)) {
            return (new BinaryFileResponse($file->real(), $status, $headers, true, null, false, false))
                ->setLastModified($file->lastModified())
            ;
        }

        return new StreamedFileResponse($file, $status, $headers);
    }

    public static function inline(File $file, int $status = 200, array $headers = []): Response
    {
        return self::create($file, $status, \array_merge($headers, ['Content-Disposition' => HeaderUtils::makeDisposition(HeaderUtils::DISPOSITION_INLINE, $file->filename())]));
    }

    public static function attachment(File $file, int $status = 200, array $headers = []): Response
    {
        return self::create($file, $status, \array_merge($headers, ['Content-Disposition' => HeaderUtils::makeDisposition(HeaderUtils::DISPOSITION_ATTACHMENT, $file->filename())]));
    }
}
