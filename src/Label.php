<?php

declare(strict_types=1);

namespace Atk4\Ui;

class Label extends View
{
    public $ui = 'label';

    /**
     * Add icon before label. If 'string' or seed is specified, it will
     * be converted to object by init().
     *
     * @var View|array|string
     */
    public $icon;

    /**
     * Icon to the right of the label.
     *
     * @var View|array|string
     */
    public $iconRight;

    /** @var string|false|null Add "Detail" to label. */
    public $detail;

    /**
     * Image to the left of the label. Cannot be used with label. If string
     * is set, will be used as Image source. Can also contain seed or object.
     *
     * @var View|array|string
     */
    public $image;

    /**
     * Image to the right of the label.
     *
     * @var View|array|string
     */
    public $imageRight;

    public $defaultTemplate = 'label.html';

    protected function renderView(): void
    {
        if ($this->icon) {
            $this->icon = Icon::addTo($this, [$this->icon], ['BeforeContent']);
        }

        if ($this->image) {
            $this->image = Image::addTo($this, [$this->image], ['BeforeContent']);
            $this->addClass('image');
        }

        if ($this->detail) {
            $this->detail = View::addTo($this, [$this->detail], ['AfterContent'])->addClass('detail');
        }

        if ($this->iconRight) {
            $this->iconRight = Icon::addTo($this, [$this->iconRight], ['AfterContent']);
        }

        if ($this->imageRight) {
            $this->imageRight = Image::addTo($this, [$this->imageRight], ['AfterContent']);
            $this->addClass('image');
        }

        parent::renderView();
    }
}
