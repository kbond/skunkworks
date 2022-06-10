<?php

namespace Zenstruck\Filesystem\Util;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 */
final class ResourceWrapper
{
    /** @var resource */
    private $resource;

    public function __construct($resource)
    {
        if (!\is_resource($resource)) {
            throw new \InvalidArgumentException(\sprintf('"%s" is not a resource.', get_debug_type($resource)));
        }

        $this->resource = $resource;
    }

    public static function wrap($resource): self
    {
        if ($resource instanceof self) {
            return $resource;
        }

        return new self($resource);
    }

    public static function inMemory(): self
    {
        return self::open('php://memory', 'rw');
    }

    public static function inOutput(): self
    {
        return self::open('php://output', 'rw');
    }

    public static function fromString(string $value): self
    {
        return self::inMemory()->write($value)->rewind();
    }

    public static function tempFile(): self
    {
        if (false === $handle = \tmpfile()) {
            throw new \RuntimeException('Unable to create temporary handle.');
        }

        return new self($handle);
    }

    /**
     * @see \fopen
     */
    public static function open(string $filename, string $mode, bool $useIncludePath = false, $context = null): self
    {
        if (false === $handle = \fopen($filename, $mode, $useIncludePath, $context)) {
            throw new \RuntimeException(\sprintf('Unable to fopen "%s" with mode "%s".', $filename, $mode));
        }

        return new self($handle);
    }

    /**
     * @return resource
     */
    public function get()
    {
        if (!\is_resource($this->resource)) {
            throw new \RuntimeException('Resource is closed.');
        }

        return $this->resource;
    }

    public function contents(): string
    {
        if ($this->metadata('seekable')) {
            $this->rewind();
        }

        if (false === $contents = \stream_get_contents($this->get())) {
            throw new \RuntimeException('Unable to get contents of stream.');
        }

        return $contents;
    }

    /**
     * @see \rewind
     */
    public function rewind(): self
    {
        if (!$this->metadata('seekable')) {
            throw new \RuntimeException('Stream does not support seeking.');
        }

        if (false === \rewind($this->get())) {
            throw new \RuntimeException('Unable to rewind stream.');
        }

        return $this;
    }

    /**
     * @param string|resource $data
     */
    public function write($data): self
    {
        if (\is_string($data)) {
            return $this->writeString($data);
        }

        if (\is_resource($data)) {
            return $this->writeStream($data);
        }

        throw new \InvalidArgumentException(\sprintf('"%s" is not a string or a resource.', get_debug_type($data)));
    }

    /**
     * @return mixed|array<string,mixed>
     */
    public function metadata(?string $key = null)
    {
        $metadata = \stream_get_meta_data($this->get());

        if (!$key) {
            return $metadata;
        }

        if (!\array_key_exists($key, $metadata)) {
            throw new \InvalidArgumentException(\sprintf('Key "%s" not valid.', $key));
        }

        return $metadata[$key];
    }

    public function uri(): string
    {
        return $this->metadata('uri');
    }

    /**
     * @see \fclose
     */
    public function close(): void
    {
        \fclose($this->get());
    }

    private function writeString(string $data): self
    {
        if (false === \fwrite($this->get(), $data)) {
            throw new \RuntimeException('Unable to write to stream.');
        }

        return $this;
    }

    /**
     * @param resource $data
     */
    private function writeStream($data): self
    {
        if (false === \stream_copy_to_stream($data, $this->get())) {
            throw new \RuntimeException('Unable to copy stream.');
        }

        return $this;
    }
}
