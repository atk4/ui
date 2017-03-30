<?php

namespace atk4\ui;

/**
 * Component implementing UI Button.
 */
class Button extends View
{
    public $defaultTemplate = 'button.html';

    public $ui = 'button';

    /**
     * Icon that will appear on the button (left).
     *
     * @var string|array|Icon
     */
    public $icon = null;

    /**
     * Additional icon that can appear on the right of the button.
     *
     * @var [type]
     */
    public $iconRight = null;

    /**
     * $icon property will end up a button icon.
     *
     * {@inheritdoc}
     */
    public function renderView()
    {
        if ($this->icon) {
            if (!is_object($this->icon)) {
                $this->icon = new Icon($this->icon);
            }

            $this->add($this->icon, 'LeftIcon');

            if ($this->content) {
                $this->addClass('labeled');
            }

            $this->addClass('icon');
        }

        if ($this->iconRight) {
            if ($this->icon) {
                throw new Exception([
                    'Cannot use icon and iconRight simultaniously',
                    'icon'     => $this->icon,
                    'iconRight'=> $this->iconRight,
                ]);
            }

            if (!is_object($this->iconRight)) {
                $this->iconRight = new Icon($this->iconRight);
            }

            $this->add($this->iconRight, 'RightIcon');

            if ($this->content) {
                $this->addClass('right labeled');
            }

            $this->addClass('icon');
        }

        parent::renderView();
    }

}
