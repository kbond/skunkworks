<?php

namespace Zenstruck\Filesystem\Node;

use Symfony\Component\Mime\MimeTypes;
use Zenstruck\Filesystem;
use Zenstruck\Filesystem\Exception\RuntimeException;
use Zenstruck\Filesystem\Exception\UnknownProperty;
use Zenstruck\Filesystem\Exception\UnsupportedFeature;
use Zenstruck\Filesystem\Node;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class File extends Node
{
    private ?int $size = null;
    private ?\DateTimeImmutable $lastModified = null;
    private ?string $mimeType = null;
    private ?string $contents = null;

    /**
     * @see Filesystem::write()
     */
    public static function create(Filesystem $filesystem, string $path, $value): self
    {
        $filesystem->write($path, $value);

        return $filesystem->file($path);
    }

    public function filename(): string
    {
        return \pathinfo($this->path(), \PATHINFO_BASENAME);
    }

    public function extension(): string
    {
        return \pathinfo($this->path(), \PATHINFO_EXTENSION);
    }

    /**
     * @throws UnknownProperty If unable to access the "size" property
     */
    public function size(): int
    {
        try {
            return $this->size ??= $this->adapter->size($this->path());
        } catch (\Throwable $e) {
            throw new UnknownProperty("Unable to access the \"size\" property for \"{$this->path()}\".", $e);
        }
    }

    /**
     * @throws UnknownProperty If unable to access the "modified at" property
     */
    public function lastModified(): \DateTimeImmutable
    {
        try {
            return $this->lastModified ??= \DateTimeImmutable::createFromFormat('U', $this->adapter->modifiedAt($this->path()))
                // timestamp is always in UTC so convert to current system timezone
                ->setTimezone(new \DateTimeZone(\date_default_timezone_get()))
            ;
        } catch (\Throwable $e) {
            throw new UnknownProperty("Unable to access the \"last modified\" property for \"{$this->path()}\".", $e);
        }
    }

    /**
     * @throws UnknownProperty If unable to access the "mime type" property
     */
    public function mimeType(): string
    {
        try {
            $this->mimeType ??= $this->adapter->mimeType($this->path());
        } catch (\Throwable $e) {
            // attempt to get mime type from file extension
            if (null === $this->mimeType = MimeTypes::getDefault()->getMimeTypes($this->extension())[0]) {
                throw new UnknownProperty("Unable to access the \"mime type\" property for \"{$this->path()}\".", $e);
            }
        }

        return $this->mimeType;
    }

    /**
     * @throws RuntimeException If unable to access file contents
     */
    public function contents(): string
    {
        try {
            return $this->contents ??= $this->adapter->contents($this->path());
        } catch (\Throwable $e) {
            throw RuntimeException::wrap($e, 'Unable to access contents for "%s".', $this->path());
        }
    }

    /**
     * @return resource
     *
     * @throws RuntimeException If unable to read file
     */
    public function read()
    {
        try {
            return $this->adapter->read($this->path());
        } catch (\Throwable $e) {
            throw RuntimeException::wrap($e, 'Unable to read "%s".', $this->path());
        }
    }

    /**
     * @throws UnsupportedFeature If adapter does not support accessing a real file
     * @throws RuntimeException   If unable to access real file
     */
    public function real(): \SplFileInfo
    {
        try {
            return $this->adapter->realFile($this->path());
        } catch (UnsupportedFeature $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw RuntimeException::wrap($e, 'Unable to access real file "%s".', $this->path());
        }
    }
}
