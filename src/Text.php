<?php

// vim:ts=4:sw=4:et:fdm=marker:fdl=0

namespace atk4\ui;

/**
 * Simple text block view.
 */
class Text extends View
{
    public $defaultTemplate = false;

    public function render($force_echo = true)
    {
        return $this->content;
    }

    public function getHTML()
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
        $this->content .= isset($this->app)
            ? $this->app->getTag('p', $text)
            : '<p>'.htmlspecialchars($text).'</p>';

        return $this;
    }

    /**
     * Adds some HTML code.
     *
     * @param $html
     *
     * @return $this
     */
    public function addHTML($html)
    {
        $this->content .= $html;

        return $this;
    }
}
