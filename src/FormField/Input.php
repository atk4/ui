<?php

namespace atk4\ui\FormField;

use \atk4\ui\Form;
use \atk4\ui\View;
use \atk4\ui\Icon;
use \atk4\ui\Label;

/**
 * Input element for a form field
 */
class Input extends Generic {

    public $ui = 'input';

    public $inputType = 'text';

    public $placeholder = '';

    public $defaultTemplate = 'formfield/input.html';

    public $icon = null;

    /**
     * Specify left / right. If you use "true" will default to the right side.
     */
    public $loading = null;

    /**
     * Set to a text
     */
    public $label = null;

    public $rightLabel = null;

    /**
     * returns <input .../> tag
     */
    function getInput()
    {
        return '<input type="'.$this->inputType.'" placeholder="'.$this->placeholder.'"/>';
    }

    /**
     * Used only from renderView()
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

    function renderView()
    {

        // TODO: I don't think we need the loading state at all.
        if ($this->loading) {
            if (!$this->icon) {
                $this->icon = 'search'; // does not matter, but since 
            }

            $this->addClass('loading');

            if($this->loading === 'left') {
                $this->addClass('left');
            }
        }

        if ($this->icon && !is_object($this->icon)) {
            $this->icon = $this->add(new Icon($this->icon), 'AfterInput');
            $this->addClass('icon');
        }

        if ($this->label) {
            $this->label = $this->prepareRenderLabel($this->label, 'BeforeInput');
        }

        if ($this->rightLabel) {
            $this->rightLabel = $this->prepareRenderLabel($this->rightLabel, 'AfterInput');
            $this->addClass('right');
        }

        if ($this->label || $this->rightLabel) {
            $this->addClass('labeled');
        }

        $this->template->setHTML('Input', $this->getInput());

        parent::renderView();

    }
}
