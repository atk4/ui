<?php

namespace atk4\ui\FormField;

use atk4\ui\Button;
use atk4\ui\Form;
use atk4\ui\Icon;
use atk4\ui\Label;

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
     * Some fields also support $label. For Input the label can be placed to the left or to the right of
     * the field and you can fit "$" inside a label. Input label will appear on the left.
     */
    public $label = null;

    /**
     * Set label that will appear to the right of the input field
     */
    public $labelRight = null;

    public $action = null;

    public $actionLeft = null;

    /**
     * Specify width for semantic UI grid. For "four wide" use 'four'.
     */
    public $width = null;

    /**
     * Method similar to View::js() however will adjust selector
     * to target the "input" element.
     *
     * $field->jsInput(true)->val(123);
     */
    public function jsInput($when = null, $action = null)
    {
        return $this->js($when, $action, '#'.$this->id.'_input');
    }

    /**
     * Returns presentable value to be inserted into input tag.
     */
    public function getValue()
    {
        return isset($this->field) ? $this->app->ui_persistence->typecastSaveField($this->field, $this->field->get()) : $this->content ?: '';
    }

    /**
     * returns <input .../> tag.
     */
    public function getInput()
    {
        return $this->app->getTag('input', [
            'name'        => $this->short_name,
            'type'        => $this->inputType,
            'placeholder' => $this->placeholder,
            'id'          => $this->id.'_input',
            'value'       => $this->getValue(),
        ]);
        //return '<input name="'.$this->short_name.'" type="'.$this->inputType.'" placeholder="'.$this->placeholder.'" id="'.$this->id.'_input"/>';
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

        if ($this->width) {
            $this->addClass($this->width.' wide');
        }

        if ($this->action) {
            if (!is_object($this->action)) {
                $this->action = new Button($this->action);
            }
            if (!$this->action->_initialized) {
                $this->add($this->action, 'AfterInput');
                $this->addClass('action');
            }
        }

        if ($this->actionLeft) {
            if (!is_object($this->actionLeft)) {
                $this->actionLeft = new Button($this->actionLeft);
            }
            if (!$this->actionLeft->_initialized) {
                $this->add($this->actionLeft, 'BeforeInput');
                $this->addClass('left action');
            }
        }

        $this->template->setHTML('Input', $this->getInput());
        $this->content = null;

        parent::renderView();
    }

    public function addAction($defaults = [])
    {
        if (!is_array($defaults)) {
            $defaults = [$defaults];
        }

        $this->action = $this->add(new Button($defaults), 'AfterInput');

        return $this->action;
    }
}
