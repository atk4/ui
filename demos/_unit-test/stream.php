<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\App;
use Atk4\Ui\Exception;
use Psr\Http\Message\StreamInterface;

/** @var App $app */
require_once __DIR__ . '/../init-app.php';

$hugePseudoStreamClass = AnonymousClassNameCache::get_class(fn () => new class(static fn (int $pos) => '', -1) implements StreamInterface {
    /** @var \Closure(int): string */
    private \Closure $fx;

    private int $size;

    private ?int $pos = 0;

    private string $buffer = '';

    /**
     * @param \Closure(int): string $fx
     */
    public function __construct(\Closure $fx, int $size)
    {
        $this->fx = $fx;
        $this->size = $size;
    }

    /**
     * @return never
     */
    public function throwNotSupported(): void
    {
        throw new Exception('Not implemented/supported');
    }

    #[\Override]
    public function __toString(): string
    {
        $this->throwNotSupported();
    }

    #[\Override]
    public function close(): void
    {
        $this->pos = null;
        $this->buffer = '';
    }

    #[\Override]
    public function detach()
    {
        $this->close();

        return null;
    }

    #[\Override]
    public function getSize(): int
    {
        return $this->size;
    }

    #[\Override]
    public function tell(): int
    {
        return $this->pos;
    }

    #[\Override]
    public function eof(): bool
    {
        return $this->pos === $this->size;
    }

    #[\Override]
    public function isSeekable(): bool
    {
        return false;
    }

    #[\Override]
    public function seek($offset, $whence = \SEEK_SET): void
    {
        $this->throwNotSupported();
    }

    #[\Override]
    public function rewind(): void
    {
        $this->seek(0);
    }

    #[\Override]
    public function isWritable(): bool
    {
        return false;
    }

    #[\Override]
    public function write($string): int
    {
        $this->throwNotSupported();
    }

    #[\Override]
    public function isReadable(): bool
    {
        return true;
    }

    #[\Override]
    public function read($length): string
    {
        if ($this->pos + $length > $this->size) {
            $length = $this->size - $this->pos;
        }

        while (strlen($this->buffer) < $length) {
            $this->buffer .= ($this->fx)($this->pos + strlen($this->buffer));
        }

        $res = substr($this->buffer, 0, $length);
        $this->pos += $length;
        $this->buffer = substr($this->buffer, $length);

        return $res;
    }

    #[\Override]
    public function getContents(): string
    {
        $this->throwNotSupported();
    }

    #[\Override]
    public function getMetadata($key = null)
    {
        $this->throwNotSupported();
    }
});

$sizeBytes = (int) $app->getRequestQueryParam('size_mb') * 1024 * 1024;

$stream = new $hugePseudoStreamClass(static function (int $pos) {
    return "\n\0" . str_repeat($pos . ',', 1024);
}, $sizeBytes);

$app->setResponseHeader('Content-Type', 'application/octet-stream');
$app->setResponseHeader('Content-Length', (string) $sizeBytes);
$app->setResponseHeader('Content-Disposition', 'attachment; filename="test.bin"');
$app->terminate($stream);
