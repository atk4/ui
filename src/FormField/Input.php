<?php

namespace atk4\ui\FormField;

use atk4\ui\Form;
use atk4\ui\Icon;
use atk4\ui\Label;
use atk4\ui\Button;

/**
 * Input element for a form field.
 */
class Input extends Generic
{
    public $ui = 'input';

    public $inputType = 'text';

    public $placeholder = '';

    public $defaultTemplate = 'formfield/input.html';

    public $icon = null;

    public $iconLeft = null;

    /**
     * Specify left / right. If you use "true" will default to the right side.
     */
    public $loading = null;

    /**
     * Set to a text.
     */
    public $label = null;

    public $labelRight = null;

    public $action = null;

    public $actionLeft = null;

    /**
     * returns <input .../> tag.
     */
    public function getInput()
    {
        return '<input type="'.$this->inputType.'" placeholder="'.$this->placeholder.'"/>';
    }

    /**
     * Used only from renderView().
     */
    private function prepareRenderLabel($label, $spot)
    {
        if (!is_object($label)) {
            $label = $this->add(new Label(), $spot)
                ->set($label);
        } else {
            $this->add($label, $spot);
        }

        if ($label->ui != 'label') {
            $label->addClass('label');
        }

        return $label;
    }

    public function renderView()
    {

        // TODO: I don't think we need the loading state at all.
        if ($this->loading) {
            if (!$this->icon) {
                $this->icon = 'search'; // does not matter, but since
            }

            $this->addClass('loading');

            if ($this->loading === 'left') {
                $this->addClass('left');
            }
        }

        if ($this->icon && !is_object($this->icon)) {
            $this->icon = $this->add(new Icon($this->icon), 'AfterInput');
            $this->addClass('icon');
        }

        if ($this->iconLeft && !is_object($this->iconLeft)) {
            $this->iconLeft = $this->add(new Icon($this->iconLeft), 'BeforeInput');
            $this->addClass('left icon');
        }


        if ($this->label) {
            $this->label = $this->prepareRenderLabel($this->label, 'BeforeInput');
        }

        if ($this->labelRight) {
            $this->labelRight = $this->prepareRenderLabel($this->labelRight, 'AfterInput');
            $this->addClass('right');
        }

        if ($this->label || $this->labelRight) {
            $this->addClass('labeled');
        }

        if ($this->action) {
            if (!is_object($this->action)) {
                $this->action = new Button($this->action);
            }
            $this->add($this->action, 'AfterInput');
            $this->addClass('action');
        }

        if ($this->actionLeft) {
            if (!is_object($this->actionLeft)) {
                $this->actionLeft = new Button($this->actionLeft);
            }
            $this->add($this->actionLeft, 'BeforeInput');
            $this->addClass('left action');
        }

        $this->template->setHTML('Input', $this->getInput());

        parent::renderView();
    }
}
