<?php

declare(strict_types=1);

namespace Atk4\Ui\HtmlTemplate;

use Atk4\Ui\Exception;

class Value
{
    /** @var string */
    private $value = '';

    /** @var bool */
    private $isEncoded = false;

    private function encodeValueToHtml(string $value): string
    {
        return htmlspecialchars($value, \ENT_NOQUOTES | \ENT_HTML5, 'UTF-8');
    }

    /**
     * @return $this
     */
    public function set(string $value): self
    {
        if (!preg_match('~~u', $value)) {
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
