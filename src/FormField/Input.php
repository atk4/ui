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
     * the field and you can fit currency symbol "$" inside a label for example.
     * For Input field label will appear on the left.
     *
     * @var string|object
     */
    public $label = null;

    /**
     * Set label that will appear to the right of the input field.
     *
     * @var string|object
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
        return isset($this->field) ? $this->app->ui_persistence->typecastSaveField($this->field, $this->field->get()) : (isset($this->content) ? $this->content : '');
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
     *
     * @param string|object $label Label class or object
     * @param string        $spot  Template spot
     *
     * @return Label
     */
    protected function prepareRenderLabel($label, $spot)
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

    /**
     * Used only from renderView().
     *
     * @param string|object $button Button class or object
     * @param string        $spot   Template spot
     *
     * @return Button
     */
    protected function prepareRenderButton($button, $spot)
    {
        if (!is_object($button)) {
            $button = new Button($button);
        }
        if (!$button->_initialized) {
            $this->add($button, $spot);
            $this->addClass('action');
        }

        return $button;
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

        // icons
        if ($this->icon && !is_object($this->icon)) {
            $this->icon = $this->add(new Icon($this->icon), 'AfterInput');
            $this->addClass('icon');
        }

        if ($this->iconLeft && !is_object($this->iconLeft)) {
            $this->iconLeft = $this->add(new Icon($this->iconLeft), 'BeforeInput');
            $this->addClass('left icon');
        }

        // labels
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

        // width
        if ($this->width) {
            $this->addClass($this->width.' wide');
        }

        // actions
        if ($this->action) {
            $this->action = $this->prepareRenderButton($this->action, 'AfterInput');
        }

        if ($this->actionLeft) {
            $this->actionLeft = $this->prepareRenderButton($this->actionLeft, 'BeforeInput');
            $this->addClass('left');
        }

        // set template
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
