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
     * @param bool   $allow_html
     *
     * @return $this
     */
    public function addParagraph($text, $allow_html = false)
    {
        $this->content .= isset($this->app)
            ? $this->app->getTag('p', $text)
            : '<p>'.($allow_html ? $text : htmlspecialchars($text)).'</p>';

        return $this;
    }
}
