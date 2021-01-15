<?php

namespace Zenstruck\Filesystem\Bridge\HttpFoundation;

use Symfony\Component\HttpFoundation\StreamedResponse;
use Zenstruck\Filesystem\Node\File;
use Zenstruck\Filesystem\Util\ResourceWrapper;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class StreamedFileResponse extends StreamedResponse
{
    /**
     * @internal
     *
     * @see ResponseFactory
     */
    public function __construct(File $file, int $status = 200, array $headers = [])
    {
        parent::__construct(function() use ($file) {
            ResourceWrapper::wrap($file->read())->copyTo(ResourceWrapper::open('php://output', 'wb'));
        }, $status, $headers);

        if (!$this->headers->has('Last-Modified')) {
            $this->setLastModified($file->lastModified());
        }

        if (!$this->headers->has('Content-Type')) {
            $this->headers->set('Content-Type', $file->mimeType());
        }
    }
}
