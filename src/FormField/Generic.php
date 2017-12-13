<?php

namespace atk4\ui\FormField;

use atk4\ui\Form;
use atk4\ui\View;

/**
 * Provides generic functionality for a form field.
 */
class Generic extends View
{
    /**
     * @var Form - to which this field belongs
     */
    public $form;

    /**
     * @var \atk4\data\Field - points to model field
     */
    public $field;

    public function init()
    {
        parent::init();

        if ($this->form && $this->field) {
            if (isset($this->form->fields[$this->field->short_name])) {
                throw new \atk4\ui\Exception(['Form already has a field with the same name', 'name' => $this->field->short_name]);
            }
            $this->form->fields[$this->field->short_name] = $this;
        }
    }

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
        return isset($this->field) ? $this->app->ui_persistence->typecastSaveField($this->field, $this->field->get()) : ($this->content ?: '');
    }

    // @todo Maybe don't need this method at all, because View->set() is doing almost the same
    public function set($value = null, $junk = null)
    {
        $this->content = $value;
    }

    /**
     * It only makes sense to have "name" property inside a field if
     * it was used inside a form.
     */
    public function renderView()
    {
        if ($this->form) {
            $this->template->trySet('name', $this->short_name);
        }

        parent::renderView();
    }
}
