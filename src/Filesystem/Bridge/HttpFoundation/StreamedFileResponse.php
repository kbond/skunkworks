<?php

namespace Zenstruck\Filesystem\Bridge\HttpFoundation;

use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Zenstruck\Filesystem\Node\File;
use Zenstruck\Filesystem\Util\ResourceWrapper;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class StreamedFileResponse extends StreamedResponse
{
    public function __construct(File $file, int $status = 200, array $headers = [])
    {
        parent::__construct(function() use ($file) {
            ResourceWrapper::inOutput()->write($file->read());
        }, $status, $headers);

        if (!$this->headers->has('Last-Modified')) {
            $this->setLastModified($file->lastModified());
        }

        if (!$this->headers->has('Content-Type')) {
            $this->headers->set('Content-Type', $file->mimeType());
        }
    }

    public static function inline(File $file, int $status = 200, array $headers = []): Response
    {
        return new self($file, $status, \array_merge($headers, ['Content-Disposition' => HeaderUtils::makeDisposition(HeaderUtils::DISPOSITION_INLINE, $file->filename())]));
    }

    public static function attachment(File $file, int $status = 200, array $headers = []): Response
    {
        return new self($file, $status, \array_merge($headers, ['Content-Disposition' => HeaderUtils::makeDisposition(HeaderUtils::DISPOSITION_ATTACHMENT, $file->filename())]));
    }
}
