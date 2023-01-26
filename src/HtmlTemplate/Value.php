<?php

declare(strict_types=1);

namespace Atk4\Ui\HtmlTemplate;

use Atk4\Core\WarnDynamicPropertyTrait;
use Atk4\Ui\Exception;

class Value
{
    use WarnDynamicPropertyTrait;

    private string $value = '';

    private bool $isEncoded = false;

    private function encodeValueToHtml(string $value): string
    {
        return htmlspecialchars($value, \ENT_HTML5 | \ENT_QUOTES | \ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * @return $this
     */
    public function set(string $value): self
    {
        if (\PHP_MAJOR_VERSION === 7 || (\PHP_MAJOR_VERSION === 8 && \PHP_MAJOR_VERSION <= 1) // @phpstan-ignore-line
                ? !preg_match('~~u', $value) // much faster in PHP 8.1 and lower
                : !mb_check_encoding($value, 'UTF-8')
        ) {
            throw new Exception('Value is not valid UTF-8');
        }

        $this->isEncoded = false;
        $this->value = $value;

        return $this;
    }

    /**
     * @return $this
     */
    public function dangerouslySetHtml(string $value): self
    {
        $this->set($value);
        $this->isEncoded = true;

        return $this;
    }

    public function isEncoded(): bool
    {
        return $this->isEncoded;
    }

    public function getUnencoded(): string
    {
        if ($this->isEncoded) {
            throw new Exception('Unencoded value is not available');
        }

        return $this->value;
    }

    public function getHtml(): string
    {
        return $this->isEncoded
            ? $this->value
            : $this->encodeValueToHtml($this->value);
    }
}
