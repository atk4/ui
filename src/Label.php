<?php

namespace atk4\ui;

class Label extends View
{
    public $ui = 'label';

    /**
     * Add icon before label.
     */
    public $icon = null;

    /**
     * Icon after label.
     */
    public $iconRight = null;

    /**
     * Add "Detail" to label.
     */
    public $detail = null;

    /**
     * Image to the left of the label.
     */
    public $image = null;

    /**
     * Image to the right of the label.
     */
    public $imageRight = null;

    public $defaultTemplate = 'label.html';

    /**
     * Makes label into a "<a>" element with a link.
     *
     * @param string $url
     *
     * @return $this
     */
    public function link($url)
    {
        $this->element = 'a';
        if (is_string($url)) {
            $this->setAttr('href', $url);

            return $this;
        }
        $this->setAttr('href', $this->app->url($url));

        return $this;
    }

    public function renderView()
    {
        if ($this->icon !== null) {
            $this->icon = $this->add(new Icon($this->icon), 'BeforeContent');
        }

        if ($this->image !== null) {
            $this->image = $this->add(new Image($this->image), 'BeforeContent');
            $this->addClass('image');
        }

        if ($this->detail !== null) {
            $this->detail = $this->add(new View($this->detail), 'AfterContent')->addClass('detail');
        }

        if ($this->iconRight !== null) {
            $this->iconRight = $this->add(new Icon($this->iconRight), 'AfterContent');
        }

        if ($this->imageRight !== null) {
            $this->imageRight = $this->add(new Image($this->imageRight), 'AfterContent');
            $this->addClass('image');
        }

        return parent::renderView();
    }
}
