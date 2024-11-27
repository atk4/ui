<?php

declare(strict_types=1);

namespace Atk4\Ui;

/**
 * Simple text block view.
 */
class Text extends View
{
    public $defaultTemplate;

    public $content = '';

    public function __construct($label = [])
    {
        $defaults = is_array($label) ? $label : [$label];

        if (array_key_exists(0, $defaults)) {
            $defaults[0] = $this->getApp()->encodeHtml($defaults[0]);
        }

        parent::__construct($defaults);
    }

    #[\Override]
    public function renderToHtml(): string
    {
        return $this->content;
    }

    #[\Override]
    public function getHtml(): string
    {
        return $this->content;
    }

    #[\Override]
    public function set($text)
    {
        return parent::set($this->getApp()->encodeHtml($text));
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
    public function dangerouslyAddHtml(string $html)
    {
        $this->content .= $html;

        return $this;
    }
}
