<?php

declare(strict_types=1);

namespace Atk4\Ui;

/**
 * Simple text block view.
 */
class Text extends View
{
    public $defaultTemplate = false;

    public function render($forceReturn = true): string
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
        $this->content .= $this->issetApp()
            ? $this->getApp()->getTag('p', $text)
            : '<p>' . htmlspecialchars($text) . '</p>';

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
