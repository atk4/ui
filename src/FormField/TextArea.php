<?php

namespace atk4\ui\FormField;

use atk4\ui\Form;

/**
 * Input element for a form field.
 */
class TextArea extends Input
{
    public $rows = 2;

    public function init()
    {
        parent::init();
    }

    /**
     * returns <input .../> tag.
     */
    public function getInput()
    {
        return $this->app->getTag('textarea', [
            'name'        => $this->short_name,
            'type'        => $this->inputType,
            'rows'        => $this->rows,
            'placeholder' => $this->placeholder,
            'id'          => $this->id.'_input',
        ], isset($this->field) ? $this->app->ui_persistence->typecastSaveField($this->field, $this->field->get()) : $this->content ?: ''
    );
        //return '<input name="'.$this->short_name.'" type="'.$this->inputType.'" placeholder="'.$this->placeholder.'" id="'.$this->id.'_input"/>';
    }
}
