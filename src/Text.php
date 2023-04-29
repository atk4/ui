<?php

declare(strict_types=1);

namespace Atk4\Ui;

/**
 * Simple text block view.
 */
class Text extends View
{
    public $defaultTemplate;

    public function render(): string
    {
        return $this->content;
    }

    public function getHtml()
    {
        return $this->content;
    }

    /**
     * Adds HTML paragraph.
     *
     * @param string $text
     *
     * @return $this
     */
    public function addParagraph($text)
    {
        $this->content .= $this->getApp()->getTag('p', [], $text);

        return $this;
    }

    /**
     * Adds some HTML code.
     *
     * @return $this
     */
    public function addHtml(string $html)
    {
        $this->content .= $html;

        return $this;
    }
}
