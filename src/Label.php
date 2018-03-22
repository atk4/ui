<?php

namespace atk4\ui;

class Label extends View
{
    public $ui = 'label';

    /**
     * Add icon before label. If 'string' or seed is specified, it will
     * be converted to object by init().
     *
     * @var View|array|string
     */
    public $icon = null;

    /**
     * Icon to the right of the label.
     *
     * @see $icon
     *
     * @var View|array|string
     */
    public $iconRight = null;

    /**
     * Add "Detail" to label.
     *
     * @var string|null|false
     */
    public $detail = null;

    /**
     * Image to the left of the label. Cannot be used with label. If string
     * is set, will be used as Image source. Can also contain seed or object.
     *
     * @var View|array|string
     */
    public $image = null;

    /**
     * Image to the right of the label.
     *
     * @see $image
     *
     * @var View|array|string
     */
    public $imageRight = null;

    public $defaultTemplate = 'label.html';

    public function renderView()
    {
        if ($this->icon) {
            $this->icon = $this->add(new Icon($this->icon), 'BeforeContent');
        }

        if ($this->image) {
            $this->image = $this->add(new Image($this->image), 'BeforeContent');
            $this->addClass('image');
        }

        if (isset($this->detail)) {
            $this->detail = $this->add(new View($this->detail), 'AfterContent')->addClass('detail');
        }

        if ($this->iconRight) {
            $this->iconRight = $this->add(new Icon($this->iconRight), 'AfterContent');
        }

        if ($this->imageRight) {
            $this->imageRight = $this->add(new Image($this->imageRight), 'AfterContent');
            $this->addClass('image');
        }

        return parent::renderView();
    }
}
